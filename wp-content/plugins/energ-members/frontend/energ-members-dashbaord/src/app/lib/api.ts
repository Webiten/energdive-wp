import { http } from "./http";

const BASE = "/wp-json/energ/v1";

export const AuthAPI = {
  /** ============================
   * REQUEST OTP (EMAIL / PHONE)
   * ============================ */
  async requestOtp(identifier: string) {
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier }),
    });

    if (!res.ok) {
      throw await res.json();
    }

    return res.json();
  },

  /** ============================
   * VERIFY OTP
   * ============================ */
  async verifyOtp(identifier: string, otp: string) {
    const res = await fetch(`${BASE}/auth/verify-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier, otp }),
    });

    if (!res.ok) {
      throw await res.json();
    }

    return res.json();
  },

  /** ============================
   * COMPLETE REGISTRATION
   * ============================ */
  completeRegistration(payload: any) {
    return http("/auth/complete-registration", {
      method: "POST",
      body: JSON.stringify(payload),
    });
  },

  /** ============================
   * CURRENT USER
   * ============================ */
  me() {
    return http("/me");
  },

  /** ============================
   * LOGOUT
   * ============================ */
  logout(refresh_token: string) {
    return http("/auth/logout", {
      method: "POST",
      body: JSON.stringify({ refresh_token }),
    });
  },
};
