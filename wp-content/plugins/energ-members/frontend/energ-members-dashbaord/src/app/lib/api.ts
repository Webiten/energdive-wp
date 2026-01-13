const BASE = "/wp-json/energ/v1";

/** 🔐 Helper: get access token safely */
const getAccessToken = () => {
  return localStorage.getItem("access_token") || "";
};

export const AuthAPI = {
  /** 📧📱 Request OTP (email or phone) */
  async requestOtp(identifier: string) {
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ identifier }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  /** 🔐 Verify OTP (email or phone) */
  async verifyOtp(identifier: string, otp: string) {
    const res = await fetch(`${BASE}/auth/verify-otp`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ identifier, otp }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  /** 📝 Complete Registration */
  async completeRegistration(payload: any) {
    const token = getAccessToken();

    const res = await fetch(`${BASE}/auth/complete-registration`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  /** 👤 Get logged-in user */
  async me() {
    const token = getAccessToken();

    const res = await fetch(`${BASE}/me`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  /** 🚪 Logout */
  async logout(refresh_token: string) {
    const res = await fetch(`${BASE}/auth/logout`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ refresh_token }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },
};
