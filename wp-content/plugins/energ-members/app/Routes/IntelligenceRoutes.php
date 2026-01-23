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

        /**
         * 🔐 USER IDENTIFIER FROM JWT
         * (JwtAuth::allow already verified token)
         */
        $identifier = $request->get_param('auth_identifier');

        if (!$identifier) {
            return rest_ensure_response([]);
        }

        /**
         * 📦 FETCH USER FROM energ_members TABLE
         * community = sector term IDs (CSV: "1,3,5")
         */
        $table = $wpdb->prefix . 'energ_members';

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT community FROM {$table} WHERE identifier = %s LIMIT 1",
                $identifier
            ),
            ARRAY_A
        );

        if (!$row || empty($row['community'])) {
            return rest_ensure_response([]);
        }

        /**
         * 🧠 PARSE SECTOR IDS
         */
        $sector_ids = array_map(
            'intval',
            array_filter(explode(',', $row['community']))
        );

        if (empty($sector_ids)) {
            return rest_ensure_response([]);
        }

        /**
         * 📰 FETCH ARTICLES BASED ON SECTOR
         */
        $query = new WP_Query([
            'post_type'      => 'articles',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => [
                [
                    'taxonomy' => 'sector',
                    'field'    => 'term_id',
                    'terms'    => $sector_ids,
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
