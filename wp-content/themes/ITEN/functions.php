<?php
// =============================================================
// THEME ASSETS (ENQUEUE - CLEAN)
// =============================================================
add_action('wp_enqueue_scripts', function () {
    $theme_dir  = get_template_directory();
    $theme_uri  = get_template_directory_uri();

    $app_css_file = $theme_dir . '/assets/css/app.min.css';
    $app_css_ver  = file_exists($app_css_file) ? filemtime($app_css_file) : false;

    $app_js_file  = $theme_dir . '/assets/js/app.min.js';
    $app_js_ver   = file_exists($app_js_file) ? filemtime($app_js_file) : false;

    $main_js_file = $theme_dir . '/assets/js/main.js';
    $main_js_ver  = file_exists($main_js_file) ? filemtime($main_js_file) : false;

    if (file_exists($app_css_file)) {
        wp_enqueue_style('iten-app-css', $theme_uri . '/assets/css/app.min.css', [], $app_css_ver);
    }

    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');

    wp_enqueue_style(
        'iten-style',
        get_stylesheet_uri(),
        ['iten-app-css', 'slick-css'],
        filemtime($theme_dir . '/style.css')
    );

    wp_enqueue_style('iten-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
    wp_enqueue_style('iten-googlefonts', 'https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700;800&family=Playfair+Display:wght@400..900&display=swap', [], null);

    wp_enqueue_script('jquery');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);

    if (file_exists($app_js_file)) {
        wp_enqueue_script('iten-app', $theme_uri . '/assets/js/app.min.js', ['jquery', 'slick-js'], $app_js_ver, true);
    }

    if (file_exists($main_js_file)) {
        wp_enqueue_script('iten-main', $theme_uri . '/assets/js/main.js', ['jquery', 'iten-app'], $main_js_ver, true);
        wp_localize_script('iten-main', 'ajax_object', ['ajaxurl' => admin_url('admin-ajax.php')]);
    }
}, 20);



// =============================================================
// 🔥 MAIN FIX — DISABLE THEME / ELEMENTOR CSS ON REACT DASHBOARD
// =============================================================
add_action('wp_enqueue_scripts', function () {
    if (is_page('dashboard')) {
        wp_dequeue_style('iten-style');
        wp_dequeue_style('iten-app-css');
        wp_dequeue_style('slick-css');
        wp_dequeue_style('swiper-css');

        wp_dequeue_script('iten-app');
        wp_dequeue_script('iten-main');
    }
}, 99);



// =============================
// ADMIN BAR CLEANUP
// =============================
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});



// =============================
// NAV MENUS
// =============================
add_action('init', function () {
    register_nav_menus([
        'left_menu'   => __('Left Menu', 'iten'),
        'right_menu'  => __('Right Menu', 'iten'),
        'mobile_menu' => __('Mobile Menu', 'iten'),
    ]);
});



// =============================
// SWIPER
// =============================
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
});



// =============================
// CONTENT ACCESS RULES
// =============================
function energ_user_can_access($post_id) {

    $access = get_field('access_type', $post_id);
    if (is_array($access)) $access = $access[0];

    if ($access === 'free' || !$access) return true;
    if (!is_user_logged_in()) return false;

    $user = wp_get_current_user();

    if ($access === 'signup_required') return true;
    if ($access === 'subscription') {
        return get_user_meta($user->ID, 'is_subscriber', true) === 'yes';
    }
    if ($access === 'paid') {
        return get_user_meta($user->ID, 'purchased_article_' . $post_id, true) === 'yes';
    }

    return false;
}



// =============================
// CPT-SPECIFIC CSS
// =============================
add_action('wp_enqueue_scripts', function () {

    $map = [
        'articles'     => 'articles.css',
        'news'         => 'news.css',
        'case-study'   => 'case-study.css',
        'cover-story'  => 'cover-story.css',
        'opinion'      => 'cover-story.css',
        'reports'      => 'cover-story.css',
        'interviews'   => 'interviews-videos.css',
    ];

    foreach ($map as $type => $file) {
        if (is_singular($type)) {
            $path = get_template_directory() . "/assets/css/$file";
            if (file_exists($path)) {
                wp_enqueue_style(
                    "iten-$type-css",
                    get_template_directory_uri() . "/assets/css/$file",
                    [],
                    filemtime($path)
                );
            }
        }
    }
});



// =============================
// SAFE HTTPS ADMIN URL FIX
// =============================
add_filter('admin_url', function ($url) {
    if (is_ssl()) {
        $url = str_replace('http://', 'https://', $url);
    }
    return $url;
});



// =============================
// TAXONOMY FILTER HANDLER
// =============================
$taxonomy_filters = get_template_directory() . '/energ-filters/taxonomy-filters.php';
if (file_exists($taxonomy_filters)) {
    require $taxonomy_filters;
}

add_action('wp_enqueue_scripts', function () {
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
});



// =============================
// REST API FEATURED IMAGE
// =============================
add_filter('rest_prepare_post', function ($response, $post) {
    $response->data['featured_media_url'] = has_post_thumbnail($post->ID)
        ? get_the_post_thumbnail_url($post->ID, 'large')
        : null;
    return $response;
}, 10, 2);
