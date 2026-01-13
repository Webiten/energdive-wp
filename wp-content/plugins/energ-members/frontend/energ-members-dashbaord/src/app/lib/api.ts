import { http } from "./http";

const BASE = "/wp-json/energ/v1";

export const AuthAPI = {
  async requestOtp(identifier: string) {
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  async verifyOtp(identifier: string, otp: string) {
    const res = await fetch(`${BASE}/auth/verify-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier, otp }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  async completeRegistration(payload: any) {
    const token = localStorage.getItem("access_token");

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

  async me() {
    const token = localStorage.getItem("access_token");

    const res = await fetch(`${BASE}/me`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },

  async logout(refresh_token: string) {
    const res = await fetch(`${BASE}/auth/logout`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ refresh_token }),
    });

    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  },
};

