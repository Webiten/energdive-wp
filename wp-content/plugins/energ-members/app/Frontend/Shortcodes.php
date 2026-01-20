<?php

namespace Energ\Frontend;

use Energ\Helpers\CommunityValidator;

defined('ABSPATH') || exit;

class Shortcodes
{
    public static function register(): void
    {
        add_shortcode('energ_members_auth', [self::class, 'renderAuth']);
        add_shortcode('energ_members_dashboard', [self::class, 'renderDashboard']);
    }

    /**
     * LOGIN / AUTH (EMAIL ONLY)
     */
    public static function renderAuth($atts = []): string
    {
        $atts = shortcode_atts([
            'redirect' => home_url('/dashboard/'),
        ], (array) $atts);

        $data = [
            'redirect' => esc_url_raw($atts['redirect']),
            'mode'     => 'email_only', // IMPORTANT FLAG
        ];

        ob_start();
        ?>
        <div class="energ-ui energ-auth-layout"
             data-energ-ui="auth"
             data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">

            <!-- LEFT BRAND -->
            <div class="energ-auth-brand">
                <div class="energ-auth-brand-inner">
                    <div class="energ-brand-logo" data-brand-logo></div>

                    <h1>Energ Members</h1>
                    <p>
                        Secure, passwordless access to your professional community.
                    </p>

                    <ul class="energ-auth-points">
                        <li>✔ Email OTP secure login</li>
                        <li>✔ Industry communities</li>
                        <li>✔ Personalised dashboard</li>
                    </ul>
                </div>
            </div>

            <!-- RIGHT AUTH -->
            <div class="energ-auth-panel">
                <div class="energ-card energ-glass">

                    <div class="energ-alert energ-hidden" role="alert"></div>

                    <div class="energ-steps">

                        <!-- STEP 1: EMAIL IDENTIFIER -->
                        <div class="energ-step" data-step="identifier">
                            <h2>Sign in to your account</h2>
                            <p class="energ-subtitle">
                                We’ll send a one-time password to your email
                            </p>

                            <div class="energ-field">
                                <input
                                    class="energ-input"
                                    type="email"
                                    placeholder=" "
                                    data-field="email"
                                    required
                                />
                                <label>Email address</label>
                            </div>

                            <input type="hidden" data-field="identifier" />

                            <button
                                class="energ-btn energ-btn-primary"
                                type="button"
                                data-action="requestOtp">
                                Continue
                            </button>
                        </div>

                        <!-- STEP 2: OTP -->
                        <div class="energ-step energ-hidden" data-step="otp">
                            <h2>Verify OTP</h2>
                            <p class="energ-subtitle">
                                Enter the 6-digit code sent to your email
                            </p>

                            <div class="energ-otp" data-otp>
                                <?php for ($i = 0; $i < 6; $i++) : ?>
                                    <input
                                        class="energ-otp-box"
                                        maxlength="1"
                                        inputmode="numeric"
                                    />
                                <?php endfor; ?>
                            </div>

                            <input type="hidden" data-field="otp" />

                            <div class="energ-row energ-row-between">
                                <button
                                    class="energ-btn energ-btn-primary"
                                    type="button"
                                    data-action="verifyOtp">
                                    Verify
                                </button>

                                <button
                                    class="energ-btn energ-btn-ghost"
                                    type="button"
                                    data-action="resendOtp">
                                    Resend OTP
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: COMPLETE REGISTRATION -->
                        <div class="energ-step energ-hidden" data-step="onboarding">
                            <h2>Complete your profile</h2>
                            <p class="energ-subtitle">
                                Verify your phone & personalise your experience
                            </p>

                            <!-- PHONE + PROFILE COMES FROM TEMPLATE -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * DASHBOARD
     */
    public static function renderDashboard($atts = []): string
    {
        $atts = shortcode_atts([
            'login_url' => home_url('/login/'),
        ], (array) $atts);

        $data = [
            'login_url' => esc_url_raw($atts['login_url']),
        ];

        ob_start();
        ?>
        <div class="energ-ui energ-dashboard-layout"
             data-energ-ui="dashboard"
             data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">

            <div class="energ-dashboard-shell">

                <div class="energ-dashboard-header">
                    <div>
                        <h2 data-bind="memberName"></h2>
                        <p data-bind="memberIdentifier"></p>
                    </div>

                    <button
                        class="energ-btn energ-btn-ghost"
                        type="button"
                        data-action="logout">
                        Logout
                    </button>
                </div>

                <div class="energ-card energ-glass">
                    <h3>Profile Information</h3>

                    <!-- Profile template will be injected -->

                    <button
                        class="energ-btn energ-btn-primary"
                        type="button"
                        data-action="saveProfile">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}
