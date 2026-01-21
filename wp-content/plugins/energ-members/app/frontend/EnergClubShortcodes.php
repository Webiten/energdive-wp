<?php
namespace Energ\Frontend;

class EnergClubShortcodes
{
    public static function register(): void
    {
        add_shortcode('energclub_login', [self::class, 'login']);
        add_shortcode('energclub_register', [self::class, 'registerForm']);
        add_shortcode('energclub_dashboard', [self::class, 'dashboard']);
    }

    /* ---------------------------
     * LOGIN: email → send link/OTP
     * --------------------------- */
    public static function login(): string
    {
        // If already logged in, go dashboard
        if (is_user_logged_in()) {
            wp_safe_redirect(home_url('/dashboard/'));
            exit;
        }

        $state = [
            'message' => '',
            'error' => '',
        ];

        // Handle email submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['energclub_login_submit'])) {
            if (!isset($_POST['energclub_nonce']) || !wp_verify_nonce($_POST['energclub_nonce'], 'energclub_login')) {
                $state['error'] = 'Security check failed. Please refresh and try again.';
            } else {
                $email = sanitize_email($_POST['email'] ?? '');
                if (!is_email($email)) {
                    $state['error'] = 'Please enter a valid email address.';
                } else {
                    // Create a short-lived verification token
                    $token = wp_generate_password(32, false, false);
                    set_transient('energclub_verify_' . md5($email . $token), [
                        'email' => $email,
                        'created' => time(),
                    ], 15 * MINUTE_IN_SECONDS);

                    $verify_url = add_query_arg([
                        'energclub_verify' => 1,
                        'email' => rawurlencode($email),
                        'token' => $token,
                    ], home_url('/login/'));

                    wp_mail(
                        $email,
                        'EnergClub Login Verification',
                        "Click to verify and continue:\n\n" . $verify_url . "\n\nThis link expires in 15 minutes."
                    );

                    $state['message'] = 'Verification link sent. Please check your email.';
                }
            }
        }

        // Handle verification callback (email link)
        if (isset($_GET['energclub_verify']) && (int)$_GET['energclub_verify'] === 1) {
            $email = sanitize_email($_GET['email'] ?? '');
            $token = sanitize_text_field($_GET['token'] ?? '');

            $key = 'energclub_verify_' . md5($email . $token);
            $payload = get_transient($key);

            if (!$payload) {
                $state['error'] = 'Invalid or expired verification link. Please resend.';
            } else {
                delete_transient($key);

                // Check if user exists
                $user = get_user_by('email', $email);

                if ($user) {
                    // Log in user
                    wp_set_current_user($user->ID);
                    wp_set_auth_cookie($user->ID, true);

                    update_user_meta($user->ID, 'energclub_email_verified', 1);

                    wp_safe_redirect(home_url('/dashboard/'));
                    exit;
                } else {
                    // Redirect to register with prefilled verified email
                    wp_safe_redirect(add_query_arg([
                        'email' => rawurlencode($email),
                        'verified' => 1,
                    ], home_url('/register/')));
                    exit;
                }
            }
        }

        ob_start();
        ?>
        <div class="energclub-auth">
            <h1>Login</h1>

            <?php if ($state['message']): ?>
                <div class="energclub-alert energclub-success"><?php echo esc_html($state['message']); ?></div>
            <?php endif; ?>
            <?php if ($state['error']): ?>
                <div class="energclub-alert energclub-error"><?php echo esc_html($state['error']); ?></div>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field('energclub_login', 'energclub_nonce'); ?>
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com" />
                <button type="submit" name="energclub_login_submit" value="1">Send Verification</button>

                <p class="energclub-note">
                    States supported: Verification sent, Invalid/expired link, Resend verification (re-submit email).
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ---------------------------
     * REGISTER: verified email locked + dependent dropdowns
     * --------------------------- */
    public static function registerForm(): string
    {
        // If logged in, go dashboard
        if (is_user_logged_in()) {
            wp_safe_redirect(home_url('/dashboard/'));
            exit;
        }

        $email = sanitize_email($_GET['email'] ?? '');
        $verified = (int)($_GET['verified'] ?? 0) === 1;

        $state = ['error' => '', 'message' => ''];

        if (!$verified || !is_email($email)) {
            $state['error'] = 'Please verify your email from the login page before registration.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['energclub_register_submit']) && !$state['error']) {
            if (!isset($_POST['energclub_nonce']) || !wp_verify_nonce($_POST['energclub_nonce'], 'energclub_register')) {
                $state['error'] = 'Security check failed. Please refresh and try again.';
            } else {
                // Fields
                $full_name = sanitize_text_field($_POST['full_name'] ?? '');
                $last_name = sanitize_text_field($_POST['last_name'] ?? '');
                $mobile = sanitize_text_field($_POST['mobile'] ?? '');
                $country = sanitize_text_field($_POST['country'] ?? '');
                $state_in = sanitize_text_field($_POST['state'] ?? '');

                $community = sanitize_text_field($_POST['community'] ?? '');
                $sub_community = sanitize_text_field($_POST['sub_community'] ?? '');
                $industry = sanitize_text_field($_POST['industry'] ?? '');
                $sub_industry = sanitize_text_field($_POST['sub_industry'] ?? '');

                $area = \Energ\Frontend\EnergClubData::areaOfIndustry($community, $sub_community);

                // Minimal validation
                if ($full_name === '' || $last_name === '' || $country === '') {
                    $state['error'] = 'Please fill all required fields.';
                } else {
                    // Create WP user (password auto; you can enforce later)
                    $username = sanitize_user(current(explode('@', $email)), true);
                    if (username_exists($username)) {
                        $username .= '_' . wp_rand(100, 999);
                    }

                    $user_id = wp_create_user($username, wp_generate_password(20, true), $email);

                    if (is_wp_error($user_id)) {
                        $state['error'] = $user_id->get_error_message();
                    } else {
                        update_user_meta($user_id, 'energclub_email_verified', 1);
                        update_user_meta($user_id, 'energclub_membership_status', 'pending'); // approval flow

                        update_user_meta($user_id, 'energclub_full_name', $full_name);
                        update_user_meta($user_id, 'energclub_last_name', $last_name);
                        update_user_meta($user_id, 'energclub_mobile', $mobile);
                        update_user_meta($user_id, 'energclub_country', $country);
                        update_user_meta($user_id, 'energclub_state', $state_in);

                        update_user_meta($user_id, 'energclub_community', $community);
                        update_user_meta($user_id, 'energclub_sub_community', $sub_community);
                        update_user_meta($user_id, 'energclub_industry', $industry);
                        update_user_meta($user_id, 'energclub_sub_industry', $sub_industry);
                        update_user_meta($user_id, 'energclub_area_of_industry', $area);

                        // Auto-login after registration (still gated by pending status on dashboard)
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id, true);

                        $state['message'] = 'Registration successful. Your access is pending approval.';
                        wp_safe_redirect(home_url('/dashboard/'));
                        exit;
                    }
                }
            }
        }

        $communities = \Energ\Frontend\EnergClubData::communities();
        $industries  = \Energ\Frontend\EnergClubData::industries();

        ob_start();
        ?>
        <div class="energclub-auth">
            <h1>Register</h1>

            <?php if ($state['error']): ?>
                <div class="energclub-alert energclub-error"><?php echo esc_html($state['error']); ?></div>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field('energclub_register', 'energclub_nonce'); ?>

                <div class="energclub-grid">
                    <div>
                        <label>Full Name</label>
                        <input name="full_name" required />
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input name="last_name" required />
                    </div>

                    <div>
                        <label>Email (Verified)</label>
                        <input value="<?php echo esc_attr($email); ?>" disabled />
                    </div>
                    <div>
                        <label>Mobile Number</label>
                        <input name="mobile" />
                    </div>

                    <div>
                        <label>Country</label>
                        <input name="country" required />
                    </div>
                    <div>
                        <label>State</label>
                        <input name="state" />
                    </div>

                    <div>
                        <label>Community</label>
                        <select name="community" id="energclub_community" required>
                            <option value="">Select</option>
                            <?php foreach ($communities as $k => $v): ?>
                                <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Sub-Community</label>
                        <select name="sub_community" id="energclub_sub_community" required disabled>
                            <option value="">Select community first</option>
                        </select>
                    </div>

                    <div>
                        <label>Industry</label>
                        <select name="industry" id="energclub_industry" required>
                            <option value="">Select</option>
                            <?php foreach ($industries as $k => $v): ?>
                                <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Sub-Industry</label>
                        <select name="sub_industry" id="energclub_sub_industry" required disabled>
                            <option value="">Select industry first</option>
                        </select>
                    </div>

                    <div class="energclub-colspan">
                        <label>Area of Industry (Auto-mapped)</label>
                        <input id="energclub_area_of_industry" value="" disabled />
                        <p class="energclub-note">
                            This will drive dashboard Intelligence modules (personalization).
                        </p>
                    </div>
                </div>

                <button type="submit" name="energclub_register_submit" value="1">Create Profile</button>

                <p class="energclub-note">
                    Post-registration: Pending Access Approval → Admin Review → Access Granted → Dashboard Enabled.
                </p>
            </form>

            <script>
              window.ENERGCLUB_DATA = {
                communities: <?php echo wp_json_encode($communities); ?>,
                industries: <?php echo wp_json_encode($industries); ?>,
                areaMapEndpoint: "<?php echo esc_url(admin_url('admin-ajax.php?action=energclub_area_map')); ?>"
              };
            </script>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ---------------------------
     * DASHBOARD: top bar + second header nav + personalization + gating
     * --------------------------- */
    public static function dashboard(): string
    {
        if (!is_user_logged_in()) {
            wp_safe_redirect(home_url('/login/'));
            exit;
        }

        $user_id = get_current_user_id();
        $status = get_user_meta($user_id, 'energclub_membership_status', true) ?: 'pending';

        // Approval gating
        if ($status !== 'active') {
            ob_start();
            ?>
            <div class="energclub-gate">
                <h1>Access Pending</h1>
                <p>Your profile is created. Access is currently pending approval.</p>
                <p><strong>Status:</strong> <?php echo esc_html(ucfirst($status)); ?></p>
                <p><a class="energclub-link" href="<?php echo esc_url(home_url('/')); ?>">Go to Main Site</a></p>
                <p><a class="energclub-link" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Logout</a></p>
            </div>
            <?php
            return ob_get_clean();
        }

        // Personalization hooks
        $community = get_user_meta($user_id, 'energclub_community', true);
        $sub = get_user_meta($user_id, 'energclub_sub_community', true);
        $area = get_user_meta($user_id, 'energclub_area_of_industry', true);

        $display_name = wp_get_current_user()->display_name ?: 'Member';

        ob_start();
        ?>
        <div class="energclub-shell">
            <!-- TOP BAR -->
            <header class="energclub-topbar">
                <div class="energclub-brand">
                    <a href="<?php echo esc_url(home_url('/dashboard/')); ?>">ENERGCLUB</a>
                </div>

                <div class="energclub-search">
                    <input type="search" placeholder="Search (Future Agenda)" disabled />
                </div>

                <div class="energclub-actions">
                    <button class="energclub-icon" type="button" title="Notifications" disabled>🔔</button>

                    <div class="energclub-profile">
                        <button class="energclub-avatar" type="button" id="energclubProfileBtn">
                            <?php echo esc_html(strtoupper(substr($display_name, 0, 1))); ?>
                        </button>
                        <div class="energclub-dropdown" id="energclubProfileMenu" style="display:none;">
                            <a href="<?php echo esc_url(home_url('/dashboard/?tab=profile')); ?>">My Profile</a>
                            <a href="<?php echo esc_url(home_url('/dashboard/?tab=settings')); ?>">Account Settings</a>
                            <a href="<?php echo esc_url(home_url('/dashboard/?tab=membership')); ?>">Membership Status</a>
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- SECOND HEADER NAV (instead of sidebar) -->
            <nav class="energclub-nav">
                <a href="<?php echo esc_url(home_url('/dashboard/')); ?>" class="<?php echo empty($_GET['tab']) ? 'is-active' : ''; ?>">Dashboard</a>
                <a href="<?php echo esc_url(home_url('/dashboard/?tab=intelligence')); ?>" class="<?php echo (($_GET['tab'] ?? '')==='intelligence') ? 'is-active' : ''; ?>">Intelligence</a>
                <a href="<?php echo esc_url(home_url('/dashboard/?tab=subscriptions')); ?>" class="<?php echo (($_GET['tab'] ?? '')==='subscriptions') ? 'is-active' : ''; ?>">Subscriptions</a>
                <a href="<?php echo esc_url(home_url('/dashboard/?tab=events')); ?>" class="<?php echo (($_GET['tab'] ?? '')==='events') ? 'is-active' : ''; ?>">Events</a>
                <a href="<?php echo esc_url(home_url('/dashboard/?tab=bookmarks')); ?>" class="<?php echo (($_GET['tab'] ?? '')==='bookmarks') ? 'is-active' : ''; ?>">Bookmarks</a>
                <a href="<?php echo esc_url(home_url('/dashboard/?tab=settings')); ?>" class="<?php echo (($_GET['tab'] ?? '')==='settings') ? 'is-active' : ''; ?>">Account Settings</a>

                <span class="energclub-nav-spacer"></span>
                <a class="energclub-main-site" href="<?php echo esc_url(home_url('/')); ?>">Go to Main Site</a>
            </nav>

            <!-- MAIN CONTENT -->
            <main class="energclub-main">
                <?php echo self::renderDashboardTab($user_id, $community, $sub, $area); ?>
            </main>
        </div>

        <script>
          (function(){
            const btn = document.getElementById('energclubProfileBtn');
            const menu = document.getElementById('energclubProfileMenu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function(){
              menu.style.display = (menu.style.display === 'none' || !menu.style.display) ? 'block' : 'none';
            });
            document.addEventListener('click', function(e){
              if (!menu.contains(e.target) && !btn.contains(e.target)) menu.style.display = 'none';
            });
          })();
        </script>
        <?php
        return ob_get_clean();
    }

    private static function renderDashboardTab(int $user_id, string $community, string $sub, string $area): string
    {
        $tab = sanitize_text_field($_GET['tab'] ?? '');

        ob_start();

        if ($tab === '' || $tab === 'home') {
            ?>
            <section class="energclub-card">
                <h2>Dashboard Home</h2>
                <p>This is the base layout window. Intelligence layers will be added (Future Agenda).</p>
                <p><strong>Personalization:</strong> <?php echo esc_html($area ?: 'Not set'); ?></p>
            </section>

            <div class="energclub-columns">
                <section class="energclub-card">
                    <h3>Intelligence Feed (Phase 1)</h3>
                    <ul>
                        <li>Editorial articles</li>
                        <li>Insight notes</li>
                        <li>Policy explainers</li>
                        <li>Industry deep dives</li>
                    </ul>
                    <p class="energclub-note">Will be filtered based on Community/Sub-Community/Area.</p>
                </section>

                <section class="energclub-card">
                    <h3>Trending This Week</h3>
                    <ul>
                        <li>Most-read articles</li>
                        <li>Popular insights/news</li>
                        <li>Active discussions (later)</li>
                    </ul>
                </section>

                <section class="energclub-card">
                    <h3>Community Highlights</h3>
                    <ul>
                        <li>Featured contributors</li>
                        <li>Expert comments</li>
                        <li>Active discussions (later)</li>
                    </ul>
                </section>
            </div>
            <?php
        } elseif ($tab === 'intelligence') {
            ?>
            <section class="energclub-card">
                <h2>Intelligence</h2>
                <div class="energclub-pills">
                    <span>Editorial Analysis</span>
                    <span>Policy & Regulation</span>
                    <span>Market & Industry</span>
                    <span>Technology & Innovation</span>
                </div>
                <p class="energclub-note">Next step: query posts by taxonomy mapped from user meta.</p>
            </section>
            <?php
        } elseif ($tab === 'events') {
            ?>
            <section class="energclub-card">
                <h2>Events</h2>
                <ul>
                    <li>Upcoming Events</li>
                    <li>Webinars & digital dialogues</li>
                </ul>
            </section>
            <?php
        } elseif ($tab === 'bookmarks') {
            ?>
            <section class="energclub-card">
                <h2>Bookmarks</h2>
                <p>Saved articles and bookmarked discussions will appear here.</p>
            </section>
            <?php
        } elseif ($tab === 'settings') {
            ?>
            <section class="energclub-card">
                <h2>Account Settings</h2>
                <p>Profile information, interests & sectors, notifications.</p>
            </section>
            <?php
        } else {
            ?>
            <section class="energclub-card">
                <h2><?php echo esc_html(ucfirst($tab)); ?></h2>
                <p>Section is under construction.</p>
            </section>
            <?php
        }

        return ob_get_clean();
    }
}
