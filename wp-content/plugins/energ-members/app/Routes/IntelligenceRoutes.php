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

        // 🔐 Email from JWT
        $email = $request->get_param('auth_identifier');
        if (!$email) {
            return rest_ensure_response([]);
        }

        /**
         * 1️⃣ Get community slug from energ_members
         */
        $table = $wpdb->prefix . 'energ_members';

        $community = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT community FROM {$table} WHERE email = %s LIMIT 1",
                $email
            )
        );

        if (!$community) {
            return rest_ensure_response([]);
        }

        /**
         * 2️⃣ Community slug(s)
         * Example: oil-gas
         */
        $community_slugs = array_map('trim', explode(',', $community));

        /**
         * 3️⃣ Fetch articles (SLUG BASED — SAME AS CLI)
         */
        $query = new WP_Query([
            'post_type'      => 'articles',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => [
                [
                    'taxonomy'         => 'sector',
                    'field'            => 'slug',
                    'terms'            => $community_slugs,
                    'include_children' => true,
                ]
            ]
        ]);

        $items = [];

        while ($query->have_posts()) {
            $query->the_post();

            $items[] = [
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'url'       => get_permalink(),
                'date'      => get_the_date('M d, Y'),
                'read_time' => get_field('read_time') ?: '5 min read',
                'author'    => get_the_author(),
                'category'  => self::get_sector_name(get_the_ID()),
            ];
        }

        wp_reset_postdata();

        return rest_ensure_response($items);
    }

    private static function get_sector_name($post_id)
    {
        $terms = get_the_terms($post_id, 'sector');
        return ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
    }
}
