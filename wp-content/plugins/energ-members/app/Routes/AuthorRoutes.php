<?php

namespace Energ\Routes;

class AuthorRoutes
{
    public static function register()
    {
        register_rest_route('energ/v1', '/authors', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_authors'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_authors()
    {
        $q = new \WP_Query([
            'post_type'      => 'energ_author',   // 👈 tumhara CPT
            'posts_per_page' => 6,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $authors = [];

        foreach ($q->posts as $p) {
            $authors[] = [
                'id'      => $p->ID,
                'name'    => get_the_title($p->ID),
                'role'    => get_post_meta($p->ID, 'role', true) ?: 'Expert',
                'specialty' => get_post_meta($p->ID, 'specialty', true) ?: '',
                'avatar'  => get_the_post_thumbnail_url($p->ID, 'thumbnail'),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'authors' => $authors,
        ]);
    }
}
