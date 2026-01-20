/* =========================================================
   ENERG MEMBERS – AUTH UX ENGINE
   Senior-level JS (Public SaaS Experience)
   ========================================================= */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    initOTPInputs();
    initForms();
  });

  /* ---------- Helpers ---------- */

  function qs(selector, scope = document) {
    return scope.querySelector(selector);
  }

  function qsa(selector, scope = document) {
    return [...scope.querySelectorAll(selector)];
  }

  function showMessage(container, message, type = "success") {
    container.innerHTML = `
      <div class="energ-message ${type}">
        ${message}
      </div>
    `;
  }

  function setLoading(btn, loading = true) {
    if (!btn) return;

    if (loading) {
      btn.dataset.text = btn.innerHTML;
      btn.innerHTML = `<span class="energ-loader"></span>`;
      btn.disabled = true;
    } else {
      btn.innerHTML = btn.dataset.text;
      btn.disabled = false;
    }
  }

  async function apiRequest(url, data) {
    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
      body: JSON.stringify(data),
    });

    const json = await res.json();
    if (!res.ok) throw json;
    return json;
  }

  /* ---------- OTP UX ---------- */

  function initOTPInputs() {
    const inputs = qsa(".energ-otp input");
    if (!inputs.length) return;

    inputs.forEach((input, index) => {
      input.addEventListener("input", (e) => {
        const value = e.target.value.replace(/\D/g, "");
        e.target.value = value;

        if (value && inputs[index + 1]) {
          inputs[index + 1].focus();
        }
      });

      input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !input.value && inputs[index - 1]) {
          inputs[index - 1].focus();
        }
      });

      input.addEventListener("paste", (e) => {
        const paste = e.clipboardData.getData("text").replace(/\D/g, "");
        if (paste.length === inputs.length) {
          inputs.forEach((inp, i) => (inp.value = paste[i]));
        }
      });
    });
  }

  function collectOTP() {
    return qsa(".energ-otp input")
      .map((i) => i.value)
      .join("");
  }

  /* ---------- Forms ---------- */

  function initForms() {
    const loginForm = qs("#energ-login-form");
    const otpForm = qs("#energ-otp-form");
    const registerForm = qs("#energ-register-form");

    if (loginForm) handleLogin(loginForm);
    if (otpForm) handleOTPVerify(otpForm);
    if (registerForm) handleRegister(registerForm);
  }

  function handleLogin(form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const btn = qs("button[type='submit']", form);
      const messageBox = qs(".energ-response", form);
      const email = qs("input[name='email']", form).value.trim();

      if (!email) {
        showMessage(messageBox, "Please enter your email.", "error");
        return;
      }

      setLoading(btn, true);

      try {
        await apiRequest(energAuth.login_url, { email });
        showMessage(
          messageBox,
          "OTP sent successfully. Please check your email.",
          "success"
        );

        setTimeout(() => {
          document.dispatchEvent(new CustomEvent("energ:show-otp"));
        }, 600);
      } catch (err) {
        showMessage(messageBox, err.message || "Something went wrong.", "error");
      } finally {
        setLoading(btn, false);
      }
    });
  }

  function handleOTPVerify(form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const btn = qs("button[type='submit']", form);
      const messageBox = qs(".energ-response", form);
      const otp = collectOTP();

      if (otp.length < 6) {
        showMessage(messageBox, "Please enter complete OTP.", "error");
        return;
      }

      setLoading(btn, true);

      try {
        const res = await apiRequest(energAuth.verify_url, { otp });

        showMessage(
          messageBox,
          "Verification successful. Redirecting...",
          "success"
        );

        setTimeout(() => {
          window.location.href = res.redirect || "/";
        }, 1200);
      } catch (err) {
        showMessage(messageBox, err.message || "Invalid OTP.", "error");
      } finally {
        setLoading(btn, false);
      }
    });
  }

  function handleRegister(form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const btn = qs("button[type='submit']", form);
      const messageBox = qs(".energ-response", form);
      const formData = Object.fromEntries(new FormData(form).entries());

      setLoading(btn, true);

      try {
        await apiRequest(energAuth.register_url, formData);
        showMessage(
          messageBox,
          "Registration successful. Please login.",
          "success"
        );

        setTimeout(() => {
          window.location.reload();
        }, 1200);
      } catch (err) {
        showMessage(messageBox, err.message || "Registration failed.", "error");
      } finally {
        setLoading(btn, false);
      }
    });
  }
})();
