/* assets/js/energ-members.js
   Unified login script: phone/email OTP + password login + UI
   Expects localized ENERG_AJAX object { ajax_url, nonce }
*/
jQuery(function($){
  if (typeof ENERG_AJAX === 'undefined') {
    console.error("ENERG_AJAX missing");
    return;
  }

  const ajaxUrl = ENERG_AJAX.ajax_url;
  const nonce = ENERG_AJAX.nonce;

  // UI helpers
  function showMsg(selector, text) { $(selector).text(text); }
  function clearMsg(selector) { $(selector).text(''); }

  /* TAB SWITCH */
  $(".auth-tab").on("click", function(){
      $(".auth-tab").removeClass("active");
      $(this).addClass("active");
      $(".auth-tab-content").hide();
      $("#" + $(this).data("tab")).show();
  });

  /* Mode toggle OTP / Password */
  $(".mode-option").on("click", function(){
      let mode = $(this).data("mode");
      $(this).siblings().removeClass("active");
      $(this).addClass("active");

      if(mode === "phone-otp"){
          $("#phone-pass-box").hide();
          $("#phone-otp-box").show();
      }
      if(mode === "phone-pass"){
          $("#phone-otp-box").hide();
          $("#phone-pass-box").show();
      }
      if(mode === "email-otp"){
          $("#email-pass-box").hide();
          $("#email-otp-box").show();
      }
      if(mode === "email-pass"){
          $("#email-otp-box").hide();
          $("#email-pass-box").show();
      }
  });

  /* -------------------
     PHONE OTP: SEND
     ------------------- */
  $("#send-otp-btn").on("click", function(e){
    e.preventDefault();
    clearMsg('#login-msg');

    let phone = $('#phone_input').val() || '';
    phone = phone.trim();

    if (!phone) { alert('Enter phone'); return; }

    $.post(ajaxUrl, {
      action: 'energ_send_otp',
      nonce: nonce,
      identifier: phone
    })
    .done(function(res){
      if (res && res.success) {
        // next UI
        $('#phone_otp').val('');
        $('#phone-otp-step2').show();
        $('#phone-otp-box').find('button#send-otp-btn').hide();
        // optionally store pending phone
        sessionStorage.setItem('energ_pending_phone', phone);
        console.log('Phone OTP sent:', res);
      } else {
        alert(res.data || 'Failed to send OTP');
      }
    })
    .fail(function(xhr){
      console.error('Phone send OTP error:', xhr.responseText);
      alert('AJAX error sending phone OTP');
    });
  });

  /* PHONE OTP: VERIFY */
  $("#phone-verify-otp").on("click", function(e){
    e.preventDefault();
    let otp = $('#phone_otp').val().trim();
    const phone = sessionStorage.getItem('energ_pending_phone') || $('#phone_input').val().trim();

    if (!otp) { alert('Enter OTP'); return; }

    $.post(ajaxUrl, {
      action: 'energ_verify_otp',
      nonce: nonce,
      identifier: phone,
      otp: otp
    })
    .done(function(res){
      if (res && res.success) {
        // if server returned redirect
        if (res.data && res.data.redirect) {
          window.location.href = res.data.redirect;
        } else {
          window.location.href = '/dashboard';
        }
      } else {
        alert(res.data || 'Invalid OTP');
      }
    })
    .fail(function(xhr){
      console.error('Phone verify error:', xhr.responseText);
      alert('AJAX error (verify phone OTP)');
    });
  });

  /* PHONE PASSWORD LOGIN */
  $("#login-phone-pass").on("click", function(e){
    e.preventDefault();
    const phone = $('#phone_input').val().trim();
    const password = $('#phone_password').val();

    if (!phone || !password) { alert('Phone and password required'); return; }

    $.post(ajaxUrl, {
      action: 'energ_password_login',
      nonce: nonce,
      identifier: phone,
      password: password
    })
    .done(function(res){
      if (res && res.success) {
        window.location.href = res.data.redirect || '/dashboard';
      } else {
        alert(res.data || 'Login failed');
      }
    }).fail(function(xhr){
      console.error('Phone password login error:', xhr.responseText);
      alert('AJAX error (login)');
    });
  });

  /* -------------------
     EMAIL OTP: SEND
     ------------------- */
  $("#send-email-otp").on("click", function(e){
    e.preventDefault();
    const email = $('#email_input').val().trim();
    if (!email) { alert('Enter email'); return; }

    $.post(ajaxUrl, {
      action: 'energ_send_email_otp',
      nonce: nonce,
      email: email
    })
    .done(function(res){
      if (res && res.success) {
        $('#email_otp').val('');
        $('#email-otp-step2').show();
        $('#email-otp-box').find('button#send-email-otp').hide();
        sessionStorage.setItem('energ_pending_email', email);
        console.log('Email OTP sent:', res);
      } else {
        alert(res.data || 'Failed to send email OTP');
      }
    })
    .fail(function(xhr){
      console.error('Email send OTP error:', xhr.responseText);
      alert('AJAX error (send email OTP)');
    });
  });

  /* EMAIL OTP: VERIFY */
  $("#email-verify-otp").on("click", function(e){
    e.preventDefault();
    const otp = $('#email_otp').val().trim();
    const email = sessionStorage.getItem('energ_pending_email') || $('#email_input').val().trim();

    if (!otp) { alert('Enter OTP'); return; }

    $.post(ajaxUrl, {
      action: 'energ_verify_email_otp',
      nonce: nonce,
      email: email,
      otp: otp
    })
    .done(function(res){
      if (res && res.success) {
        if (res.data && res.data.redirect) {
          window.location.href = res.data.redirect;
        } else {
          window.location.href = '/dashboard';
        }
      } else {
        alert(res.data || 'Invalid OTP');
      }
    })
    .fail(function(xhr){
      console.error('Email verify error:', xhr.responseText);
      alert('AJAX error (verify email OTP)');
    });
  });

  /* EMAIL PASSWORD LOGIN */
  $("#login-email-pass").on("click", function(e){
    e.preventDefault();
    const email = $('#email_input').val().trim();
    const password = $('#email_password').val();

    if (!email || !password) { alert('Email and password required'); return; }

    $.post(ajaxUrl, {
      action: 'energ_password_login',
      nonce: nonce,
      identifier: email,
      password: password
    })
    .done(function(res){
      if (res && res.success) {
        window.location.href = res.data.redirect || '/dashboard';
      } else {
        alert(res.data || 'Login failed');
      }
    })
    .fail(function(xhr){
      console.error('Email password login error:', xhr.responseText);
      alert('AJAX error (login)');
    });
  });

  /* RESEND handlers (optional) */
  $(document).on('click', '.resend-btn', function(e){
    e.preventDefault();
    const current = $(this).closest('.auth-tab-content').attr('id');
    if (current === 'phone-section') {
      $('#send-otp-btn').trigger('click');
    } else {
      $('#send-email-otp').trigger('click');
    }
  });

  // debug
  console.log("energ_members_script_loaded");
});