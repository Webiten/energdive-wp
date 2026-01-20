<?php

namespace Energ\Frontend;

use Energ\Helpers\CommunityValidator;

defined('ABSPATH') || exit;

/**
 * Frontend shortcodes that mount a small JS-driven UI.
 */
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

        $communitySlugs = CommunityValidator::slugList();
        $communityLabels = CommunityValidator::list();

        $data = [
            'redirect' => esc_url_raw($atts['redirect']),
            'communitySlugs' => $communitySlugs,
            'communityLabels' => $communityLabels,
        ];

        ob_start();
        ?>
        <div class="energ-ui" data-energ-ui="auth" data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">
            <div class="energ-card">
                <div class="energ-header">
                    <h2>Member Login</h2>
                    <p>Use your email or mobile number to receive a one-time password.</p>
                </div>

                <div class="energ-alert energ-hidden" role="alert"></div>

                <div class="energ-steps">
                    <div class="energ-step" data-step="identifier">
                        <label class="energ-label">Email or Mobile</label>
                        <input class="energ-input" type="text" inputmode="email" placeholder="name@company.com or +91XXXXXXXXXX" data-field="identifier" />
                        <button class="energ-btn energ-btn-primary" type="button" data-action="requestOtp">Send OTP</button>
                    </div>

                    <div class="energ-step energ-hidden" data-step="otp">
                        <label class="energ-label">Enter OTP</label>
                        <input class="energ-input" type="text" inputmode="numeric" maxlength="6" placeholder="6-digit OTP" data-field="otp" />
                        <div class="energ-row">
                            <button class="energ-btn energ-btn-primary" type="button" data-action="verifyOtp">Verify & Login</button>
                            <button class="energ-btn" type="button" data-action="resendOtp">Resend</button>
                        </div>
                    </div>

                    <div class="energ-step energ-hidden" data-step="onboarding">
                        <h3>Complete Your Profile</h3>
                        <div class="energ-grid">
                            <div>
                                <label class="energ-label">First Name</label>
                                <input class="energ-input" type="text" data-field="first_name" />
                            </div>
                            <div>
                                <label class="energ-label">Last Name</label>
                                <input class="energ-input" type="text" data-field="last_name" />
                            </div>
                            <div>
                                <label class="energ-label">Country</label>
                                <input class="energ-input" type="text" data-field="country" placeholder="India" />
                            </div>
                            <div>
                                <label class="energ-label">State</label>
                                <input class="energ-input" type="text" data-field="state" placeholder="Gujarat" />
                            </div>
                            <div>
                                <label class="energ-label">Industry</label>
                                <input class="energ-input" type="text" data-field="industry" placeholder="Oil & Gas" />
                            </div>
                            <div>
                                <label class="energ-label">Sub Industry</label>
                                <input class="energ-input" type="text" data-field="sub_industry" placeholder="Upstream" />
                            </div>
                        </div>

                        <div class="energ-grid">
                            <div>
                                <label class="energ-label">Communities</label>
                                <select class="energ-input" multiple size="6" data-field="communities"></select>
                            </div>
                            <div>
                                <label class="energ-label">Sub-Communities</label>
                                <select class="energ-input" multiple size="6" data-field="sub_communities"></select>
                            </div>
                        </div>

                        <label class="energ-checkbox">
                            <input type="checkbox" data-field="privacy_accepted" />
                            <span>I accept the privacy policy.</span>
                        </label>

                        <button class="energ-btn energ-btn-primary" type="button" data-action="completeRegistration">Complete Registration</button>
                    </div>
                </div>

                <div class="energ-footer">
                    <small>Having trouble? Contact support.</small>
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

        $communitySlugs = CommunityValidator::slugList();
        $communityLabels = CommunityValidator::list();

        $data = [
            'login_url' => esc_url_raw($atts['login_url']),
            'communitySlugs' => $communitySlugs,
            'communityLabels' => $communityLabels,
        ];

        ob_start();
        ?>
        <div class="energ-ui" data-energ-ui="dashboard" data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">
            <div class="energ-card">
                <div class="energ-header">
                    <h2>Member Dashboard</h2>
                    <p>View and update your profile.</p>
                </div>

                <div class="energ-alert energ-hidden" role="alert"></div>

                <div class="energ-dashboard energ-hidden" data-role="dashboard">
                    <div class="energ-row energ-row-between">
                        <div>
                            <strong data-bind="memberName"></strong><br />
                            <span data-bind="memberIdentifier"></span>
                        </div>
                        <div>
                            <button class="energ-btn" type="button" data-action="logout">Logout</button>
                        </div>
                    </div>

                    <h3>Profile</h3>
                    <div class="energ-grid">
                        <div>
                            <label class="energ-label">First Name</label>
                            <input class="energ-input" type="text" data-field="firstName" />
                        </div>
                        <div>
                            <label class="energ-label">Last Name</label>
                            <input class="energ-input" type="text" data-field="lastName" />
                        </div>
                        <div>
                            <label class="energ-label">Country</label>
                            <input class="energ-input" type="text" data-field="country" />
                        </div>
                        <div>
                            <label class="energ-label">Industry</label>
                            <input class="energ-input" type="text" data-field="industry" />
                        </div>
                    </div>

                    <div class="energ-grid">
                        <div>
                            <label class="energ-label">Communities</label>
                            <select class="energ-input" multiple size="6" data-field="communities"></select>
                        </div>
                        <div>
                            <label class="energ-label">Sub-Communities</label>
                            <select class="energ-input" multiple size="6" data-field="subCommunities"></select>
                        </div>
                    </div>

                    <button class="energ-btn energ-btn-primary" type="button" data-action="saveProfile">Save Changes</button>
                </div>

                <div class="energ-loading" data-role="loading">
                    <p>Loading...</p>
                </div>

                <div class="energ-footer">
                    <small>Session tokens are stored in the browser for this device.</small>
                </div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}
