(() => {
  /* ==============================
     STORAGE & HELPERS (UNCHANGED)
     ============================== */
  const STORAGE_KEYS = {
    access: 'energ_access_token',
    refresh: 'energ_refresh_token',
    identifier: 'energ_identifier',
    expiresAt: 'energ_access_expires_at'
  };

  const nowMs = () => Date.now();

  const getStored = k => { try { return localStorage.getItem(k) || ''; } catch { return ''; } };
  const setStored = (k, v) => { try { localStorage.setItem(k, v); } catch {} };
  const removeStored = k => { try { localStorage.removeItem(k); } catch {} };

  const clearSession = () => Object.values(STORAGE_KEYS).forEach(removeStored);

  const getSession = () => ({
    accessToken: getStored(STORAGE_KEYS.access),
    refreshToken: getStored(STORAGE_KEYS.refresh),
    identifier: getStored(STORAGE_KEYS.identifier),
    expiresAt: parseInt(getStored(STORAGE_KEYS.expiresAt), 10) || 0
  });

  const setSession = ({ accessToken, refreshToken, identifier, expiresIn }) => {
    if (accessToken) setStored(STORAGE_KEYS.access, accessToken);
    if (refreshToken) setStored(STORAGE_KEYS.refresh, refreshToken);
    if (identifier) setStored(STORAGE_KEYS.identifier, identifier);
    if (expiresIn) setStored(STORAGE_KEYS.expiresAt, String(nowMs() + expiresIn * 1000));
  };

  const parseConfig = el => {
    try { return JSON.parse(el.getAttribute('data-config') || '{}'); }
    catch { return {}; }
  };

  const alertBox = root => root.querySelector('.energ-alert');

  const showAlert = (root, msg, variant = 'error') => {
    const box = alertBox(root);
    if (!box) return;
    if (!msg) {
      box.classList.add('energ-hidden');
      box.textContent = '';
      return;
    }
    box.classList.remove('energ-hidden');
    box.className = `energ-alert ${variant}`;
    box.textContent = msg;
  };

  const setBusy = (btn, busy, label = 'Please wait...') => {
    if (!btn) return;
    btn.disabled = busy;
    if (!btn.dataset.label) btn.dataset.label = btn.textContent;
    btn.textContent = busy ? label : btn.dataset.label;
  };

  /* ==============================
     MICRO INTERACTIONS (NEW)
     ============================== */

  function shakeOTP(root) {
    const otp = root.querySelector('[data-otp]');
    if (!otp) return;
    otp.classList.remove('energ-shake');
    void otp.offsetWidth;
    otp.classList.add('energ-shake');
  }

  function successPop(el) {
    if (!el) return;
    el.classList.add('energ-success-pop');
  }

  /* ==============================
     BRANDING INJECTION (NEW)
     ============================== */
  function applyBranding(root) {
    const cfg = parseConfig(root);
    if (cfg.brandColor) {
      document.documentElement.style.setProperty('--brand', cfg.brandColor);
    }

    if (cfg.brandLogo) {
      const logo = root.querySelector('[data-brand-logo]');
      if (logo) logo.style.backgroundImage = `url(${cfg.brandLogo})`;
    }
  }

  /* ==============================
     API (UNCHANGED)
     ============================== */
  async function apiFetch(path, { method = 'GET', body, auth = true } = {}) {
    const session = getSession();
    const headers = {
      'Content-Type': 'application/json',
      'X-WP-Nonce': ENERG.nonce || ''
    };
    if (auth && session.accessToken) {
      headers.Authorization = `Bearer ${session.accessToken}`;
    }

    const res = await fetch(`${ENERG.api}${path}`, {
      method,
      headers,
      body: body ? JSON.stringify(body) : undefined,
      credentials: 'same-origin'
    });

    const text = await res.text();
    const data = text ? JSON.parse(text) : {};

    if (res.ok) return data;

    if (res.status === 401 && auth && session.refreshToken) {
      const refreshed = await apiFetch('/auth/refresh-token', {
        method: 'POST',
        body: { refresh_token: session.refreshToken },
        auth: false
      });
      if (refreshed?.access_token) {
        setSession({
          accessToken: refreshed.access_token,
          refreshToken: refreshed.refresh_token || session.refreshToken,
          identifier: session.identifier,
          expiresIn: refreshed.expires_in
        });
        return apiFetch(path, { method, body, auth });
      }
    }

    throw new Error(data.message || 'Request failed');
  }

  /* ==============================
     AUTH MOUNT
     ============================== */
  function mountAuth(root) {
    applyBranding(root);

    const cfg = parseConfig(root);
    const step = name => {
      root.querySelectorAll('[data-step]').forEach(s => s.classList.add('energ-hidden'));
      const el = root.querySelector(`[data-step="${name}"]`);
      if (el) el.classList.remove('energ-hidden');
    };

    const btnRequest = root.querySelector('[data-action="requestOtp"]');
    const btnVerify = root.querySelector('[data-action="verifyOtp"]');
    const btnResend = root.querySelector('[data-action="resendOtp"]');

    btnRequest?.addEventListener('click', async () => {
      showAlert(root, '');
      setBusy(btnRequest, true, 'Sending OTP...');
      try {
        const id = root.querySelector('[data-field="identifier"]').value;
        const data = await apiFetch('/auth/request-otp', {
          method: 'POST',
          body: { identifier: id, context: 'login' },
          auth: false
        });
        if (data?.success) {
          showAlert(root, 'OTP sent successfully.', 'success');
          step('otp');
        }
      } catch (e) {
        showAlert(root, e.message, 'error');
      } finally {
        setBusy(btnRequest, false);
      }
    });

    btnVerify?.addEventListener('click', async () => {
      showAlert(root, '');
      setBusy(btnVerify, true, 'Verifying...');
      try {
        const otp = root.querySelector('[data-field="otp"]').value;
        const id = root.querySelector('[data-field="identifier"]').value;

        const data = await apiFetch('/auth/verify-otp', {
          method: 'POST',
          body: { identifier: id, otp },
          auth: false
        });

        if (data?.access_token) {
          setSession({
            accessToken: data.access_token,
            refreshToken: data.refresh_token,
            identifier: id,
            expiresIn: data.expires_in
          });

          showAlert(root, 'Success! Redirecting...', 'success');
          const card = root.querySelector('.energ-card');
          successPop(card);

          // LOTTIE SUCCESS
          const lottieBox = root.querySelector('#energ-lottie');
          if (lottieBox && window.lottie) {
            lottie.loadAnimation({
              container: lottieBox,
              renderer: 'svg',
              loop: false,
              autoplay: true,
              path: 'https://assets10.lottiefiles.com/packages/lf20_jbrw3hcz.json'
            });
          }

          setTimeout(() => {
            window.location.href = cfg.redirect || ENERG.home || '/';
          }, 1500);
        } else {
          shakeOTP(root);
          showAlert(root, 'Invalid OTP.', 'error');
        }
      } catch (e) {
        shakeOTP(root);
        showAlert(root, e.message, 'error');
      } finally {
        setBusy(btnVerify, false);
      }
    });

    btnResend?.addEventListener('click', () => btnRequest?.click());
  }

  /* ==============================
     INIT
     ============================== */
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-energ-ui="auth"]').forEach(mountAuth);
  });
})();
