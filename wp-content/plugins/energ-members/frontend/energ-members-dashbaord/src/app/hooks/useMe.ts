import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { AuthAPI } from "../lib/api";

/* =========================
   Types
========================= */

export type MeResponse = {
  id?: number | string;

  // Identity (read-only in UI)
  email?: string;
  phone?: string;

  // Profile
  firstName?: string;
  lastName?: string;
  jobTitle?: string;
  organization?: string;
  country?: string;
  industry?: string;

  // Communities
  community?: string;
  subCommunities?: string[];

  // Notifications
  notifications?: {
    emailNotifications?: boolean;
    weeklyDigest?: boolean;
    eventReminders?: boolean;
    communityActivity?: boolean;
  };

  // Membership (system-controlled)
  membership?: {
    planName?: string;
    tier?: string;
    status?: string;
    price?: string;
    period?: string;
    description?: string;
  };

  // Security
  hasPassword?: boolean;

  roleLabel?: string;
};

export type UpdateMePayload = Partial<Omit<MeResponse, "membership">> & {
  password?: {
    currentPassword?: string;
    newPassword: string;
  };
};

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
    return value
      .split(",")
      .map((s) => s.trim())
      .filter(Boolean);
  }
  return [];
}

function normalizeMe(raw: any): MeResponse {
  const u = raw?.user ?? raw;
  const meta = u?.meta ?? {};
  const acf = u?.acf ?? {};

  return {
    id: u?.id ?? u?.ID,

    email: u?.email ?? u?.user_email ?? u?.data?.email ?? meta?.email ?? "",
    phone:
      u?.phone ??
      u?.mobile ??
      u?.identifier ??
      meta?.phone ??
      meta?.mobile ??
      "",

    firstName:
      u?.firstName ??
      u?.first_name ??
      meta?.first_name ??
      acf?.first_name ??
      "",
    lastName:
      u?.lastName ??
      u?.last_name ??
      meta?.last_name ??
      acf?.last_name ??
      "",

    jobTitle:
      u?.jobTitle ??
      u?.job_title ??
      meta?.job_title ??
      acf?.job_title ??
      "",
    organization:
      u?.organization ??
      u?.company ??
      meta?.organization ??
      acf?.organization ??
      "",

    country: u?.country ?? meta?.country ?? acf?.country ?? "",
    industry: u?.industry ?? meta?.industry ?? acf?.industry ?? "",

    community:
      u?.community ??
      u?.primaryCommunity ??
      meta?.community ??
      acf?.community ??
      "",
    subCommunities: asStringArray(
      u?.subCommunities ??
        u?.sub_communities ??
        u?.sub_communities_json ??
        meta?.sub_communities ??
        meta?.sub_communities_json ??
        acf?.sub_communities ??
        acf?.sub_communities_json
    ),

    notifications: u?.notifications ?? meta?.notifications ?? acf?.notifications,

    membership: u?.membership ?? meta?.membership ?? acf?.membership,

    hasPassword:
      typeof u?.hasPassword === "boolean"
        ? u.hasPassword
        : typeof meta?.has_password === "boolean"
          ? meta.has_password
          : typeof u?.passwordSet === "boolean"
            ? u.passwordSet
            : undefined,

    roleLabel: u?.roleLabel ?? meta?.roleLabel ?? acf?.roleLabel,
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
    if (status === 401 || status === 403) return null;
    return null;
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
  const revalidateOnFocus = options?.revalidateOnFocus ?? true;

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
    if (cache.promise) {
      setLoading(true);
      const data = await cache.promise;
      setMe(data);
      setError(cache.error);
      setLoading(false);
      return;
    }

    abortRef.current?.abort();
    abortRef.current = new AbortController();

    setLoading(true);
    setError(null);

    cache.promise = (async () => {
      try {
        const data = await fetchMeFromServer(abortRef.current?.signal);
        cache.data = data;
        cache.error = null;
        cache.fetchedAt = Date.now();
        return data;
      } catch (e: any) {
        cache.data = null;
        cache.error = e?.message ?? "Failed to load profile.";
        cache.fetchedAt = Date.now();
        return null;
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
        cache.data = { ...(cache.data ?? {}), ...updated };
        cache.fetchedAt = Date.now();
        safeSet(() => setMe(cache.data));
        return cache.data;
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
      if (!cache.fetchedAt || Date.now() - cache.fetchedAt > ttlMs) refresh();
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
