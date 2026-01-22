// ===========================
// MAIN FILTER AJAX FUNCTION
// ===========================
function filter_by_taxonomies_ajax() {
    $paged = !empty($_POST['paged']) ? intval($_POST['paged']) : 1;

    // Detect current post type context from referrer or fallback
    $referrer = wp_get_referer();
    $post_type = 'news'; // default fallback

    if ($referrer) {
    if (strpos($referrer, '/reports') !== false) {
        $post_type = 'reports';
    } elseif (strpos($referrer, '/opinion') !== false) {
        $post_type = 'opinion';
    } elseif (strpos($referrer, '/magazine') !== false) {
        $post_type = 'magazine';
    } elseif (strpos($referrer, '/videos') !== false) {
        $post_type = 'videos';
    } elseif (strpos($referrer, '/events') !== false) {
        $post_type = 'events';
    } elseif (strpos($referrer, '/cover-story') !== false) {
        $post_type = 'cover-story';
    } elseif (strpos($referrer, '/sector') !== false) {
        // Multiple post types for Sector pages
        $post_type = ['articles', 'cover-story', 'news', 'reports', 'magazine', 'opinion', 'videos', 'events'];
    }
}

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => 20,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ];


    $tax_query = ['relation' => 'AND'];

    // Note: Fix taxonomy slugs as per your actual registration
    $taxonomies = ['sector', 'content_type', 'tags', 'worlds', 'states'];

    foreach ($taxonomies as $tax) {
        if (!empty($_POST[$tax])) {
            $tax_query[] = [
                'taxonomy' => $tax,
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_POST[$tax]),
            ];
        }
    }

    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    if (!empty($_POST['author'])) {
        $args['author'] = intval($_POST['author']);
    }

    if (!empty($_POST['from']) && !empty($_POST['to'])) {
        $args['date_query'] = [
            [
                'after'     => sanitize_text_field($_POST['from']),
                'before'    => sanitize_text_field($_POST['to']),
                'inclusive' => true,
            ]
        ];
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
    echo '<div class="filtered-list">';

    while ($query->have_posts()) {
        $query->the_post();

        $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
        $author = get_the_author();
        $date = get_the_date('F j, Y');
        $categories = get_the_terms(get_the_ID(), 'sector');
        $category_name = !empty($categories) ? esc_html($categories[0]->name) : '';

        echo '<article class="news-card">';

        // Thumbnail + Content Type Badge
        if ($thumbnail) {
            echo '<div class="news-thumb">';

            // Get "Type of Content" term dynamically
            $content_types = get_the_terms(get_the_ID(), 'content-type');
            if (!empty($content_types) && !is_wp_error($content_types)) {
                $type_name = esc_html($content_types[0]->name);
                $type_slug = esc_attr($content_types[0]->slug);
                echo '<span class="content-type-badge ' . $type_slug . '">' . $type_name . '</span>';
            }

            // Post Thumbnail
            echo '<a href="' . esc_url(get_permalink()) . '">';
            echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr(get_the_title()) . '">';
            echo '</a>';

            echo '</div>'; // end .news-thumb
        }

        // Title
        echo '<h2 class="news-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2>';

        // Meta Info
        echo '<div class="news-meta">';
        echo '<span class="meta-author">By ' . esc_html($author) . '</span>';
        echo ' | <span class="meta-date">' . esc_html($date) . '</span>';
        if ($category_name) {
            echo ' | <span class="meta-category">' . $category_name . '</span>';
        }
        echo '</div>'; // end .news-meta

        echo '</article>';
    }

    echo '</div>'; // end .filtered-list

} else {
    echo '<p class="nrf">No results found.</p>';
}

wp_reset_postdata();
wp_die();
} 
add_action('wp_ajax_filter_by_taxonomies', 'filter_by_taxonomies_ajax'); 
add_action('wp_ajax_nopriv_filter_by_taxonomies', 'filter_by_taxonomies_ajax');

// =============================================
// NEW AJAX FUNCTION: DYNAMIC FILTER DISABLER
// =============================================
function energ_dynamic_filter_update() {
    $post_type = 'news';

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    ];

    $taxonomies = ['sector', 'content_type', 'tags', 'worlds', 'states'];
    $tax_query = ['relation' => 'AND'];

    foreach ($taxonomies as $tax) {
        if (!empty($_POST[$tax])) {
            $tax_query[] = [
                'taxonomy' => $tax,
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_POST[$tax]),
            ];
        }
    }

    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);
    $available_terms = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            foreach ($taxonomies as $tax) {
                $terms = get_the_terms(get_the_ID(), $tax);
                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $available_terms[$tax][$term->slug] = $term->name;
                    }
                }
            }
        endwhile;
        wp_reset_postdata();
    }

    wp_send_json_success($available_terms);
}
add_action('wp_ajax_energ_dynamic_filter_update', 'energ_dynamic_filter_update');
add_action('wp_ajax_nopriv_energ_dynamic_filter_update', 'energ_dynamic_filter_update');



// ===================================
// ENQUEUE FILTER SCRIPTS & STYLES
// ===================================
require get_template_directory() . '/energ-filters/taxonomy-filters.php';

function enqueue_taxonomy_filters_assets() {
    wp_enqueue_script(
        'taxonomy-filters-js',
        get_template_directory_uri() . '/energ-filters/taxonomy-filters.js',
        ['jquery'],
        time(),
        true
    );

    wp_enqueue_style(
        'taxonomy-filters-css',
        get_template_directory_uri() . '/energ-filters/taxonomy-filters.css',
        [],
        time()
    );

    wp_localize_script('taxonomy-filters-js', 'ajax_object', [
        'ajaxurl' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_taxonomy_filters_assets');

// Load CSS only for taxonomy-sector.php
function energ_sector_styles() {
    if (is_tax('sector')) {
        wp_enqueue_style(
            'taxonomy-sector-css',
            get_template_directory_uri() . '/assets/css/taxonomy-sector.css',
            [],
            time()
        );
    }
}
add_action('wp_enqueue_scripts', 'energ_sector_styles');

function energ_enqueue_cover_story_css() {
    if (is_singular('cover-story')) {
        wp_enqueue_style(
            'cover-story-css',
            get_stylesheet_directory_uri() . '/assets/css/cover-story.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/cover-story.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'energ_enqueue_cover_story_css');

function iten_load_article_css() {
    if (is_singular('articles')) {
        wp_enqueue_style(
            'article-css',
            get_template_directory_uri() . '/assets/css/articles.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/articles.css')
        );
    }

}
add_action('wp_enqueue_scripts', 'iten_load_article_css');

function iten_load_interviews_videos_css() {
    if (is_singular('interviews')) {
        wp_enqueue_style(
            'article-css',
            get_template_directory_uri() . '/assets/css/interviews-videos.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/interviews-videos.css')
        );
    }

}
add_action('wp_enqueue_scripts', 'iten_load_interviews_videos_css');

/**
 * Check if user can access this content
 */
function energ_user_can_access($post_id) {

    // FIX 1: ACF returns array → convert to string
    $access = get_field('access_type', $post_id);
    if (is_array($access)) {
        $access = $access[0];
    }

    // FIX 2: NEW MATCHING VALUES (your ACF choices)
    // free
    // signup_required
    // subscription
    // paid

    // FREE CONTENT → always visible
    if ($access === 'free' || !$access) {
        return true;
    }

    // NOT LOGGED IN → deny for non-free
    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();

    // SIGNUP REQUIRED → any logged-in user can view
    if ($access === 'signup_required') {
        return true;
    }

    // SUBSCRIPTION CONTENT
    if ($access === 'subscription') {

        // user meta for subscriber
        $has_subscription = get_user_meta($user->ID, 'is_subscriber', true);

        if ($has_subscription == 'yes') {
            return true;
        }

        return false;
    }

    // PAID CONTENT (article purchase)
    if ($access === 'paid') {

        // user meta: purchased_article_123
        $purchased = get_user_meta($user->ID, 'purchased_article_' . $post_id, true);

        if ($purchased == 'yes') {
            return true;
        }

        return false;
    }

    return false;
}



/* ---------- RAZORPAY HELPERS ---------- */
function energ_rzp_api_request($endpoint, $method = 'POST', $body = []) {
    $key = defined('rzp_test_Rh6P19Rb4YqbSN') ? RZP_KEY_ID : '';
    $secret = defined('9ysZcBklM1fmfnynvDWyylPb') ? RZP_KEY_SECRET : '';

    $url = 'https://api.razorpay.com/v1/' . ltrim($endpoint, '/');

    $ch = curl_init($url);
    $headers = ['Content-Type: application/json'];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    } elseif ($method === 'GET') {
        // nothing
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($body)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'body' => json_decode($resp, true), 'raw' => $resp];
}

/* Create subscription server-side (requires plan_id) */
function energ_rzp_create_subscription($plan_id, $customer_notify = 1, $total_count = 12, $customer = []) {
    $body = [
        'plan_id' => $plan_id,
        'customer_notify' => $customer_notify,
        'total_count' => $total_count
    ];
    if (!empty($customer)) $body['customer'] = $customer;
    return energ_rzp_api_request('subscriptions', 'POST', $body);
}

/* Create order for one-time payment */
function energ_rzp_create_order($amount_in_rupees, $currency = 'INR', $receipt = '') {
    $amount = intval(round($amount_in_rupees * 100)); // paise
    $body = [
        'amount' => $amount,
        'currency' => $currency,
        'receipt' => $receipt,
        'payment_capture' => 1
    ];
    return energ_rzp_api_request('orders', 'POST', $body);
}

/* Verify webhook signature */
function energ_rzp_verify_webhook($payload, $signature) {
    if (!defined('RZP_WEBHOOK_SECRET') || empty(RZP_WEBHOOK_SECRET)) return false;
    $computed = hash_hmac('sha256', $payload, RZP_WEBHOOK_SECRET);
    return hash_equals($computed, $signature);
}

/* Send transactional email */
function energ_send_mail($to, $subject, $message) {
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($to, $subject, $message, $headers);
}

// AJAX handler to create subscription server-side

add_action('wp_ajax_energ_create_subscription', 'energ_create_subscription_ajax');
function energ_create_subscription_ajax() {
    // must be logged in
    if (!is_user_logged_in()) {
        wp_send_json(['error' => 'Login required'], 403);
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $plan_id = sanitize_text_field($input['plan_id'] ?? '');

    if (empty($plan_id)) wp_send_json(['error' => 'Plan ID missing'], 400);

    // optional: pass customer info to Razorpay
    $user = wp_get_current_user();
    $customer = [
        'name' => $user->display_name,
        'email' => $user->user_email,
    ];

    // create subscription (server to Razorpay)
    $resp = energ_rzp_create_subscription($plan_id, 1, 12, $customer);
    if ($resp['code'] !== 200 && $resp['code'] !== 201) {
        wp_send_json(['error' => 'Razorpay error', 'detail' => $resp['body'] ?? $resp['raw']], 500);
    }

    $subscription = $resp['body'];
    // return subscription id to client
    wp_send_json(['subscription_id' => $subscription['id'], 'subscription' => $subscription], 200);
}

// ENDPOINTS WHEBOOK

add_action('rest_api_init', function () {
    register_rest_route('energ/v1', '/rzp-webhook', [
        'methods'  => 'POST',
        'callback' => 'energ_rzp_webhook_handler',
        'permission_callback' => '__return_true'
    ]);
});

function energ_rzp_webhook_handler(WP_REST_Request $request) {
    $payload = $request->get_body();
    $signature = $request->get_header('x-razorpay-signature') ?? '';

    if (!energ_rzp_verify_webhook($payload, $signature)) {
        return new WP_REST_Response(['error' => 'Invalid signature'], 400);
    }

    $data = json_decode($payload, true);
    $event = $data['event'] ?? '';

    // Handle subscription activated
    if ($event === 'subscription.charged' || $event === 'subscription.activated') {
        $subscription = $data['payload']['subscription']['entity'] ?? null;
        if ($subscription) {
            $plan_id = $subscription['plan_id'] ?? '';
            $sub_id = $subscription['id'] ?? '';
            $customer_id = $subscription['customer_id'] ?? '';

            // Try to find WP user by email (customer entity might be empty). We'll use subscription.customer.email if present
            // But better: when creating subscription, we passed customer email, Razorpay created customer. For simplicity:
            $customer = $subscription['customer_id'] ?? false;

            // If customer email present:
            $customer_email = $subscription['customer_email'] ?? ($subscription['customer'] ?? null);
            // We attempt to map via customer email sent in payload (some events include it)
            $email = '';
            if (!empty($data['payload']['subscription']['entity']['customer_email'])) $email = $data['payload']['subscription']['entity']['customer_email'];

            if ($email) {
                $user = get_user_by('email', $email);
                if ($user) {
                    update_user_meta($user->ID, 'is_subscriber', 'yes');
                    update_user_meta($user->ID, 'subscription_id', $sub_id);
                    update_user_meta($user->ID, 'subscription_status', 'active');

                    // Send email
                    $subject = "Your subscription is active";
                    $message = "<p>Hi {$user->display_name},</p>
                                <p>Your subscription is now active. Subscription ID: {$sub_id}</p>
                                <p>Thanks,</p><p>" . get_bloginfo('name') . "</p>";
                    energ_send_mail($user->user_email, $subject, $message);
                }
            }
        }
    }

    // Handle payment capture for orders (one-time purchases)
    if ($event === 'payment.captured' || $event === 'order.paid') {
        // get payment entity
        $payment = $data['payload']['payment']['entity'] ?? null;
        if ($payment) {
            $notes = $payment['notes'] ?? [];
            // We used receipt to include article id: 'article_{post}_user_{id}_{ts}'
            $order_id = $payment['order_id'] ?? '';
            // fetch order details to get receipt:
            // call Razorpay orders API
            $order_resp = energ_rzp_api_request('orders/' . $order_id, 'GET', []);
            $receipt = $order_resp['body']['receipt'] ?? '';

            // parse article id from receipt if pattern matches
            if (preg_match('/article_(\d+)_user_(\d+)_/', $receipt, $m)) {
                $post_id = intval($m[1]);
                $user_id = intval($m[2]);
                if ($post_id && $user_id) {
                    update_user_meta($user_id, 'purchased_article_' . $post_id, 'yes');

                    // add billing history entry
                    $billing = get_user_meta($user_id, 'billing_history', true);
                    if (empty($billing)) $billing = [];
                    $billing[] = [
                        'type' => 'purchase',
                        'post_id' => $post_id,
                        'amount' => ($payment['amount'] / 100),
                        'currency' => $payment['currency'],
                        'payment_id' => $payment['id'],
                        'created_at' => time()
                    ];
                    update_user_meta($user_id, 'billing_history', $billing);

                    // send purchase email
                    $user = get_userdata($user_id);
                    if ($user) {
                        $subject = "Article unlocked: " . get_the_title($post_id);
                        $link = get_permalink($post_id);
                        $message = "<p>Hi {$user->display_name},</p>
                                    <p>Your purchase for <strong>" . get_the_title($post_id) . "</strong> is successful.</p>
                                    <p><a href='{$link}'>Click to read the article</a></p>";
                        energ_send_mail($user->user_email, $subject, $message);
                    }
                }
            }
        }
    }

    return new WP_REST_Response(['success'=>true], 200);
}


// Account creation & email on registration

add_action('user_register', 'energ_on_user_register', 10, 1);
function energ_on_user_register($user_id) {
    $user = get_userdata($user_id);
    $subject = "Welcome to " . get_bloginfo('name');
    $message = "<p>Hi {$user->display_name},</p>
                <p>Thanks for creating an account at " . get_bloginfo('name') . ".</p>
                <p>You can login <a href='" . wp_login_url() . "'>here</a>.</p>";
    energ_send_mail($user->user_email, $subject, $message);
}