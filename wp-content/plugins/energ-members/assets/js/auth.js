(() => {

  /* ==============================
     STORAGE
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

  /* ==============================
     HELPERS
     ============================== */
  const parseConfig = el => {
    try { return JSON.parse(el.getAttribute('data-config') || '{}'); }
    catch { return {}; }
  };

  const showAlert = (root, msg, type = 'error') => {
    const box = root.querySelector('.energ-alert');
    if (!box) return;

    if (!msg) {
      box.classList.add('energ-hidden');
      box.textContent = '';
      return;
    }

    box.className = `energ-alert ${type}`;
    box.textContent = msg;
    box.classList.remove('energ-hidden');
  };

  const setBusy = (btn, busy, label = 'Please wait...') => {
    if (!btn) return;
    btn.disabled = busy;
    if (!btn.dataset.label) btn.dataset.label = btn.textContent;
    btn.textContent = busy ? label : btn.dataset.label;
  };

  const shakeOTP = root => {
    const otp = root.querySelector('[data-otp]');
    if (!otp) return;
    otp.classList.remove('energ-shake');
    void otp.offsetWidth;
    otp.classList.add('energ-shake');
  };

  /* ==============================
     API
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

    throw new Error(data.message || 'Request failed');
  }

  /* ==============================
     AUTH MOUNT (EMAIL ONLY)
     ============================== */
  function mountAuth(root) {
    const cfg = parseConfig(root);

    const step = name => {
      root.querySelectorAll('[data-step]').forEach(s =>
        s.classList.add('energ-hidden')
      );
      root.querySelector(`[data-step="${name}"]`)
        ?.classList.remove('energ-hidden');
    };

    const emailInput = root.querySelector('[data-field="email"]');
    const identifierInput = root.querySelector('[data-field="identifier"]');

    const otpInput = root.querySelector('[data-field="otp"]');

    const btnRequest = root.querySelector('[data-action="requestOtp"]');
    const btnVerify = root.querySelector('[data-action="verifyOtp"]');
    const btnResend = root.querySelector('[data-action="resendOtp"]');

    /* Sync email → identifier */
    emailInput?.addEventListener('input', () => {
      identifierInput.value = emailInput.value.trim();
    });

    /* REQUEST OTP */
    btnRequest?.addEventListener('click', async () => {
      showAlert(root, '');
      const identifier = identifierInput.value.trim();

      if (!identifier || !identifier.includes('@')) {
        showAlert(root, 'Please enter a valid email address');
        return;
      }

      setBusy(btnRequest, true, 'Sending OTP...');

      try {
        const data = await apiFetch('/auth/request-otp', {
          method: 'POST',
          body: { identifier, context: 'login' },
          auth: false
        });

        if (data?.success) {
          setStored(STORAGE_KEYS.identifier, identifier);
          showAlert(root, 'OTP sent to your email', 'success');
          step('otp');
        }
      } catch (e) {
        showAlert(root, e.message);
      } finally {
        setBusy(btnRequest, false);
      }
    });

    /* VERIFY OTP */
    btnVerify?.addEventListener('click', async () => {
      showAlert(root, '');
      const otp = otpInput.value.trim();
      const identifier = identifierInput.value.trim();

      if (otp.length < 6) {
        shakeOTP(root);
        showAlert(root, 'Please enter valid OTP');
        return;
      }

      setBusy(btnVerify, true, 'Verifying...');

      try {
        const data = await apiFetch('/auth/verify-otp', {
          method: 'POST',
          body: { identifier, otp },
          auth: false
        });

        if (data?.access_token) {
          setSession({
            accessToken: data.access_token,
            refreshToken: data.refresh_token,
            identifier,
            expiresIn: data.expires_in
          });

          if (data.is_new_user) {
            showAlert(root, 'Almost done! Complete your profile.', 'success');
            step('onboarding'); // PHONE VERIFY WILL BE HERE
          } else {
            showAlert(root, 'Login successful. Redirecting...', 'success');
            setTimeout(() => {
              window.location.href = cfg.redirect || ENERG.home || '/';
            }, 900);
          }
        } else {
          shakeOTP(root);
          showAlert(root, 'Invalid OTP');
        }
      } catch (e) {
        shakeOTP(root);
        showAlert(root, e.message);
      } finally {
        setBusy(btnVerify, false);
      }
    });

    btnResend?.addEventListener('click', () => btnRequest?.click());

    step('identifier');
  }

  /* ==============================
     INIT
     ============================== */
  document.addEventListener('DOMContentLoaded', () => {
    document
      .querySelectorAll('[data-energ-ui="auth"]')
      .forEach(mountAuth);
  });

})();
