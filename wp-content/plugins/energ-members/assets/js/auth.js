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

  function setBusy(btn, busy, labelBusy = 'Please wait...') {
    if (!btn) return;
    btn.disabled = !!busy;
    btn.dataset.busy = busy ? '1' : '0';
    if (!btn.dataset.label) btn.dataset.label = btn.textContent;
    btn.textContent = busy ? labelBusy : btn.dataset.label;
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

  function normalizePhone(code, number) {
    const c = String(code || '').trim();
    const n = String(number || '').replace(/\D/g, '');
    if (!c || !n) return '';
    // Ensure country code begins with +
    const cc = c.startsWith('+') ? c : `+${c}`;
    return `${cc}${n}`;
  }

  function parseCsvString(s) {
    if (!s) return [];
    return String(s)
      .split(',')
      .map(x => x.trim())
      .filter(Boolean);
  }

  /*
    MultiSelect Enhancer
    - Keeps original <select multiple> for value storage
    - Renders chips + search + dropdown
  */
  function enhanceMultiSelect(selectEl) {
    if (!selectEl || selectEl.dataset.enhanced === '1') return;
    if (!selectEl.multiple) return;

    selectEl.dataset.enhanced = '1';

    const wrapper = document.createElement('div');
    wrapper.className = 'energ-ms';

    const trigger = document.createElement('div');
    trigger.className = 'energ-ms-trigger';
    trigger.tabIndex = 0;

    const input = document.createElement('input');
    input.className = 'energ-ms-input';
    input.type = 'text';
    input.placeholder = 'Search and select...';
    input.autocomplete = 'off';

    const panel = document.createElement('div');
    panel.className = 'energ-ms-panel energ-hidden';

    const empty = document.createElement('div');
    empty.className = 'energ-ms-empty energ-hidden';
    empty.textContent = 'No matches.';

    panel.appendChild(empty);

    function getOptions() {
      return Array.from(selectEl.options).map(o => ({
        value: o.value,
        label: o.textContent || o.value,
        selected: o.selected
      }));
    }

    function setSelected(value, selected) {
      const opt = Array.from(selectEl.options).find(o => o.value === value);
      if (opt) opt.selected = selected;
      selectEl.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function renderChips() {
      trigger.querySelectorAll('.energ-ms-chip').forEach(n => n.remove());

      const selected = getOptions().filter(o => o.selected);
      selected.forEach(item => {
        const chip = document.createElement('span');
        chip.className = 'energ-ms-chip';
        chip.textContent = item.label;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', `Remove ${item.label}`);
        btn.textContent = '×';
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          setSelected(item.value, false);
          renderChips();
          renderPanel();
        });

        chip.appendChild(btn);
        trigger.insertBefore(chip, input);
      });
    }

    function renderPanel() {
      panel.querySelectorAll('.energ-ms-item').forEach(n => n.remove());

      const q = (input.value || '').trim().toLowerCase();
      const opts = getOptions().filter(o => {
        if (!q) return true;
        return o.label.toLowerCase().includes(q);
      });

      if (!opts.length) {
        empty.classList.remove('energ-hidden');
        return;
      }

      empty.classList.add('energ-hidden');
      opts.forEach(o => {
        const row = document.createElement('div');
        row.className = 'energ-ms-item' + (o.selected ? ' is-selected' : '');
        row.setAttribute('role', 'option');
        row.setAttribute('aria-selected', o.selected ? 'true' : 'false');

        const left = document.createElement('span');
        left.textContent = o.label;

        const right = document.createElement('span');
        right.textContent = o.selected ? 'Selected' : '';
        right.style.color = 'rgba(17,24,39,.55)';
        right.style.fontSize = '12px';

        row.appendChild(left);
        row.appendChild(right);

        row.addEventListener('click', () => {
          setSelected(o.value, !o.selected);
          renderChips();
          renderPanel();
        });

        panel.appendChild(row);
      });
    }

    function openPanel() {
      panel.classList.remove('energ-hidden');
      renderPanel();
    }

    function closePanel() {
      panel.classList.add('energ-hidden');
    }

    trigger.addEventListener('click', () => {
      if (panel.classList.contains('energ-hidden')) {
        openPanel();
        input.focus();
      } else {
        closePanel();
      }
    });

    input.addEventListener('input', () => renderPanel());

    document.addEventListener('click', (e) => {
      if (!wrapper.contains(e.target)) closePanel();
    });

    selectEl.addEventListener('change', () => {
      renderChips();
      renderPanel();
    });

    // Build DOM
    selectEl.style.display = 'none';
    selectEl.parentNode.insertBefore(wrapper, selectEl);
    wrapper.appendChild(selectEl);
    wrapper.appendChild(trigger);
    trigger.appendChild(input);
    wrapper.appendChild(panel);

    renderChips();
    renderPanel();
  }

  function enhanceAllMultiSelects(root) {
    root.querySelectorAll('select[multiple][data-enhance="multiselect"]').forEach(enhanceMultiSelect);
  }

  function mountOtpBoxes(root) {
    const otpWrap = root.querySelector('[data-otp]');
    const otpHidden = root.querySelector('[data-field="otp"]');
    if (!otpWrap || !otpHidden) return;

    const boxes = Array.from(otpWrap.querySelectorAll('input.energ-otp-box'));
    if (!boxes.length) return;

    function sync() {
      otpHidden.value = boxes.map(b => (b.value || '').replace(/\D/g, '')).join('').slice(0, 6);
    }

    boxes.forEach((box, idx) => {
      box.addEventListener('input', () => {
        box.value = (box.value || '').replace(/\D/g, '').slice(0, 1);
        sync();
        if (box.value && idx < boxes.length - 1) boxes[idx + 1].focus();
      });

      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !box.value && idx > 0) {
          boxes[idx - 1].focus();
        }
      });

      box.addEventListener('paste', (e) => {
        const text = (e.clipboardData || window.clipboardData).getData('text') || '';
        const digits = text.replace(/\D/g, '').slice(0, 6).split('');
        if (!digits.length) return;
        e.preventDefault();
        digits.forEach((d, i) => {
          if (boxes[i]) boxes[i].value = d;
        });
        sync();
        const lastIdx = Math.min(digits.length, boxes.length) - 1;
        if (boxes[lastIdx]) boxes[lastIdx].focus();
      });
    });

    return {
      clear: () => {
        boxes.forEach(b => (b.value = ''));
        otpHidden.value = '';
      },
      focus: () => {
        boxes[0].focus();
      }
    };
  }

  function mountAuth(root) {
    const config = parseConfig(root);
    const stepIdentifier = root.querySelector('[data-step="identifier"]');
    const stepOtp = root.querySelector('[data-step="otp"]');
    const stepOnboarding = root.querySelector('[data-step="onboarding"]');

    const hiddenIdentifier = root.querySelector('[data-field="identifier"]');

    const phoneCode = root.querySelector('[data-field="phone_country"]');
    const phoneNumber = root.querySelector('[data-field="phone_number"]');
    const emailInput = root.querySelector('[data-field="email"]');

    const btnRequest = root.querySelector('[data-action="requestOtp"]');
    const btnVerify = root.querySelector('[data-action="verifyOtp"]');
    const btnResend = root.querySelector('[data-action="resendOtp"]');
    const btnComplete = root.querySelector('[data-action="completeRegistration"]');

    const communitySelect = root.querySelector('[data-field="communities"]');
    const subCommunitySelect = root.querySelector('[data-field="sub_communities"]');

    const tabs = Array.from(root.querySelectorAll('.energ-tab'));
    const panels = Array.from(root.querySelectorAll('.energ-tabpanel'));

    const slugList = (config && config.communitySlugs) ? config.communitySlugs : {};

    function showStep(name) {
      [stepIdentifier, stepOtp, stepOnboarding].forEach(el => el && el.classList.add('energ-hidden'));
      const step = root.querySelector(`[data-step="${name}"]`);
      if (step) step.classList.remove('energ-hidden');
    }

    function setActiveTab(name) {
      tabs.forEach(t => {
        const is = t.dataset.tab === name;
        t.classList.toggle('is-active', is);
        t.setAttribute('aria-selected', is ? 'true' : 'false');
      });
      panels.forEach(p => {
        p.classList.toggle('energ-hidden', p.dataset.tabpanel !== name);
      });
      setIdentifierFromInputs();
    }

    tabs.forEach(t => {
      t.addEventListener('click', () => setActiveTab(t.dataset.tab));
    });

    function setIdentifierFromInputs() {
      const active = tabs.find(t => t.classList.contains('is-active'));
      const mode = active ? active.dataset.tab : 'phone';

      let identifier = '';
      if (mode === 'email') {
        identifier = (emailInput?.value || '').trim();
      } else {
        identifier = normalizePhone(phoneCode?.value, phoneNumber?.value);
      }

      if (hiddenIdentifier) hiddenIdentifier.value = identifier;
      return identifier;
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
    communitySelect?.addEventListener('change', fillSubCommunities);

    enhanceAllMultiSelects(root);

    const otp = mountOtpBoxes(root);

    async function requestOtp() {
      showAlert(root, '');
      const identifier = setIdentifierFromInputs();
      if (!identifier) {
        showAlert(root, 'Please enter a valid phone number or email address.', 'error');
        return;
      }

      setBusy(btnRequest, true, 'Sending OTP...');
      try {
        const data = await apiFetch('/auth/request-otp', {
          method: 'POST',
          body: { identifier, context: 'login' },
          auth: false
        });

        if (data && data.success) {
          setSession({ identifier });
          showAlert(root, 'OTP sent. Please check your SMS/Email.', 'success');
          showStep('otp');
          otp?.clear();
          otp?.focus();
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
      const identifier = setIdentifierFromInputs() || getSession().identifier;
      const otpCode = (root.querySelector('[data-field="otp"]')?.value || '').trim();

      if (!identifier || !otpCode || otpCode.length < 6) {
        showAlert(root, 'Please enter the 6-digit OTP.', 'error');
        return;
      }

      setBusy(btnVerify, true, 'Verifying...');
      try {
        const data = await apiFetch('/auth/verify-otp', {
          method: 'POST',
          body: { identifier, otp: otpCode },
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
            showAlert(root, 'Verified. Please complete your profile.', 'success');
            showStep('onboarding');
            enhanceAllMultiSelects(root);
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
      setBusy(btnComplete, true, 'Saving...');
      try {
        const payload = {
          first_name: readField('first_name'),
          last_name: readField('last_name'),
          job_title: readField('job_title'),
          organization: readField('organization'),
          country: readField('country'),
          state: readField('state') || '',
          industry: readField('industry') || [],
          sub_industry: readField('sub_industry') || [],
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
          showAlert(root, 'Profile completed. Redirecting...', 'success');
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

    // Update identifier as user types
    phoneNumber?.addEventListener('input', setIdentifierFromInputs);
    emailInput?.addEventListener('input', setIdentifierFromInputs);
    phoneCode?.addEventListener('change', setIdentifierFromInputs);

    // Default mode: phone
    setActiveTab('phone');

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
        // Select multiple: match values
        const values = Array.isArray(value) ? value : parseCsvString(value);
        Array.from(el.options).forEach(o => { o.selected = values.includes(o.value); });
        el.dispatchEvent(new Event('change', { bubbles: true }));
        return;
      }

      el.value = value ?? '';
    }

    function getField(name) {
      const el = root.querySelector(`[data-field="${name}"]`);
      if (!el) return '';
      if (el.tagName === 'SELECT' && el.multiple) {
        return selectedValues(el);
      }
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
        setTimeout(() => { window.location.href = to; }, 900);
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
        setField('jobTitle', u.job_title || '');
        setField('organization', u.organization || '');
        setField('country', u.country || '');
        setField('industry', u.industry || '');
        setField('subIndustry', u.sub_industry || '');

        const communities = Array.isArray(u.communities) ? u.communities : [];
        const subs = Array.isArray(u.sub_communities) ? u.sub_communities : [];

        fillCommunities(communities);
        fillSubCommunities(subs);

        enhanceAllMultiSelects(root);

        setLoading(false);
      } catch (e) {
        setLoading(false);
        showAlert(root, e.message || 'Failed to load profile.', 'error');
      }
    }

    async function saveProfile() {
      showAlert(root, '');
      setBusy(btnSave, true, 'Saving...');
      try {
        const payload = {
          firstName: getField('firstName'),
          lastName: getField('lastName'),
          jobTitle: getField('jobTitle'),
          organization: getField('organization'),
          country: getField('country'),
          industry: getField('industry'),
          subIndustry: getField('subIndustry'),
          communities: selectedValues(communitySelect),
          subCommunities: selectedValues(subCommunitySelect)
        };

        const data = await apiFetch('/me', { method: 'POST', body: payload, auth: true });
        if (data && data.success) {
          showAlert(root, 'Profile updated successfully.', 'success');
          // refresh binds
          if (data.user) {
            bindText('[data-bind="memberName"]', `${data.user.first_name || ''} ${data.user.last_name || ''}`.trim() || 'Member');
          }
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
    enhanceAllMultiSelects(root);
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
