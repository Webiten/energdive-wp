const BASE = "/wp-json/energ/v1";

/** 🔐 Helper: get access token safely */
const getAccessToken = () => {
    return localStorage.getItem("access_token") || "";
};

/** ✅ Normalize identifier for backend compatibility
 * - email: lowercased + trimmed
 * - phone: digits only (removes +, spaces, dashes)
 */
const normalizeIdentifier = (identifier: string) => {
    const v = (identifier || "").trim();
    if (v.includes("@")) return v.toLowerCase();
    return v.replace(/[^\d]/g, ""); // digits only
};

/** 🔁 Standard error formatting */
const throwIfNotOk = async (res: Response) => {
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        // Ensure message exists for UI
        const message =
            data?.message ||
            data?.data?.message ||
            data?.error ||
            "Request failed";
        throw { ...data, message };
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


    /** 🔐 Verify OTP (email or phone) */
    async verifyOtp(identifier: string, otp: string) {
        const normalized = normalizeIdentifier(identifier);

        const res = await fetch(`${BASE}/auth/verify-otp`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ identifier: normalized, otp }),
        });

        return throwIfNotOk(res);
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

        return throwIfNotOk(res);
    },

    /** 👤 Get logged-in user */
    async me() {
        const token = getAccessToken();

        const res = await fetch(`${BASE}/me`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        return throwIfNotOk(res);
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

        return throwIfNotOk(res);
    },
};
