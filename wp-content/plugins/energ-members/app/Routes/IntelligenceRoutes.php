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

        // ✅ correct JWT param
        $authUser = $request->get_param('auth_user');
        if (!$authUser) {
            return rest_ensure_response([]);
        }

        // ✅ resolve member safely
        if (is_numeric($authUser)) {
            $where = $wpdb->prepare("user_id = %d", (int) $authUser);
        } elseif (is_email($authUser)) {
            $where = $wpdb->prepare("email = %s", $authUser);
        } else {
            $where = $wpdb->prepare("phone = %s", $authUser);
        }

        // ✅ read SOURCE OF TRUTH
        $row = $wpdb->get_row(
            "SELECT communities_json FROM {$wpdb->prefix}energ_members WHERE {$where} LIMIT 1",
            ARRAY_A
        );

        if (empty($row['communities_json'])) {
            return rest_ensure_response([]);
        }

        $communities = json_decode($row['communities_json'], true);
        if (!is_array($communities) || empty($communities)) {
            return rest_ensure_response([]);
        }

        // ✅ normalize to taxonomy slugs
        $sectorSlugs = array_map(
            fn($c) => sanitize_title($c),
            $communities
        );

        // ✅ fetch sector terms
        $sectors = get_terms([
            'taxonomy'   => 'sector',
            'slug'       => $sectorSlugs,
            'hide_empty' => false,
        ]);

        if (empty($sectors) || is_wp_error($sectors)) {
            return rest_ensure_response([]);
        }

        $response = [];

        foreach ($sectors as $sector) {

            $articles = new WP_Query([
                'post_type'      => 'articles',
                'post_status'    => 'publish',
                'posts_per_page' => 5,
                'tax_query'      => [
                    [
                        'taxonomy' => 'sector',
                        'terms'    => [$sector->term_id],
                        'field'    => 'term_id',
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
}
