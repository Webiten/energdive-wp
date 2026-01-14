<?php
// =============================================================
// THEME ASSETS (ENQUEUE - CLEAN)
// =============================================================
add_action('wp_enqueue_scripts', function () {
    $theme_dir  = get_template_directory();
    $theme_uri  = get_template_directory_uri();

    // --- Ensure files exist before using filemtime ---
    $app_css_file = $theme_dir . '/assets/css/app.min.css';
    $app_css_ver  = file_exists($app_css_file) ? filemtime($app_css_file) : false;

    $app_js_file  = $theme_dir . '/assets/js/app.min.js';
    $app_js_ver   = file_exists($app_js_file) ? filemtime($app_js_file) : false;

    $main_js_file = $theme_dir . '/assets/js/main.js';
    $main_js_ver  = file_exists($main_js_file) ? filemtime($main_js_file) : false;

    // 1) app.min.css (library CSS)
    if ( file_exists($app_css_file) ) {
        wp_enqueue_style('iten-app-css', $theme_uri . '/assets/css/app.min.css', [], $app_css_ver);
    }

    // 2) slick (from CDN)
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');

    // 3) main theme style.css (root). Make it depend on app-css so it loads AFTER app.min.css
    wp_enqueue_style('iten-style', get_stylesheet_uri(), ['iten-app-css', 'slick-css'], filemtime($theme_dir . '/style.css'));

    // Fontawesome + Google fonts (external)
    wp_enqueue_style('iten-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
    wp_enqueue_style('iten-googlefonts', 'https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap', [], null);

    // ---------- JS ----------
    // ensure WP's jquery (registered) is available as dependency
    wp_enqueue_script('jquery');

    // slick
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);

    // app.min.js
    if ( file_exists($app_js_file) ) {
        wp_enqueue_script('iten-app', $theme_uri . '/assets/js/app.min.js', ['jquery', 'slick-js'], $app_js_ver, true);
    }

    // main.js (theme logic) — depends on app
    if ( file_exists($main_js_file) ) {
        wp_enqueue_script('iten-main', $theme_uri . '/assets/js/main.js', ['jquery', 'iten-app'], $main_js_ver, true);
    }

    // Localize ajaxurl if you need it
    wp_localize_script('iten-main', 'ajax_object', ['ajaxurl' => admin_url('admin-ajax.php')]);

}, 20);

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
// NAV MENUS (Dynamic Header + Mobile Menu)
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

add_filter('elementor_pro/post_info/author', function($author) {
    $author_id = get_field('select_author');
    if ($author_id) {
        return get_the_title($author_id); // return ACF Author CPT name
    }
    return $author; // fallback to WP default
});


// =============================
// FEATURED VIDEO
// =============================
add_filter('sv_featured_video_post_types', function($types){
    $types[] = 'interviews';
    $types[] = 'videos';
    return $types;
});




// ====================================================================
//  CONTENT ACCESS RULES (subscription, paid etc)
// ====================================================================
function energ_user_can_access($post_id) {

    $access = get_field('access_type', $post_id);
    if (is_array($access)) $access = $access[0];

    if ($access === 'free' || !$access) {
        return true;
    }

    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();

    if ($access === 'signup_required') {
        return true;
    }

    if ($access === 'subscription') {
        return get_user_meta($user->ID, 'is_subscriber', true) === 'yes';
    }

    if ($access === 'paid') {
        return get_user_meta($user->ID, 'purchased_article_' . $post_id, true) === 'yes';
    }

    return false;
}


// ====================================================================
//  ARTICLE CSS LOADING
// ====================================================================
add_action('wp_enqueue_scripts', function () {
    if (is_singular('articles')) {
        wp_enqueue_style(
            'article-css',
            get_template_directory_uri() . '/assets/css/articles.css',
            [],
            filemtime(get_template_directory() . '/assets/css/articles.css')
        );
    }
});

add_action('wp_enqueue_scripts', function () {
    if (is_singular('news')) {
        wp_enqueue_style(
            'news-css',
            get_template_directory_uri() . '/assets/css/news.css',
            [],
            filemtime(get_template_directory() . '/assets/css/news.css')
        );
    }
});

add_action('wp_enqueue_scripts', function () {
    if (is_singular('case-study')) {
        wp_enqueue_style(
            'news-css',
            get_template_directory_uri() . '/assets/css/case-study.css',
            [],
            filemtime(get_template_directory() . '/assets/css/case-study.css')
        );
    }
});

add_action('wp_enqueue_scripts', function() {
    if (is_singular('cover-story')) {
        wp_enqueue_style(
            'news-css',
            get_template_directory_uri() . '/assets/css/cover-story.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/cover-story.css')
        );
    }
});

add_action('wp_enqueue_scripts', function() {
    if (is_singular('opinion')) {
        wp_enqueue_style(
            'news-css',
            get_template_directory_uri() . '/assets/css/cover-story.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/cover-story.css')
        );
    }
});

add_action('wp_enqueue_scripts', function() {
    if (is_singular('reports')) {
        wp_enqueue_style(
            'news-css',
            get_template_directory_uri() . '/assets/css/cover-story.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/cover-story.css')
        );
    }
});

add_action('wp_enqueue_scripts', function () {
    if (is_singular('interviews')) {
        wp_enqueue_style(
            'interviews-css',
            get_template_directory_uri() . '/assets/css/interviews-videos.css',
            [],
            filemtime(get_template_directory() . '/assets/css/interviews-videos.css')
        );
    }
});

// 1) Add Meta Box
add_action('add_meta_boxes', function () {
  add_meta_box(
    'videos_youtube_url',
    'YouTube Video URL',
    'videos_youtube_url_metabox_cb',
    'videos',
    'side',
    'default'
  );
});

function videos_youtube_url_metabox_cb($post) {
  wp_nonce_field('videos_youtube_url_nonce_action', 'videos_youtube_url_nonce');

  $value = get_post_meta($post->ID, 'youtube_url', true);
  echo '<p><label for="youtube_url">Paste YouTube URL</label></p>';
  echo '<input type="url" id="youtube_url" name="youtube_url" style="width:100%;" value="' . esc_attr($value) . '" placeholder="https://youtu.be/xxxx or https://www.youtube.com/watch?v=xxxx" />';
}

// 2) Save Meta Box Value
add_action('save_post_videos', function ($post_id) {

  if (!isset($_POST['videos_youtube_url_nonce']) ||
      !wp_verify_nonce($_POST['videos_youtube_url_nonce'], 'videos_youtube_url_nonce_action')) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  if (!current_user_can('edit_post', $post_id)) return;

  $url = isset($_POST['youtube_url']) ? trim($_POST['youtube_url']) : '';

  // Basic validation
  if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
    return; // invalid URL, don't save
  }

  update_post_meta($post_id, 'youtube_url', esc_url_raw($url));
});


// ====================================================================
// ACF AUTHOR FIELD CODE
// ====================================================================
/**
 * Return selected ACF author as WP_Post (handles Post Object / ID / array)
 */
function energ_get_selected_author_post($post_id = 0) {
    if (!$post_id) $post_id = get_the_ID();
    if (!$post_id) return null;

    $acf = get_field('select_author', $post_id);
    if (!$acf) return null;

    // Relationship/array => pick first
    if (is_array($acf)) {
        $acf = reset($acf);
    }

    // If ID => convert to post
    if (is_numeric($acf)) {
        $acf = get_post((int) $acf);
    }

    return ($acf instanceof WP_Post) ? $acf : null;
}

/**
 * Shortcode: ACF author title
 * Usage: [acf_select_author_title]
 */
function acf_select_author_title_shortcode() {
    $author = energ_get_selected_author_post();
    return $author ? esc_html(get_the_title($author->ID)) : '';
}
add_shortcode('acf_select_author_title', 'acf_select_author_title_shortcode');

/**
 * Shortcode: ACF author link (best for Elementor)
 * Usage: [acf_select_author_link]
 */
function acf_select_author_link_shortcode() {
    $author = energ_get_selected_author_post();
    if (!$author) return '';

    $url  = get_permalink($author->ID);
    $name = get_the_title($author->ID);

    return '<a href="' . esc_url($url) . '">' . esc_html($name) . '</a>';
}
add_shortcode('acf_select_author_link', 'acf_select_author_link_shortcode');


/**
 * Override the_author() output (name)
 */
add_filter('the_author', function($author_name){
    $author = energ_get_selected_author_post();
    if (!$author) return $author_name;

    return get_the_title($author->ID);
}, 20);


/**
 * Override clickable author link HTML (themes/Elementor often use this)
 */
add_filter('the_author_posts_link', function($link_html){
    $author = energ_get_selected_author_post();
    if (!$author) return $link_html;

    $url  = get_permalink($author->ID);
    $name = get_the_title($author->ID);

    return '<a href="' . esc_url($url) . '" rel="author">' . esc_html($name) . '</a>';
}, 20);


/**
 * Extra: Override author archive URL (some builders use author_link)
 */
add_filter('author_link', function($link, $author_id, $author_nicename){
    $author = energ_get_selected_author_post();
    if (!$author) return $link;

    return get_permalink($author->ID);
}, 20, 3);


// Create Archive Options Pages for multiple CPTs
add_action('acf/init', function() {

    if( function_exists('acf_add_options_sub_page') ) {

        $cpts = ['opinion', 'news', 'events', 'reports', 'articles']; // <-- add all your CPT slugs here

        foreach ($cpts as $cpt) {
            acf_add_options_sub_page(array(
                'page_title'  => ucfirst($cpt) . ' Archive Settings',
                'menu_title'  => ucfirst($cpt) . ' Archive',
                'parent_slug' => 'edit.php?post_type=' . $cpt,
                'menu_slug'   => $cpt . '-archive-settings',
            ));
        }
    }
});

// ====================================================================
//  MAIN FIX: STOP ARTICLES FROM REDIRECTING TO DASHBOARD
// ====================================================================

// OLD BROKEN REDIRECT REMOVED COMPLETELY ✔
// No redirect will fire on articles now
// News, Reports, Opinion sab safe hai


// ====================================================================
// HTTPS FIX FOR ADMIN URLS
// ====================================================================
add_filter('admin_url', function($url){
    return str_replace("http://", "https://", $url);
});


// ====================================================================
// TAXONOMY FILTER HANDLER
// ====================================================================
require get_template_directory() . '/energ-filters/taxonomy-filters.php';

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

    wp_localize_script('taxonomy-filters-js', 'ajax_object', [
        'ajaxurl' => admin_url('admin-ajax.php'),
    ]);
});



add_action( 'elementor/query/multi_cpt_custom', function( $query ) {

    // Only show specific post IDs:
    $post_ids = [3830, 3395, 505]; // <-- apni 3 IDs yaha daal

    $query->set( 'post__in', $post_ids );

    // Allow posts from ANY CPT
    $query->set( 'post_type', ['articles', 'opinion'] );

    // Order posts in exact same order as IDs
    $query->set( 'orderby', 'post__in' );
});

add_action( 'elementor/query/multi_cpt_custom1', function( $query ) {

    // Only show specific post IDs:
    $post_ids = [3370, 3766, 3908, 3725]; // <-- apni 3 IDs yaha daal

    $query->set( 'post__in', $post_ids );

    // Allow posts from ANY CPT
    $query->set( 'post_type', ['articles', 'opinion'] );

    // Order posts in exact same order as IDs
    $query->set( 'orderby', 'post__in' );
});



add_filter('rest_prepare_post', function ($response, $post, $request) {

    if (has_post_thumbnail($post->ID)) {
        $image = get_the_post_thumbnail_url($post->ID, 'large');
        $response->data['featured_media_url'] = $image;
    } else {
        $response->data['featured_media_url'] = null;
    }

    return $response;

}, 10, 3);



/* ================================================================
   ZOHO-STYLE COMMUNITY (SECTOR) + INDUSTRY AJAX ENDPOINTS
   Used by: complete-registration page
=================================================================== */

/*
|--------------------------------------------------------------------------
| 1) GET SECTOR → PARENT + CHILD GROUPS
|--------------------------------------------------------------------------
| Returns:
| [
|   { parent: {term_id, name}, children: [ {term_id,name}, ... ] },
|   ...
| ]
|--------------------------------------------------------------------------
*/
add_action('wp_ajax_nopriv_energ_get_sector_terms', 'energ_get_sector_terms_ajax');
add_action('wp_ajax_energ_get_sector_terms', 'energ_get_sector_terms_ajax');

function energ_get_sector_terms_ajax() {
    check_ajax_referer('energ_nonce', 'nonce');

    $parents = get_terms([
        'taxonomy'   => 'sector',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ]);

    $out = [];

    if (!is_wp_error($parents) && !empty($parents)) {
        foreach ($parents as $p) {

            $children = get_terms([
                'taxonomy'   => 'sector',
                'hide_empty' => false,
                'parent'     => $p->term_id,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ]);

            $child_list = [];
            if (!is_wp_error($children) && !empty($children)) {
                foreach ($children as $c) {
                    $child_list[] = [
                        'term_id' => (string)$c->term_id,
                        'name'    => $c->name
                    ];
                }
            }

            $out[] = [
                'parent'   => ['term_id' => (string)$p->term_id, 'name' => $p->name],
                'children' => $child_list
            ];
        }
    }

    wp_send_json_success($out);
}



/*
|--------------------------------------------------------------------------
| 2) INDUSTRY → PARENT LIST
|--------------------------------------------------------------------------
| Used to fill Industry dropdown
|--------------------------------------------------------------------------
*/
add_action('wp_ajax_nopriv_energ_get_industry_terms', 'energ_get_industry_terms_ajax');
add_action('wp_ajax_energ_get_industry_terms', 'energ_get_industry_terms_ajax');

function energ_get_industry_terms_ajax() {
    check_ajax_referer('energ_nonce', 'nonce');

    $parents = get_terms([
        'taxonomy'   => 'industry',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ]);

    $out = [];

    if (!is_wp_error($parents) && !empty($parents)) {
        foreach ($parents as $p) {
            $out[] = [
                'term_id' => (string)$p->term_id,
                'name'    => $p->name
            ];
        }
    }

    wp_send_json_success($out);
}

/*
|--------------------------------------------------------------------------
| 3) GENERIC SUB-TERMS (sector or industry children)
|--------------------------------------------------------------------------
| FIXED VERSION — UNIQUE FUNCTION NAME (no conflict with plugin)
|--------------------------------------------------------------------------
*/
add_action('wp_ajax_nopriv_energ_get_sub_terms', 'energ_get_sub_terms_ajax_theme');
add_action('wp_ajax_energ_get_sub_terms', 'energ_get_sub_terms_ajax_theme');

function energ_get_sub_terms_ajax_theme() {

    check_ajax_referer('energ_nonce', 'nonce');

    $taxonomy = sanitize_text_field($_POST['taxonomy'] ?? '');
    $parent   = intval($_POST['parent'] ?? 0);

    if (!$taxonomy) {
        wp_send_json_error("Missing taxonomy");
    }

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'parent'     => $parent,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ]);

    $out = [];

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $t) {
            $out[] = [
                'term_id' => (string)$t->term_id,
                'name'    => $t->name
            ];
        }
    }

    wp_send_json_success($out);
}
	?>