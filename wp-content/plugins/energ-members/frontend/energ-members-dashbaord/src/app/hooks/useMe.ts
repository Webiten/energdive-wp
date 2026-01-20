import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { AuthAPI } from "../lib/api";

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

  communities?: string[];
  subCommunities?: string[];

  roleLabel?: string;
};

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

function asStringArray(value: any): string[] {
  if (!value) return [];
  if (Array.isArray(value)) return value.filter((x) => typeof x === "string" && x.trim());
  if (typeof value === "string") {
    // supports "a,b,c" and json string
    try {
      const maybe = JSON.parse(value);
      if (Array.isArray(maybe)) return maybe.filter((x) => typeof x === "string" && x.trim());
    } catch {}
    return value.split(",").map((s) => s.trim()).filter(Boolean);
  }
  return [];
}

function normalizeMe(raw: any): MeResponse {
  const u = raw?.user ?? raw;

  return {
    id: u?.id ?? u?.ID,
    email: u?.email ?? u?.user_email ?? u?.data?.email,
    phone: u?.phone ?? u?.mobile ?? "",

    firstName: u?.firstName ?? u?.first_name ?? "",
    lastName: u?.lastName ?? u?.last_name ?? "",

    jobTitle: u?.jobTitle ?? u?.job_title ?? "",
    organization: u?.organization ?? u?.company ?? "",

    country: u?.country ?? "",
    industry: u?.industry ?? "",

    // ✅ these are critical
    communities: asStringArray(u?.communities ?? u?.communities_json ?? u?.community),
    subCommunities: asStringArray(u?.sub_communities ?? u?.sub_communities_json ?? u?.sub_community),
  };
}

async function fetchMeFromServer(): Promise<MeResponse | null> {
  try {
    const raw = await AuthAPI.me();
    return normalizeMe(raw);
  } catch (e: any) {
    const status = e?.data?.status ?? e?.status;
    if (status === 401 || status === 403) return null;
    return null;
  }
}

async function updateMeOnServer(payload: any): Promise<MeResponse> {
  const raw = await AuthAPI.updateMe(payload);
  return normalizeMe(raw);
}

export function useMe(options?: { ttlMs?: number; revalidateOnFocus?: boolean }) {
  const ttlMs = options?.ttlMs ?? 60_000;
  const revalidateOnFocus = options?.revalidateOnFocus ?? true;

  const [me, setMe] = useState<MeResponse | null>(cache.data);
  const [loading, setLoading] = useState<boolean>(!cache.fetchedAt);
  const [error, setError] = useState<string | null>(cache.error);

  const mountedRef = useRef(true);

  useEffect(() => {
    mountedRef.current = true;
    return () => {
      mountedRef.current = false;
    };
  }, []);

  const isStale = useMemo(() => {
    if (!cache.fetchedAt) return true;
    return Date.now() - cache.fetchedAt > ttlMs;
  }, [ttlMs]);

  const safeSetState = useCallback((fn: () => void) => {
    if (!mountedRef.current) return;
    fn();
  }, []);

  const refresh = useCallback(async () => {
    safeSetState(() => {
      setLoading(true);
      setError(null);
    });

    cache.promise = (async () => {
      try {
        const data = await fetchMeFromServer();
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
    safeSetState(() => {
      setMe(data);
      setError(cache.error);
      setLoading(false);
    });
  }, [safeSetState]);

  const updateMe = useCallback(
    async (payload: any) => {
      safeSetState(() => {
        setLoading(true);
        setError(null);
      });

      try {
        const updated = await updateMeOnServer(payload);

        cache.data = { ...(cache.data ?? {}), ...updated };
        cache.error = null;
        cache.fetchedAt = Date.now();

        safeSetState(() => setMe(cache.data));
        return cache.data;
      } catch (e: any) {
        const msg = e?.message ?? "Failed to save.";
        cache.error = msg;
        safeSetState(() => setError(msg));
        throw e;
      } finally {
        safeSetState(() => setLoading(false));
      }
    },
    [safeSetState]
  );

  useEffect(() => {
    if (isStale) refresh();
    else {
      safeSetState(() => {
        setMe(cache.data);
        setError(cache.error);
        setLoading(false);
      });
    }
  }, [isStale, refresh, safeSetState]);

  useEffect(() => {
    if (!revalidateOnFocus) return;

    const onFocus = () => {
      const stale = !cache.fetchedAt || Date.now() - cache.fetchedAt > ttlMs;
      if (stale) refresh();
    };

    window.addEventListener("focus", onFocus);
    return () => window.removeEventListener("focus", onFocus);
  }, [revalidateOnFocus, ttlMs, refresh]);

  return { me, loading, error, refresh, updateMe };
}
