<?php
defined('ABSPATH') || exit;

$phone = sanitize_text_field($_GET['phone'] ?? '');
$nonce = wp_create_nonce('energ_nonce');
?>

<div class="energ-complete">

  <h1 class="energ-title">Complete your registration</h1>
  <p class="energ-subtitle">Verify your email and finish your profile</p>

  <input type="hidden" id="energ_nonce" value="<?php echo esc_attr($nonce); ?>">
  <input type="hidden" id="cr_phone" value="<?php echo esc_attr($phone); ?>">
  <input type="hidden" id="cr_email_otp_verified">

  <!-- EMAIL VERIFY -->
  <section id="step_email" class="energ-card">
    <label>Email address</label>
    <div class="energ-row">
      <input id="cr_email" type="email" placeholder="you@company.com">
      <button id="btn_send_email_otp" class="btn-primary">Send OTP</button>
    </div>

    <div id="email_otp_box" class="energ-hidden">
      <label>Enter OTP</label>
      <div class="energ-row">
        <input id="cr_email_otp" placeholder="6 digit OTP">
        <button id="btn_verify_email_otp" class="btn-outline">Verify</button>
      </div>
    </div>

    <p id="step1_msg" class="energ-msg"></p>
  </section>

  <!-- FULL FORM -->
  <section id="step_full_form" class="energ-card energ-hidden">

    <div class="energ-grid">
      <div>
        <label>First name</label>
        <input id="cr_first">
      </div>
      <div>
        <label>Last name</label>
        <input id="cr_last">
      </div>
      <div>
        <label>Mobile number</label>
        <input readonly value="<?php echo esc_attr($phone); ?>">
      </div>
      <div>
        <label>Email</label>
        <input id="cr_email_final" readonly>
      </div>
      <div>
        <label>Company</label>
        <input id="cr_org">
      </div>
      <div>
        <label>Designation</label>
        <input id="cr_designation">
      </div>
      <div>
        <label>Country</label>
        <select id="cr_country">
          <option value="">Select country</option>
          <option>India</option>
          <option>United States</option>
          <option>United Kingdom</option>
        </select>
      </div>
    </div>

    <button id="btn_submit_final" class="btn-primary full">
      Complete Registration
    </button>

    <p id="final_msg" class="energ-msg"></p>
  </section>

</div>
