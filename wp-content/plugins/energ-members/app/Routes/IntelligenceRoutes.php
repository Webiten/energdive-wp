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

        if (!$email) {
            return [];
        }

        // 1️⃣ Get user community
        $table = $wpdb->prefix . 'energ_members';
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT community FROM {$table} WHERE email = %s LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        if (!$row || empty($row['community'])) {
            return [];
        }

        // 2️⃣ Community → sector terms
        $sector_slugs = array_map('trim', explode(',', $row['community']));

        $sector_ids = get_terms([
            'taxonomy' => 'sector',
            'slug'     => $sector_slugs,
            'fields'   => 'ids',
        ]);

        if (empty($sector_ids) || is_wp_error($sector_ids)) {
            return [];
        }

        // 3️⃣ Fetch Intelligence posts
        $intelligence = new WP_Query([
            'post_type'      => 'intelligence',
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'tax_query'      => [
                [
                    'taxonomy' => 'sector',
                    'terms'    => $sector_ids,
                ]
            ]
        ]);

        $response = [];

        while ($intelligence->have_posts()) {
            $intelligence->the_post();

            $related = get_field('related_articles') ?: [];
            $articles = [];

            foreach ($related as $post) {
                // 🔒 Safety: ensure Articles CPT only
                if ($post->post_type !== 'articles') {
                    continue;
                }

                $articles[] = [
                    'id'    => $post->ID,
                    'title' => get_the_title($post->ID),
                    'url'   => get_permalink($post->ID),
                ];
            }

            $response[] = [
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'read_time' => get_field('read_time') ?: '5 min read',
                'sector'    => self::get_sector_name(get_the_ID()),
                'articles'  => $articles,
            ];
        }

        wp_reset_postdata();

        return rest_ensure_response($response);
    }

    private static function get_sector_name($post_id)
    {
        $terms = get_the_terms($post_id, 'sector');
        return $terms && !is_wp_error($terms) ? $terms[0]->name : '';
    }
}
