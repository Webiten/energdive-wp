import { http } from "./http";

export const AuthAPI = {
  requestOtp(identifier: string) {
    return http("/auth/request-otp", {
      method: "POST",
      body: JSON.stringify({ identifier }),
    });
  },

  verifyOtp(identifier: string, otp: string) {
    return http("/auth/verify-otp", {
      method: "POST",
      body: JSON.stringify({ identifier, otp }),
    });
  },

  completeRegistration(payload: any) {
    return http("/auth/complete-registration", {
      method: "POST",
      body: JSON.stringify(payload),
    });
  },

  me() {
    return http("/me");
  },

  logout(refresh_token: string) {
    return http("/auth/logout", {
      method: "POST",
      body: JSON.stringify({ refresh_token }),
    });
  },
};
