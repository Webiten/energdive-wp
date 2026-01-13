<?php
/* Template Name: My Account */
get_header();
if (!is_user_logged_in()) { wp_redirect(wp_login_url(get_permalink())); exit; }
$user = wp_get_current_user();
?>

<div class="my-account-wrapper">
    <aside class="my-account-sidebar">
        <ul>
            <li><a href="#profile">Profile</a></li>
            <li><a href="#subscription">Subscription</a></li>
            <li><a href="#purchased">Purchased Articles</a></li>
            <li><a href="#saved">Saved Articles</a></li>
            <li><a href="#billing">Billing History</a></li>
            <li><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
        </ul>
    </aside>

    <main class="my-account-main">
        <section id="profile">
            <h2>Profile</h2>
            <p>Name: <?php echo esc_html($user->display_name); ?></p>
            <p>Email: <?php echo esc_html($user->user_email); ?></p>
            <!-- simple profile update form -->
            <form method="post">
                <?php wp_nonce_field('energ_update_profile', 'energ_update_profile_nonce'); ?>
                <input type="text" name="display_name" value="<?php echo esc_attr($user->display_name); ?>">
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </section>

        <section id="subscription">
            <h2>Subscription</h2>
            <?php
            $sub = get_user_meta($user->ID, 'subscription_status', true);
            $plan = get_user_meta($user->ID, 'subscription_plan', true);
            $sub_id = get_user_meta($user->ID, 'subscription_id', true);
            echo '<p>Status: ' . ($sub ? esc_html($sub) : 'none') . '</p>';
            echo '<p>Plan: ' . ($plan ? esc_html($plan) : '-') . '</p>';
            echo '<p>Subscription ID: ' . ($sub_id ? esc_html($sub_id) : '-') . '</p>';
            ?>
            <a href="<?php echo site_url('/subscribe'); ?>">Change / Subscribe</a>
        </section>

        <section id="purchased">
            <h2>Purchased Articles</h2>
            <?php
            $meta = get_user_meta($user->ID);
            foreach ($meta as $key => $val) {
                if (strpos($key, 'purchased_article_') === 0) {
                    $post_id = intval(str_replace('purchased_article_', '', $key));
                    if ($post_id) {
                        echo '<div><a href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a></div>';
                    }
                }
            }
            ?>
        </section>

        <section id="billing">
            <h2>Billing History</h2>
            <?php
            $billing = get_user_meta($user->ID, 'billing_history', true);
            if ($billing && is_array($billing)) {
                foreach ($billing as $b) {
                    echo '<div>';
                    echo '<strong>'.esc_html($b['type']).'</strong> — ₹'.esc_html($b['amount']).' on '.date('d M Y', $b['created_at']);
                    echo ' (Txn: '.esc_html($b['payment_id'] ?? '-').')';
                    echo '</div>';
                }
            } else {
                echo '<p>No billing records.</p>';
            }
            ?>
        </section>

    </main>
</div>

<?php
// handle profile update submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!wp_verify_nonce($_POST['energ_update_profile_nonce'] ?? '', 'energ_update_profile')) {
        wp_die('Invalid request');
    }
    $new_name = sanitize_text_field($_POST['display_name'] ?? '');
    wp_update_user(['ID' => $user->ID, 'display_name' => $new_name]);
    wp_redirect(get_permalink()); exit;
}
get_footer();