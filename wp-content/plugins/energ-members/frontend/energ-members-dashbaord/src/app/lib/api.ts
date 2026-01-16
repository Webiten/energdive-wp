const BASE = "/wp-json/energ/v1";

/** 🔐 Helper: get tokens safely */
const getAccessToken = () => localStorage.getItem("access_token") || "";
const getRefreshToken = () => localStorage.getItem("refresh_token") || "";

/** ✅ Store tokens centrally */
export const setTokens = (accessToken?: string, refreshToken?: string) => {
  if (accessToken) localStorage.setItem("access_token", accessToken);
  if (refreshToken) localStorage.setItem("refresh_token", refreshToken);
};

export const clearTokens = () => {
  localStorage.removeItem("access_token");
  localStorage.removeItem("refresh_token");
};

/** ✅ Normalize identifier for backend compatibility */
const normalizeIdentifier = (identifier: string) => {
  const v = (identifier || "").trim();
  if (v.includes("@")) return v.toLowerCase();
  return v.replace(/[^\d]/g, "");
};

/** 🔁 Standard error formatting */
const throwIfNotOk = async (res: Response) => {
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const message = data?.message || data?.data?.message || data?.error || "Request failed";
    throw { ...data, message, status: res.status };
  }
  return data;
};

export const AuthAPI = {
  /** 📧📱 Request OTP (email or phone) */
  async requestOtp(identifier: string, context: "login" | "register_phone" = "login") {
    const normalized = normalizeIdentifier(identifier);

    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier: normalized, context }),
    });

    return throwIfNotOk(res);
  },

  /** 🔐 Verify OTP (email or phone) + ✅ auto-store tokens */
  async verifyOtp(identifier: string, otp: string) {
    const normalized = normalizeIdentifier(identifier);

    const res = await fetch(`${BASE}/auth/verify-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier: normalized, otp }),
    });

    const data = await throwIfNotOk(res);

    // ✅ Ensure tokens are stored for both phone/email flows (when returned)
    if (data?.access_token || data?.refresh_token) {
      setTokens(data?.access_token, data?.refresh_token);
    }

    return data;
  },

  /** 📝 Complete Registration */
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

  /** 👤 Get logged-in user (JWT) */
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

  /** 🚪 Logout (revokes refresh token + clears local tokens) */
  async logout() {
    const refresh_token = getRefreshToken();

    const res = await fetch(`${BASE}/auth/logout`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({ refresh_token }),
    });

    // Always clear tokens on client, even if backend returns invalid_token
    try {
      await throwIfNotOk(res);
    } finally {
      clearTokens();
    }

    return { success: true };
  },
};
