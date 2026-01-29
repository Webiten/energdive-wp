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

        /* ================= AUTH ================= */

        $authUser = $request->get_param('auth_user');
        if (!$authUser) {
            return rest_ensure_response([]);
        }

        if (is_numeric($authUser)) {
            $where = $wpdb->prepare('user_id = %d', (int) $authUser);
        } elseif (is_email($authUser)) {
            $where = $wpdb->prepare('email = %s', $authUser);
        } else {
            $where = $wpdb->prepare('phone = %s', $authUser);
        }

        /* ================= USER COMMUNITIES ================= */

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

        /* ================= SECTOR TERMS ================= */

        // normalize to slugs + legacy safety
        $sectorSlugs = array_unique(array_merge(
            array_map('sanitize_title', $communities),
            array_map(fn($c) => sanitize_title(str_replace('-', ' ', $c)), $communities)
        ));

        $sectors = get_terms([
            'taxonomy'   => 'sector',
            'slug'       => $sectorSlugs,
            'hide_empty' => false,
        ]);

        if (empty($sectors) || is_wp_error($sectors)) {
            return rest_ensure_response([]);
        }

        $response = [];

        /* ================= MAIN LOOP ================= */

        foreach ($sectors as $sector) {

            // collect community + child sectors
            $termIds = [$sector->term_id];

            $children = get_terms([
                'taxonomy'   => 'sector',
                'parent'     => $sector->term_id,
                'hide_empty' => false,
            ]);

            if (!is_wp_error($children)) {
                foreach ($children as $child) {
                    $termIds[] = $child->term_id;
                }
            }

            /* ================= INTELLIGENCE CPT ================= */

            $intelQuery = new WP_Query([
                'post_type'      => 'intelligence',
                'post_status'    => 'publish',
                'posts_per_page' => 10,
                'tax_query'      => [
                    [
                        'taxonomy' => 'sector',
                        'field'    => 'term_id',
                        'terms'    => $termIds,
                    ]
                ]
            ]);

            if (!$intelQuery->have_posts()) {
                wp_reset_postdata();
                continue;
            }

            $items = [];
            $seen  = [];

            while ($intelQuery->have_posts()) {
                $intelQuery->the_post();

                // 🔥 ACF Relationship field
                $relatedArticles = function_exists('get_field')
                    ? get_field('related_articles')
                    : [];


                if (!is_array($relatedArticles)) {
                    continue;
                }

                if (!is_array($relatedArticles)) {
                    continue;
                }

                foreach ($relatedArticles as $post) {

                    if (!isset($post->ID) || isset($seen[$post->ID])) {
                        continue;
                    }

                    $seen[$post->ID] = true;

                    $items[] = [
                        'id'    => $post->ID,
                        'title' => get_the_title($post->ID),
                        'url'   => get_permalink($post->ID),
                    ];
                }
            }

            wp_reset_postdata();

            if (!empty($items)) {
                $response[] = [
                    'sector'   => $sector->name,
                    'articles' => array_values($items),
                ];
            }
        }

        return rest_ensure_response($response);
    }
}
