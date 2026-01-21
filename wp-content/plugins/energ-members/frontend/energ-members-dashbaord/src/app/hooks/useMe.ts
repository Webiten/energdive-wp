import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { AuthAPI } from "../lib/api";

/* =========================
   Session Expiry Bus
========================= */

let sessionExpired = false;
const sessionListeners = new Set<() => void>();

export function onSessionExpired(cb: () => void) {
  sessionListeners.add(cb);
  return () => sessionListeners.delete(cb);
}

function triggerSessionExpired() {
  if (sessionExpired) return;
  sessionExpired = true;
  sessionListeners.forEach((cb) => cb());
}

/* =========================
   Types
========================= */

export type MeResponse = {
  id?: number | string;

  email?: string;
  phone?: string;

  firstName?: string;
  lastName?: string;
  jobTitle?: string;
  organization?: string;
  country?: string;
  industry?: string;
  subIndustry?: string;

  community?: string;
  communities?: string[];
  subCommunities?: string[];

  notifications?: {
    emailNotifications?: boolean;
    weeklyDigest?: boolean;
    eventReminders?: boolean;
    communityActivity?: boolean;
  };

  membership?: {
    planName?: string;
    tier?: string;
    status?: string;
    price?: string;
    period?: string;
    description?: string;
  };

  hasPassword?: boolean;
  roleLabel?: string;
};

export type UpdateMePayload = Partial<Omit<MeResponse, "membership">>;

/* =========================
   Cache
========================= */

type Cache = {
  data: MeResponse | null;
  fetchedAt: number;
  promise: Promise<MeResponse | null> | null;
  error: string | null;
};

const cache: Cache = {
  data: null,
  fetchedAt: 0,
  promise: null,
  error: null,
};

/* =========================
   Helpers
========================= */

function asStringArray(value: any): string[] {
  if (!value) return [];
  if (Array.isArray(value)) return value.filter((x) => typeof x === "string");
  if (typeof value === "string") {
    return value.split(",").map((s) => s.trim()).filter(Boolean);
  }
  return [];
}

function normalizeMe(raw: any): MeResponse {
  const u = raw?.user ?? raw ?? {};

  return {
    id: u.id,

    email: u.email ?? "",
    phone: u.phone ?? "",

    firstName: u.first_name ?? "",
    lastName: u.last_name ?? "",
    jobTitle: u.job_title ?? "",
    organization: u.organization ?? "",

    country: u.country ?? "",
    industry: u.industry ?? "",
    subIndustry: u.sub_industry ?? "",

    community: u.community ?? "",
    communities: asStringArray(u.communities ?? u.communities_json),
    subCommunities: asStringArray(u.sub_communities ?? u.sub_communities_json),

    notifications: u.notifications,
    membership: u.membership,

    hasPassword: u.has_password,
    roleLabel: u.role_label,
  };
}

/* =========================
   Server Calls
========================= */

async function fetchMeFromServer(signal?: AbortSignal): Promise<MeResponse | null> {
  try {
    const raw = await AuthAPI.me({ signal });
    return normalizeMe(raw);
  } catch (e: any) {
    const status = e?.status ?? e?.data?.status;

    // 🔁 Try refresh token ONCE
    if (status === 401 || status === 403) {
      try {
        await AuthAPI.refreshToken();
        const retry = await AuthAPI.me({ signal });
        return normalizeMe(retry);
      } catch {
        triggerSessionExpired(); // 🚨 FINAL FAIL → popup
        return cache.data; // ❌ DO NOT WIPE UI
      }
    }

    return cache.data;
  }
}

async function updateMeOnServer(payload: UpdateMePayload): Promise<MeResponse> {
  const raw = await AuthAPI.updateMe(payload);
  return normalizeMe(raw);
}

/* =========================
   Hook
========================= */

export function useMe(options?: { ttlMs?: number; revalidateOnFocus?: boolean }) {
  const ttlMs = options?.ttlMs ?? 60_000;

  // 🔥 IMPORTANT: disable focus auto-refresh by default
  const revalidateOnFocus = options?.revalidateOnFocus ?? false;

  const [me, setMe] = useState<MeResponse | null>(cache.data);
  const [loading, setLoading] = useState<boolean>(!cache.fetchedAt);
  const [error, setError] = useState<string | null>(cache.error);

  const abortRef = useRef<AbortController | null>(null);
  const mountedRef = useRef(true);

  useEffect(() => {
    mountedRef.current = true;
    return () => {
      mountedRef.current = false;
      abortRef.current?.abort();
    };
  }, []);

  const isStale = useMemo(() => {
    if (!cache.fetchedAt) return true;
    return Date.now() - cache.fetchedAt > ttlMs;
  }, [ttlMs]);

  const safeSet = useCallback((fn: () => void) => {
    if (mountedRef.current) fn();
  }, []);

  const refresh = useCallback(async () => {
    if (cache.promise) return;

    abortRef.current?.abort();
    abortRef.current = new AbortController();

    setLoading(true);
    setError(null);

    cache.promise = (async () => {
      try {
        const data = await fetchMeFromServer(abortRef.current?.signal);

        if (data) {
          cache.data = data;
          cache.error = null;
        }

        cache.fetchedAt = Date.now();
        return cache.data;
      } catch (e: any) {
        cache.error = e?.message ?? "Failed to load profile.";
        return cache.data;
      } finally {
        cache.promise = null;
      }
    })();

    const data = await cache.promise;

    safeSet(() => {
      setMe(data);
      setError(cache.error);
      setLoading(false);
    });
  }, [safeSet]);

  const updateMe = useCallback(
    async (payload: UpdateMePayload) => {
      setLoading(true);
      setError(null);

      try {
        const updated = await updateMeOnServer(payload);

        // 🔥 SINGLE SOURCE OF TRUTH
        cache.data = updated;
        cache.fetchedAt = Date.now();

        safeSet(() => setMe(updated));
        return updated;
      } catch (e: any) {
        const msg = e?.message ?? "Failed to save.";
        cache.error = msg;
        safeSet(() => setError(msg));
        throw e;
      } finally {
        safeSet(() => setLoading(false));
      }
    },
    [safeSet]
  );

  useEffect(() => {
    if (isStale) refresh();
    else {
      setMe(cache.data);
      setError(cache.error);
      setLoading(false);
    }
  }, [isStale, refresh]);

  useEffect(() => {
    if (!revalidateOnFocus) return;

    const onFocus = () => {
      if (Date.now() - cache.fetchedAt > ttlMs) refresh();
    };

    window.addEventListener("focus", onFocus);
    return () => window.removeEventListener("focus", onFocus);
  }, [revalidateOnFocus, ttlMs, refresh]);

  return { me, loading, error, refresh, updateMe };
}

/* =========================
   Utils
========================= */

export function clearMeCache() {
  cache.data = null;
  cache.error = null;
  cache.fetchedAt = 0;
  cache.promise = null;
  sessionExpired = false;
}

export function getMeDisplayName(me: MeResponse | null) {
  const first = (me?.firstName ?? "").trim();
  const last = (me?.lastName ?? "").trim();
  const full = `${first} ${last}`.trim();
  if (full) return full;
  if (me?.email) return me.email.split("@")[0];
  return "User";
}

export function getMeInitials(me: MeResponse | null) {
  const first = (me?.firstName ?? "").trim();
  const last = (me?.lastName ?? "").trim();
  if (first || last) return `${first[0] ?? ""}${last[0] ?? ""}`.toUpperCase() || "U";
  if (me?.email) return me.email.slice(0, 2).toUpperCase();
  return "U";
}
