<?php
namespace EnergMembers\Frontend;

defined('ABSPATH') || exit;

class Shortcodes {

  public function __construct() {
    add_shortcode('energ_login', [$this, 'render_login']);
  }

  /* =====================================================
     MAIN WRAPPER
     ===================================================== */
  private function wrapper($content) {
    return '
      <div class="energ-auth">
        <div class="energ-card">
          ' . $content . '
        </div>
      </div>
    ';
  }

  /* =====================================================
     LOGIN FORM
     ===================================================== */
  public function render_login() {
    ob_start();
    ?>
    <div class="energ-header">
      <h2>Welcome back</h2>
      <p>Login to continue to your account</p>
    </div>

    <form id="energ-login-form" class="energ-form">
      <div class="energ-field">
        <label>Email address</label>
        <input
          type="email"
          name="email"
          class="energ-input"
          placeholder="you@example.com"
          required
        />
      </div>

      <div class="energ-response"></div>

      <button type="submit" class="energ-btn">
        Send OTP
      </button>
    </form>

    <div class="energ-footer">
      <a href="#" id="energ-show-register">New user? Create account</a>
    </div>

    <div id="energ-otp-step" style="display:none;">
      <?php echo $this->render_otp(false); ?>
    </div>

    <div id="energ-register-step" style="display:none;">
      <?php echo $this->render_register(false); ?>
    </div>

    <script>
      document.addEventListener("energ:show-otp", function () {
        document.getElementById("energ-login-form").style.display = "none";
        document.querySelector(".energ-footer").style.display = "none";
        document.getElementById("energ-otp-step").style.display = "block";
      });

      document.getElementById("energ-show-register").addEventListener("click", function (e) {
        e.preventDefault();
        document.getElementById("energ-login-form").style.display = "none";
        document.querySelector(".energ-footer").style.display = "none";
        document.getElementById("energ-register-step").style.display = "block";
      });
    </script>
    <?php

    return $this->wrapper(ob_get_clean());
  }

  /* =====================================================
     OTP FORM
     ===================================================== */
  public function render_otp($wrap = true) {
    ob_start();
    ?>
    <div class="energ-header">
      <h2>Verify OTP</h2>
      <p>Enter the 6-digit code sent to you</p>
    </div>

    <form id="energ-otp-form" class="energ-form">
      <div class="energ-otp">
        <?php for ($i = 0; $i < 6; $i++) : ?>
          <input type="text" maxlength="1" inputmode="numeric" />
        <?php endfor; ?>
      </div>

      <div class="energ-response"></div>

      <button type="submit" class="energ-btn">
        Verify & Continue
      </button>
    </form>
    <?php

    $html = ob_get_clean();
    return $wrap ? $this->wrapper($html) : $html;
  }

  /* =====================================================
     REGISTER FORM
     ===================================================== */
  public function render_register($wrap = true) {
    ob_start();
    ?>
    <div class="energ-header">
      <h2>Create account</h2>
      <p>Join us in less than a minute</p>
    </div>

    <form id="energ-register-form" class="energ-form">
      <div class="energ-field">
        <label>Full name</label>
        <input type="text" name="name" class="energ-input" required />
      </div>

      <div class="energ-field">
        <label>Email address</label>
        <input type="email" name="email" class="energ-input" required />
      </div>

      <div class="energ-field">
        <label>Password</label>
        <input type="password" name="password" class="energ-input" required />
      </div>

      <div class="energ-response"></div>

      <button type="submit" class="energ-btn">
        Create account
      </button>
    </form>
    <?php

    $html = ob_get_clean();
    return $wrap ? $this->wrapper($html) : $html;
  }
}
