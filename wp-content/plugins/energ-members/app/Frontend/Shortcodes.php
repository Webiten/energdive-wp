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
                        <div class="energ-tabs" role="tablist" aria-label="Choose login method">
                            <button class="energ-tab is-active" type="button" role="tab" aria-selected="true" data-tab="phone">Phone</button>
                            <button class="energ-tab" type="button" role="tab" aria-selected="false" data-tab="email">Email</button>
                        </div>

                        <div class="energ-tabpanel" data-tabpanel="phone">
                            <label class="energ-label">Phone Number</label>
                            <div class="energ-phone">
                                <select class="energ-input energ-phone-code" data-field="phone_country">
                                    <option value="+91" selected>+91 (IN)</option>
                                    <option value="+971">+971 (UAE)</option>
                                    <option value="+966">+966 (KSA)</option>
                                    <option value="+1">+1 (US)</option>
                                    <option value="+44">+44 (UK)</option>
                                </select>
                                <input class="energ-input energ-phone-num" type="tel" inputmode="tel" autocomplete="tel" placeholder="Mobile number" data-field="phone_number" />
                            </div>
                            <p class="energ-help">We will send a 6-digit OTP by SMS.</p>
                        </div>

                        <div class="energ-tabpanel energ-hidden" data-tabpanel="email">
                            <label class="energ-label">Email Address</label>
                            <input class="energ-input" type="email" inputmode="email" autocomplete="email" placeholder="name@company.com" data-field="email" />
                            <p class="energ-help">We will send a 6-digit OTP to your inbox.</p>
                        </div>

                        <input type="hidden" data-field="identifier" />
                        <button class="energ-btn energ-btn-primary" type="button" data-action="requestOtp">Send OTP</button>
                    </div>

                    <div class="energ-step energ-hidden" data-step="otp">
                        <label class="energ-label">Enter OTP</label>
                        <div class="energ-otp" data-otp>
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                            <input class="energ-otp-box" inputmode="numeric" maxlength="1" />
                        </div>
                        <input type="hidden" data-field="otp" />
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
                                <label class="energ-label">Job Title</label>
                                <input class="energ-input" type="text" data-field="job_title" placeholder="Manager, HSE" />
                            </div>
                            <div>
                                <label class="energ-label">Organisation</label>
                                <input class="energ-input" type="text" data-field="organization" placeholder="Company name" />
                            </div>
                            <div>
                                <label class="energ-label">State</label>
                                <input class="energ-input" type="text" data-field="state" placeholder="Gujarat" />
                            </div>
                            <div>
                                <label class="energ-label">Industry</label>
                                <select class="energ-input" multiple data-field="industry" data-enhance="multiselect">
                                    <option value="oil-gas">Oil & Gas</option>
                                    <option value="power-utilities">Power & Utilities</option>
                                    <option value="renewables">Renewables</option>
                                    <option value="chemicals">Chemicals</option>
                                    <option value="manufacturing">Manufacturing</option>
                                </select>
                            </div>
                            <div>
                                <label class="energ-label">Sub Industry</label>
                                <select class="energ-input" multiple data-field="sub_industry" data-enhance="multiselect">
                                    <option value="upstream">Upstream</option>
                                    <option value="midstream">Midstream</option>
                                    <option value="downstream">Downstream</option>
                                    <option value="solar">Solar</option>
                                    <option value="wind">Wind</option>
                                    <option value="transmission">Transmission</option>
                                </select>
                            </div>
                        </div>

                        <div class="energ-grid">
                            <div>
                                <label class="energ-label">Communities</label>
                                <select class="energ-input" multiple data-field="communities" data-enhance="multiselect"></select>
                            </div>
                            <div>
                                <label class="energ-label">Sub-Communities</label>
                                <select class="energ-input" multiple data-field="sub_communities" data-enhance="multiselect"></select>
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
                            <label class="energ-label">Job Title</label>
                            <input class="energ-input" type="text" data-field="jobTitle" />
                        </div>
                        <div>
                            <label class="energ-label">Organisation</label>
                            <input class="energ-input" type="text" data-field="organization" />
                        </div>
                        <div>
                            <label class="energ-label">Country</label>
                            <input class="energ-input" type="text" data-field="country" />
                        </div>
                        <div>
                            <label class="energ-label">Industry</label>
                            <select class="energ-input" multiple data-field="industry" data-enhance="multiselect">
                                <option value="oil-gas">Oil & Gas</option>
                                <option value="power-utilities">Power & Utilities</option>
                                <option value="renewables">Renewables</option>
                                <option value="chemicals">Chemicals</option>
                                <option value="manufacturing">Manufacturing</option>
                            </select>
                        </div>
                        <div>
                            <label class="energ-label">Sub Industry</label>
                            <select class="energ-input" multiple data-field="subIndustry" data-enhance="multiselect">
                                <option value="upstream">Upstream</option>
                                <option value="midstream">Midstream</option>
                                <option value="downstream">Downstream</option>
                                <option value="solar">Solar</option>
                                <option value="wind">Wind</option>
                                <option value="transmission">Transmission</option>
                            </select>
                        </div>
                    </div>

                    <div class="energ-grid">
                        <div>
                            <label class="energ-label">Communities</label>
                            <select class="energ-input" multiple data-field="communities" data-enhance="multiselect"></select>
                        </div>
                        <div>
                            <label class="energ-label">Sub-Communities</label>
                            <select class="energ-input" multiple data-field="subCommunities" data-enhance="multiselect"></select>
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
