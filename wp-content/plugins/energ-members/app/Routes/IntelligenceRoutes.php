<?php

namespace Energ\Routes;

use WP_Query;
use Energ\Middleware\JwtAuth;

class IntelligenceRoutes
{
    public static function register()
    {
        register_rest_route('energ/v1', '/intelligence', [
            'methods'  => 'GET',
            'callback' => [self::class, 'handle'],
            'permission_callback' => [JwtAuth::class, 'allow'],
        ]);
    }

    public static function handle($request)
{
    global $wpdb;

    $email = $request->get_param('auth_identifier');
    if (!$email) return [];

    // 1️⃣ Get user communities
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT community FROM {$wpdb->prefix}energ_members WHERE email = %s",
            $email
        ),
        ARRAY_A
    );

    if (empty($row['community'])) return [];

    $sector_slugs = array_map('trim', explode(',', $row['community']));

    // 2️⃣ Resolve sector terms
    $sectors = get_terms([
        'taxonomy'   => 'sector',
        'slug'       => $sector_slugs,
        'hide_empty' => false,
    ]);

    if (empty($sectors) || is_wp_error($sectors)) return [];

    $response = [];

    // 3️⃣ LOOP PER COMMUNITY 🔥
    foreach ($sectors as $sector) {

        $articles = new WP_Query([
            'post_type'      => 'articles',
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'tax_query'      => [
                [
                    'taxonomy' => 'sector',
                    'terms'    => [$sector->term_id],
                ]
            ]
        ]);

        $items = [];

        while ($articles->have_posts()) {
            $articles->the_post();
            $items[] = [
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'url'   => get_permalink(),
            ];
        }

        wp_reset_postdata();

        if (!empty($items)) {
            $response[] = [
                'sector'   => $sector->name,
                'articles' => $items,
            ];
        }
    }

    return rest_ensure_response($response);
}

    private static function get_sector_name($post_id)
    {
        $terms = get_the_terms($post_id, 'sector');
        return $terms && !is_wp_error($terms) ? $terms[0]->name : '';
    }
}
