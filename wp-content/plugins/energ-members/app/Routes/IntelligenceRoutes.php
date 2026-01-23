<?php

namespace Energ\Routes;

use WP_Query;

class IntelligenceRoutes
{

    public static function register()
    {

        register_rest_route('energ/v1', '/intelligence', [
            'methods'  => 'GET',
            'callback' => [self::class, 'handle'],
            'permission_callback' => function () {
                return JwtAuth::validate();
            }
        ]);
    }

    public static function handle()
    {

        $user = wp_get_current_user();

        // ACF user field: sector (community)
        $sectors = get_field('sector', 'user_' . $user->ID);

        if (!$sectors) {
            return rest_ensure_response([]);
        }

        if (!is_array($sectors)) {
            $sectors = [$sectors];
        }

        $sector_ids = array_map(fn($t) => (int) $t->term_id, $sectors);

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
        return $terms && !is_wp_error($terms) ? $terms[0]->name : '';
    }
}
