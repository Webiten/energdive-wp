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
            return [];
        }

        // 1️⃣ Get community slug from energ_members
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT community FROM {$wpdb->prefix}energ_members WHERE email = %s LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        if (!$row || empty($row['community'])) {
            return [];
        }

        // 2️⃣ Community slug → sector term IDs
        $community_slugs = array_map('trim', explode(',', $row['community']));

        $sector_terms = get_terms([
            'taxonomy'   => 'sector',
            'slug'       => $community_slugs,
            'hide_empty' => false,
        ]);

        if (empty($sector_terms) || is_wp_error($sector_terms)) {
            return [];
        }

        $sector_ids = wp_list_pluck($sector_terms, 'term_id');

        // 3️⃣ Fetch articles (🔥 filters OFF)
        $query = new WP_Query([
            'post_type'           => 'articles',
            'post_status'         => 'publish',
            'posts_per_page'      => 10,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'suppress_filters'    => true, // 🔥 MOST IMPORTANT
            'tax_query' => [
                [
                    'taxonomy' => 'sector',
                    'field'    => 'term_id',
                    'terms'    => $sector_ids,
                ]
            ],
        ]);

        $items = [];

        while ($query->have_posts()) {
            $query->the_post();

            $items[] = [
                'id'       => get_the_ID(),
                'title'    => get_the_title(),
                'excerpt'  => get_the_excerpt(),
                'url'      => get_permalink(),
                'date'     => get_the_date('M d, Y'),
                'author'   => get_the_author(),
                'category' => self::get_sector_name(get_the_ID()),
            ];
        }

        wp_reset_postdata();

        return $items;
    }

    private static function get_sector_name($post_id)
    {
        $terms = get_the_terms($post_id, 'sector');
        return ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
    }
}
