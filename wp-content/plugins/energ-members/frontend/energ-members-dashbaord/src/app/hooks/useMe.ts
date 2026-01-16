import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { AuthAPI } from "../lib/api";

export type MeResponse = {
    id?: number | string;
    email?: string;

    firstName?: string;
    lastName?: string;
    jobTitle?: string;
    organization?: string;

    interests?: string[];

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

    return {
        id: u?.id ?? u?.ID,
        email: u?.email ?? u?.user_email ?? u?.data?.email,

        // ✅ handle snake_case from energ_members table
        firstName: u?.firstName ?? u?.first_name ?? u?.meta?.first_name ?? u?.acf?.first_name ?? "",
        lastName: u?.lastName ?? u?.last_name ?? u?.meta?.last_name ?? u?.acf?.last_name ?? "",

        jobTitle: u?.jobTitle ?? u?.job_title ?? u?.meta?.job_title ?? u?.acf?.job_title ?? "",
        organization: u?.organization ?? u?.company ?? u?.meta?.organization ?? u?.acf?.organization ?? "",

        interests: asStringArray(u?.interests ?? u?.meta?.interests ?? u?.acf?.interests),

        notifications: u?.notifications ?? u?.meta?.notifications ?? u?.acf?.notifications,
        membership: u?.membership ?? u?.meta?.membership ?? u?.acf?.membership,

        roleLabel: u?.roleLabel ?? u?.meta?.roleLabel ?? u?.acf?.roleLabel,
    };
}


async function fetchMeFromServer(signal?: AbortSignal): Promise<MeResponse | null> {
    // AuthAPI.me uses Authorization: Bearer <access_token>
    // We keep signal support for future; current AuthAPI uses fetch directly.
    try {
        const raw = await AuthAPI.me();
        return normalizeMe(raw);
    } catch (e: any) {
        // If token missing/expired → treat as logged out
        const status = e?.data?.status ?? e?.status;
        if (status === 401 || status === 403) return null;
        return null;
    }
}

/**
 * NOTE:
 * You currently do not have a JWT-protected "update me" endpoint shown in routes.
 * So updateMe will be a no-op unless you implement one.
 * For now, we optimistically update cache and let your registration/update flow handle persistence.
 */
async function updateMeOnServer(payload: Partial<MeResponse>): Promise<MeResponse> {
    // If you create a real endpoint later (recommended):
    // const res = await fetch("/wp-json/energ/v1/me", { method:"POST", ... })
    // return normalizeMe(await res.json())

    // For now: just return normalized payload so UI updates.
    return normalizeMe(payload);
}

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

    const safeSetState = useCallback((fn: () => void) => {
        if (!mountedRef.current) return;
        fn();
    }, []);

    const refresh = useCallback(async () => {
        if (cache.promise) {
            safeSetState(() => setLoading(true));
            try {
                const data = await cache.promise;
                safeSetState(() => {
                    setMe(data);
                    setError(cache.error);
                });
            } finally {
                safeSetState(() => setLoading(false));
            }
            return;
        }

        abortRef.current?.abort();
        abortRef.current = new AbortController();

        safeSetState(() => {
            setLoading(true);
            setError(null);
        });

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
        safeSetState(() => {
            setMe(data);
            setError(cache.error);
            setLoading(false);
        });
    }, [safeSetState]);

    const updateMe = useCallback(
        async (payload: Partial<MeResponse>) => {
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
