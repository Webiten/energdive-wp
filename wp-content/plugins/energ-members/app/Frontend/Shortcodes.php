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

    public static function renderAuth($atts = []): string
    {
        $atts = shortcode_atts([
            'redirect' => home_url('/dashboard/'),
        ], (array) $atts);

        $data = [
            'redirect' => esc_url_raw($atts['redirect']),
            'communitySlugs' => CommunityValidator::slugList(),
            'communityLabels' => CommunityValidator::list(),
        ];

        ob_start();
        ?>
        <div class="energ-ui energ-auth-layout"
             data-energ-ui="auth"
             data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">

            <!-- BRAND PANEL -->
            <div class="energ-auth-brand">
                <div class="energ-auth-brand-inner">
                    <div class="energ-brand-logo" data-brand-logo></div>

                    <h1>Energ Members</h1>
                    <p>Secure, passwordless access to your professional community.</p>

                    <ul class="energ-auth-points">
                        <li>✔ OTP based secure login</li>
                        <li>✔ Industry communities</li>
                        <li>✔ Personalised dashboard</li>
                    </ul>
                </div>
            </div>

            <!-- AUTH PANEL -->
            <div class="energ-auth-panel">
                <div class="energ-card energ-glass energ-pop">

                    <div class="energ-alert energ-hidden" role="alert"></div>

                    <div class="energ-steps">

                        <!-- STEP: IDENTIFIER -->
                        <div class="energ-step" data-step="identifier">
                            <h2>Sign in to your account</h2>
                            <p class="energ-subtitle">Choose how you want to receive your OTP</p>

                            <div class="energ-tabs">
                                <button class="energ-tab is-active" data-tab="phone">Mobile</button>
                                <button class="energ-tab" data-tab="email">Email</button>
                            </div>

                            <!-- PHONE -->
                            <div class="energ-tabpanel" data-tabpanel="phone">
                                <div class="energ-field">
                                    <select class="energ-input energ-phone-code" data-field="phone_country">
                                        <option value="+91" selected>+91</option>
                                        <option value="+971">+971</option>
                                        <option value="+966">+966</option>
                                        <option value="+1">+1</option>
                                        <option value="+44">+44</option>
                                    </select>

                                    <input class="energ-input energ-phone-num"
                                           type="tel"
                                           placeholder=" "
                                           data-field="phone_number" />

                                    <label>Mobile number</label>
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="energ-tabpanel energ-hidden" data-tabpanel="email">
                                <div class="energ-field">
                                    <input class="energ-input"
                                           type="email"
                                           placeholder=" "
                                           data-field="email" />
                                    <label>Email address</label>
                                </div>
                            </div>

                            <input type="hidden" data-field="identifier" />

                            <button class="energ-btn energ-btn-primary"
                                    type="button"
                                    data-action="requestOtp">
                                Continue
                            </button>
                        </div>

                        <!-- STEP: OTP -->
                        <div class="energ-step energ-hidden" data-step="otp">
                            <h2>Verify OTP</h2>
                            <p class="energ-subtitle">Enter the 6-digit code we sent you</p>

                            <div class="energ-otp energ-pop" data-otp>
                                <?php for ($i = 0; $i < 6; $i++) : ?>
                                    <input class="energ-otp-box" maxlength="1" inputmode="numeric" />
                                <?php endfor; ?>
                            </div>

                            <input type="hidden" data-field="otp" />

                            <div class="energ-row energ-row-between">
                                <button class="energ-btn energ-btn-primary"
                                        type="button"
                                        data-action="verifyOtp">
                                    Verify
                                </button>

                                <button class="energ-btn energ-btn-ghost"
                                        type="button"
                                        data-action="resendOtp">
                                    Resend OTP
                                </button>
                            </div>
                        </div>

                        <!-- STEP: ONBOARDING -->
                        <div class="energ-step energ-hidden" data-step="onboarding">
                            <h2>Complete your profile</h2>
                            <p class="energ-subtitle">
                                This helps us personalise your experience
                            </p>

                            <!-- YOUR EXISTING ONBOARDING GRID STAYS AS-IS -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

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

                    <button class="energ-btn energ-btn-ghost"
                            type="button"
                            data-action="logout">
                        Logout
                    </button>
                </div>

                <div class="energ-card energ-glass energ-pop">
                    <h3>Profile Information</h3>

                    <!-- YOUR EXISTING PROFILE GRID STAYS AS-IS -->

                    <button class="energ-btn energ-btn-primary"
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
