<?php
namespace Energ\Frontend;

defined('ABSPATH') || exit;

class Shortcodes {

  /**
   * Register all shortcodes
   */
  public static function register() {
    add_shortcode('energ_login', [self::class, 'render_login']);
  }

  /**
   * Login + OTP UI (Passwordless)
   */
  public static function render_login() {
    ob_start();
    ?>
    <div class="energ-auth">
      <div class="energ-card energ-step">

        <div class="energ-header">
          <h2>Welcome back</h2>
          <p>Login to continue</p>
        </div>

        <!-- LOGIN -->
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

        <!-- OTP -->
        <form
          id="energ-otp-form"
          class="energ-form energ-step"
          style="display:none;"
        >
          <div class="energ-otp">
            <?php for ($i = 0; $i < 6; $i++) : ?>
              <input type="text" maxlength="1" inputmode="numeric" />
            <?php endfor; ?>
          </div>

          <div class="energ-response"></div>

          <button type="submit" class="energ-btn">
            Verify OTP
          </button>

          <button
            type="button"
            id="energ-resend-otp"
            class="energ-btn"
            style="margin-top:10px;background:#e5e7eb;color:#000;"
          >
            Resend OTP
          </button>
        </form>

        <!-- SUCCESS -->
        <div
          id="energ-success-step"
          class="energ-step"
          style="display:none;text-align:center;"
        >
          <div class="energ-success-check">
            ✔
          </div>
          <h2>You're in</h2>
          <p>Redirecting...</p>
        </div>

      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
