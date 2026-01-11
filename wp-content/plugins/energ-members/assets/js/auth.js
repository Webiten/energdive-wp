if (typeof ENERG === "undefined") {
  console.error("ENERG not loaded");
  return;
}


const Auth = {
  async requestOtp(email) {
    const res = await fetch(`${ENERG.api}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email })
    });
    return res.json();
  },

  async verifyOtp(email, otp) {
    const res = await fetch(`${ENERG.api}/auth/verify-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, otp })
    });
    const data = await res.json();

    if (data.access_token) {
      localStorage.setItem("access_token", data.access_token);
      localStorage.setItem("refresh_token", data.refresh_token);
      localStorage.setItem("expires_at", Date.now() + data.expires_in * 1000);
    }

    return data;
  },

  async me() {
    const token = localStorage.getItem("access_token");
    const res = await fetch(`${ENERG.api}/me`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    return res.json();
  },

  logout() {
    localStorage.clear();
    window.location.href = "/login";
  }
};
