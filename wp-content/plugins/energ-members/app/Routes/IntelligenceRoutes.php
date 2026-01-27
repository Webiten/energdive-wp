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

        // 🔐 JWT se email
        $email = $request->get_param('auth_identifier');
        if (!$email) return [];

        /**
         * 1️⃣ User community
         */
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT community FROM {$wpdb->prefix}energ_members WHERE email = %s LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        if (!$row || empty($row['community'])) return [];

        $community_slugs = array_map('trim', explode(',', $row['community']));

        /**
         * 2️⃣ Sector term IDs
         */
        $sector_ids = get_terms([
            'taxonomy'   => 'sector',
            'slug'       => $community_slugs,
            'fields'     => 'ids',
            'hide_empty' => false,
        ]);

        if (empty($sector_ids) || is_wp_error($sector_ids)) return [];

        /**
         * 3️⃣ Fetch Intelligence CPT
         */
        $query = new WP_Query([
            'post_type'      => 'intelligence',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'tax_query'      => [
                [
                    'taxonomy' => 'sector',
                    'field'    => 'term_id',
                    'terms'    => $sector_ids,
                ]
            ]
        ]);

        $data = [];

        while ($query->have_posts()) {
            $query->the_post();

            // 🔗 Related articles
            $related_articles = [];
            $related = get_field('related_articles');

            if ($related) {
                foreach ($related as $post) {
                    setup_postdata($post);
                    $related_articles[] = [
                        'id'    => get_the_ID(),
                        'title' => get_the_title(),
                        'url'   => get_permalink(),
                    ];
                }
                wp_reset_postdata();
            }

            $data[] = [
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'read_time' => get_field('read_time') ?: '5 min read',
                'sector'    => self::get_sector_name(get_the_ID()),
                'articles'  => $related_articles,
            ];
        }

        wp_reset_postdata();
        return $data;
    }

    private static function get_sector_name($post_id)
    {
        $terms = get_the_terms($post_id, 'sector');
        return $terms && !is_wp_error($terms) ? $terms[0]->name : '';
    }
}
