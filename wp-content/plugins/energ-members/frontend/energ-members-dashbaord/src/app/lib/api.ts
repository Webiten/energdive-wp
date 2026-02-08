const BASE = "/wp-json/energ/v1";

const getAccessToken = () => localStorage.getItem("access_token") || "";
const getRefreshToken = () => localStorage.getItem("refresh_token") || "";

export const setTokens = (accessToken?: string, refreshToken?: string) => {
  if (accessToken) localStorage.setItem("access_token", accessToken);
  if (refreshToken) localStorage.setItem("refresh_token", refreshToken);
};

export const clearTokens = () => {
  localStorage.removeItem("access_token");
  localStorage.removeItem("refresh_token");
};

const normalizeIdentifier = (identifier: string) => {
  const v = (identifier || "").trim();
  if (v.includes("@")) return v.toLowerCase();
  return v.replace(/[^\d]/g, "");
};

const throwIfNotOk = async (res: Response) => {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const message = data?.message || data?.data?.message || data?.error || "Request failed";
    throw { ...data, message, status: res.status };
  }
  return data;
};

export const AuthAPI = {
  async requestOtp(identifier: string, context: "login" | "register_phone" = "login") {
    const normalized = normalizeIdentifier(identifier);
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier: normalized, context }),
    });
    return throwIfNotOk(res);
  },

  async verifyOtp(identifier: string, otp: string) {
    const normalized = normalizeIdentifier(identifier);
    const res = await fetch(`${BASE}/auth/verify-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier: normalized, otp }),
    });

    const data = await throwIfNotOk(res);

    if (data?.access_token || data?.refresh_token) {
      setTokens(data?.access_token, data?.refresh_token);
    }

    return data;
  },

  async completeRegistration(payload: any) {
    const token = getAccessToken();
    const res = await fetch(`${BASE}/auth/complete-registration`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
      body: JSON.stringify(payload),
    });
    return throwIfNotOk(res);
  },

  async me() {
    const token = getAccessToken();
    const res = await fetch(`${BASE}/me`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    });
    return throwIfNotOk(res);
  },

  /** ✅ NEW: Update me */
  async updateMe(payload: any) {
    const token = getAccessToken();
    const res = await fetch(`${BASE}/me`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
      body: JSON.stringify(payload),
    });
    return throwIfNotOk(res);
  },

  async logout() {
    const refresh_token = getRefreshToken();
    const res = await fetch(`${BASE}/auth/logout`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ refresh_token }),
    });

    try {
      await throwIfNotOk(res);
    } finally {
      clearTokens();
    }

    return { success: true };
  },
};

export const NewsAPI = {
  async getNews() {
    const res = await fetch(`${BASE}/news`, {
      headers: { Accept: "application/json" },
    });

    return throwIfNotOk(res);
  },
};

