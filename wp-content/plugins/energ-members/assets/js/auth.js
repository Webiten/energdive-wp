(() => {
  const STORAGE_KEYS = {
    access: 'energ_access_token',
    refresh: 'energ_refresh_token',
    identifier: 'energ_identifier',
    expiresAt: 'energ_access_expires_at'
  };

  function nowMs() { return Date.now(); }

  function getStored(key) {
    try { return localStorage.getItem(key) || ''; } catch (_) { return ''; }
  }

  function setStored(key, val) {
    try { localStorage.setItem(key, val); } catch (_) {}
  }

  function removeStored(key) {
    try { localStorage.removeItem(key); } catch (_) {}
  }

  function clearSession() {
    Object.values(STORAGE_KEYS).forEach(removeStored);
  }

  function getSession() {
    return {
      accessToken: getStored(STORAGE_KEYS.access),
      refreshToken: getStored(STORAGE_KEYS.refresh),
      identifier: getStored(STORAGE_KEYS.identifier),
      expiresAt: parseInt(getStored(STORAGE_KEYS.expiresAt), 10) || 0
    };
  }

  function setSession({ accessToken, refreshToken, identifier, expiresIn }) {
    if (accessToken) setStored(STORAGE_KEYS.access, accessToken);
    if (refreshToken) setStored(STORAGE_KEYS.refresh, refreshToken);
    if (identifier) setStored(STORAGE_KEYS.identifier, identifier);
    if (expiresIn) setStored(STORAGE_KEYS.expiresAt, String(nowMs() + (Number(expiresIn) * 1000)));
  }

  function humanizeSlug(slug) {
    return String(slug)
      .replace(/-/g, ' ')
      .replace(/\b\w/g, c => c.toUpperCase());
  }

  function parseConfig(el) {
    const raw = el.getAttribute('data-config') || '{}';
    try { return JSON.parse(raw); } catch (_) { return {}; }
  }

  function alertBox(root) {
    return root.querySelector('.energ-alert');
  }

  function showAlert(root, msg, variant = 'error') {
    const box = alertBox(root);
    if (!box) return;
    if (!msg) {
      box.classList.add('energ-hidden');
      box.textContent = '';
      box.removeAttribute('data-variant');
      return;
    }
    box.classList.remove('energ-hidden');
    box.textContent = msg;
    box.setAttribute('data-variant', variant);
  }

  function setBusy(btn, busy) {
    if (!btn) return;
    btn.disabled = !!busy;
    btn.dataset.busy = busy ? '1' : '0';
  }

  async function apiFetch(path, { method = 'GET', body = undefined, auth = true } = {}) {
    if (!window.ENERG || !ENERG.api) {
      throw new Error('ENERG config missing. Ensure wp_localize_script is working.');
    }
    const url = `${ENERG.api}${path}`;
    const session = getSession();

    const headers = {
      'Content-Type': 'application/json',
      'X-WP-Nonce': ENERG.nonce || ''
    };

    if (auth && session.accessToken) {
      headers['Authorization'] = `Bearer ${session.accessToken}`;
    }

    const res = await fetch(url, {
      method,
      headers,
      body: body ? JSON.stringify(body) : undefined,
      credentials: 'same-origin'
    });

    const text = await res.text();
    let data = {};
    try { data = text ? JSON.parse(text) : {}; } catch (_) { data = { message: text }; }

    if (res.ok) return data;

    // If JWT expired, attempt refresh once.
    if (res.status === 401 && auth) {
      const refreshed = await tryRefresh();
      if (refreshed) {
        return apiFetch(path, { method, body, auth });
      }
    }

    const msg = (data && (data.message || data.error || data.code)) ? (data.message || data.error || data.code) : `Request failed (${res.status})`;
    const err = new Error(msg);
    err.status = res.status;
    err.data = data;
    throw err;
  }

  async function tryRefresh() {
    const session = getSession();
    if (!session.refreshToken) return false;

    try {
      const data = await apiFetch('/auth/refresh-token', {
        method: 'POST',
        body: { refresh_token: session.refreshToken },
        auth: false
      });

      if (data && data.access_token) {
        setSession({
          accessToken: data.access_token,
          refreshToken: data.refresh_token || session.refreshToken,
          identifier: session.identifier,
          expiresIn: data.expires_in
        });
        return true;
      }
    } catch (_) {
      // fall through
    }

    clearSession();
    return false;
  }

  function mountAuth(root) {
    const config = parseConfig(root);
    const stepIdentifier = root.querySelector('[data-step="identifier"]');
    const stepOtp = root.querySelector('[data-step="otp"]');
    const stepOnboarding = root.querySelector('[data-step="onboarding"]');

    const identifierInput = root.querySelector('[data-field="identifier"]');
    const otpInput = root.querySelector('[data-field="otp"]');

    const btnRequest = root.querySelector('[data-action="requestOtp"]');
    const btnVerify = root.querySelector('[data-action="verifyOtp"]');
    const btnResend = root.querySelector('[data-action="resendOtp"]');
    const btnComplete = root.querySelector('[data-action="completeRegistration"]');

    const communitySelect = root.querySelector('[data-field="communities"]');
    const subCommunitySelect = root.querySelector('[data-field="sub_communities"]');

    const slugList = (config && config.communitySlugs) ? config.communitySlugs : {};

    function showStep(name) {
      [stepIdentifier, stepOtp, stepOnboarding].forEach(el => el && el.classList.add('energ-hidden'));
      const step = root.querySelector(`[data-step="${name}"]`);
      if (step) step.classList.remove('energ-hidden');
    }

    function selectedValues(selectEl) {
      return Array.from(selectEl?.selectedOptions || []).map(o => o.value).filter(Boolean);
    }

    function fillCommunities() {
      if (!communitySelect) return;
      communitySelect.innerHTML = '';
      Object.keys(slugList).forEach(communitySlug => {
        const opt = document.createElement('option');
        opt.value = communitySlug;
        opt.textContent = humanizeSlug(communitySlug);
        communitySelect.appendChild(opt);
      });
    }

    function fillSubCommunities() {
      if (!subCommunitySelect) return;
      const communities = selectedValues(communitySelect);
      const set = new Set();
      communities.forEach(c => (slugList[c] || []).forEach(s => set.add(s)));

      const prev = new Set(selectedValues(subCommunitySelect));
      subCommunitySelect.innerHTML = '';

      Array.from(set).sort().forEach(sub => {
        const opt = document.createElement('option');
        opt.value = sub;
        opt.textContent = humanizeSlug(sub);
        if (prev.has(sub)) opt.selected = true;
        subCommunitySelect.appendChild(opt);
      });
    }

    fillCommunities();
    fillSubCommunities();
    if (communitySelect) communitySelect.addEventListener('change', fillSubCommunities);

    async function requestOtp() {
      showAlert(root, '');
      const identifier = (identifierInput?.value || '').trim();
      if (!identifier) {
        showAlert(root, 'Please enter email or mobile number.', 'error');
        return;
      }

      setBusy(btnRequest, true);
      try {
        const data = await apiFetch('/auth/request-otp', {
          method: 'POST',
          body: { identifier, context: 'login' },
          auth: false
        });
        if (data && data.success) {
          setSession({ identifier });
          showAlert(root, 'OTP sent. Please check your email/SMS.', 'success');
          showStep('otp');
          if (otpInput) otpInput.focus();
        } else {
          showAlert(root, 'Unable to send OTP.', 'error');
        }
      } catch (e) {
        showAlert(root, e.message || 'Unable to send OTP.', 'error');
      } finally {
        setBusy(btnRequest, false);
      }
    }

    async function verifyOtp() {
      showAlert(root, '');
      const identifier = (identifierInput?.value || getSession().identifier || '').trim();
      const otp = (otpInput?.value || '').trim();
      if (!identifier || !otp) {
        showAlert(root, 'Identifier and OTP are required.', 'error');
        return;
      }

      setBusy(btnVerify, true);
      try {
        const data = await apiFetch('/auth/verify-otp', {
          method: 'POST',
          body: { identifier, otp },
          auth: false
        });

        if (data && data.access_token) {
          setSession({
            accessToken: data.access_token,
            refreshToken: data.refresh_token,
            identifier,
            expiresIn: data.expires_in
          });

          if (data.is_new_user) {
            showAlert(root, 'Login successful. Please complete your profile.', 'success');
            showStep('onboarding');
          } else {
            showAlert(root, 'Login successful. Redirecting...', 'success');
            const to = (config && config.redirect) ? config.redirect : (ENERG.home || '/');
            window.location.href = to;
          }
        } else {
          showAlert(root, 'OTP verification failed.', 'error');
        }
      } catch (e) {
        showAlert(root, e.message || 'OTP verification failed.', 'error');
      } finally {
        setBusy(btnVerify, false);
      }
    }

    async function resendOtp() {
      return requestOtp();
    }

    function readField(name) {
      const el = root.querySelector(`[data-field="${name}"]`);
      if (!el) return null;
      if (el.type === 'checkbox') return !!el.checked;
      if (el.tagName === 'SELECT' && el.multiple) return selectedValues(el);
      return (el.value || '').trim();
    }

    async function completeRegistration() {
      showAlert(root, '');
      setBusy(btnComplete, true);
      try {
        const payload = {
          first_name: readField('first_name'),
          last_name: readField('last_name'),
          country: readField('country'),
          state: readField('state') || '',
          industry: readField('industry'),
          sub_industry: readField('sub_industry') || '',
          communities: readField('communities') || [],
          sub_communities: readField('sub_communities') || [],
          privacy_accepted: readField('privacy_accepted') === true
        };

        const data = await apiFetch('/auth/complete-registration', {
          method: 'POST',
          body: payload,
          auth: true
        });

        if (data && data.success) {
          showAlert(root, 'Registration completed. Redirecting...', 'success');
          const to = (config && config.redirect) ? config.redirect : (ENERG.home || '/');
          window.location.href = to;
          return;
        }

        showAlert(root, 'Could not complete registration.', 'error');
      } catch (e) {
        showAlert(root, e.message || 'Could not complete registration.', 'error');
      } finally {
        setBusy(btnComplete, false);
      }
    }

    btnRequest?.addEventListener('click', requestOtp);
    btnVerify?.addEventListener('click', verifyOtp);
    btnResend?.addEventListener('click', resendOtp);
    btnComplete?.addEventListener('click', completeRegistration);

    // If already logged in, redirect.
    const s = getSession();
    if (s.accessToken && s.expiresAt > nowMs()) {
      const to = (config && config.redirect) ? config.redirect : (ENERG.home || '/');
      window.location.href = to;
    }
  }

  function mountDashboard(root) {
    const config = parseConfig(root);
    const slugList = (config && config.communitySlugs) ? config.communitySlugs : {};

    const loading = root.querySelector('[data-role="loading"]');
    const dash = root.querySelector('[data-role="dashboard"]');

    const communitySelect = root.querySelector('[data-field="communities"]');
    const subCommunitySelect = root.querySelector('[data-field="subCommunities"]');

    const btnSave = root.querySelector('[data-action="saveProfile"]');
    const btnLogout = root.querySelector('[data-action="logout"]');

    function setLoading(isLoading) {
      if (loading) loading.classList.toggle('energ-hidden', !isLoading);
      if (dash) dash.classList.toggle('energ-hidden', isLoading);
    }

    function selectedValues(selectEl) {
      return Array.from(selectEl?.selectedOptions || []).map(o => o.value).filter(Boolean);
    }

    function fillCommunities(selected = []) {
      if (!communitySelect) return;
      communitySelect.innerHTML = '';
      Object.keys(slugList).forEach(communitySlug => {
        const opt = document.createElement('option');
        opt.value = communitySlug;
        opt.textContent = humanizeSlug(communitySlug);
        if (selected.includes(communitySlug)) opt.selected = true;
        communitySelect.appendChild(opt);
      });
    }

    function fillSubCommunities(selected = []) {
      if (!subCommunitySelect) return;
      const communities = selectedValues(communitySelect);
      const set = new Set();
      communities.forEach(c => (slugList[c] || []).forEach(s => set.add(s)));
      subCommunitySelect.innerHTML = '';
      Array.from(set).sort().forEach(sub => {
        const opt = document.createElement('option');
        opt.value = sub;
        opt.textContent = humanizeSlug(sub);
        if (selected.includes(sub)) opt.selected = true;
        subCommunitySelect.appendChild(opt);
      });
    }

    communitySelect?.addEventListener('change', () => fillSubCommunities(selectedValues(subCommunitySelect)));

    function bindText(selector, value) {
      const el = root.querySelector(selector);
      if (el) el.textContent = value || '';
    }

    function setField(name, value) {
      const el = root.querySelector(`[data-field="${name}"]`);
      if (!el) return;
      if (el.tagName === 'SELECT' && el.multiple) {
        // handled separately
        return;
      }
      el.value = value ?? '';
    }

    function getField(name) {
      const el = root.querySelector(`[data-field="${name}"]`);
      if (!el) return '';
      return (el.value || '').trim();
    }

    async function ensureAuth() {
      const s = getSession();
      if (s.accessToken && s.expiresAt > nowMs()) return true;
      return tryRefresh();
    }

    async function loadMe() {
      showAlert(root, '');
      setLoading(true);

      const authed = await ensureAuth();
      if (!authed) {
        setLoading(false);
        showAlert(root, 'You are not logged in. Redirecting to login...', 'error');
        const to = (config && config.login_url) ? config.login_url : (ENERG.home || '/');
        setTimeout(() => { window.location.href = to; }, 1200);
        return;
      }

      try {
        const data = await apiFetch('/me', { method: 'GET', auth: true });
        if (!data || !data.success || !data.user) throw new Error('Failed to load profile.');

        const u = data.user;
        bindText('[data-bind="memberName"]', `${u.first_name || ''} ${u.last_name || ''}`.trim() || 'Member');
        bindText('[data-bind="memberIdentifier"]', u.email || u.phone || '');

        setField('firstName', u.first_name || '');
        setField('lastName', u.last_name || '');
        setField('country', u.country || '');
        setField('industry', u.industry || '');

        const communities = Array.isArray(u.communities) ? u.communities : [];
        const subs = Array.isArray(u.sub_communities) ? u.sub_communities : [];

        fillCommunities(communities);
        fillSubCommunities(subs);

        setLoading(false);
      } catch (e) {
        setLoading(false);
        showAlert(root, e.message || 'Failed to load profile.', 'error');
      }
    }

    async function saveProfile() {
      showAlert(root, '');
      setBusy(btnSave, true);
      try {
        const payload = {
          firstName: getField('firstName'),
          lastName: getField('lastName'),
          country: getField('country'),
          industry: getField('industry'),
          communities: selectedValues(communitySelect),
          subCommunities: selectedValues(subCommunitySelect)
        };

        const data = await apiFetch('/me', { method: 'POST', body: payload, auth: true });
        if (data && data.success) {
          showAlert(root, 'Profile updated successfully.', 'success');
        } else {
          showAlert(root, 'Could not save changes.', 'error');
        }
      } catch (e) {
        showAlert(root, e.message || 'Could not save changes.', 'error');
      } finally {
        setBusy(btnSave, false);
      }
    }

    async function logout() {
      showAlert(root, '');
      const s = getSession();
      try {
        await apiFetch('/auth/logout', {
          method: 'POST',
          body: { refresh_token: s.refreshToken },
          auth: false
        });
      } catch (_) {
        // ignore
      } finally {
        clearSession();
        const to = (config && config.login_url) ? config.login_url : (ENERG.home || '/');
        window.location.href = to;
      }
    }

    btnSave?.addEventListener('click', saveProfile);
    btnLogout?.addEventListener('click', logout);

    fillCommunities([]);
    fillSubCommunities([]);
    loadMe();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-energ-ui]')
      .forEach(root => {
        const type = root.getAttribute('data-energ-ui');
        if (type === 'auth') mountAuth(root);
        if (type === 'dashboard') mountDashboard(root);
      });
  });
})();
