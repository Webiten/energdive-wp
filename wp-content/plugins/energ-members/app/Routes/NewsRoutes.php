<?php

namespace Energ\Routes;

class NewsRoutes
{
    public static function register()
    {
        register_rest_route('energ/v1', '/news', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_news'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_news()
    {
        $query = new \WP_Query([
            'post_type'      => 'news',   // tumhara CPT slug
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $items = [];

        foreach ($query->posts as $post) {
            $items[] = [
                'id'      => $post->ID,
                'title'   => get_the_title($post->ID),
                'excerpt' => wp_trim_words($post->post_content, 25),
                'date'    => get_the_date('', $post->ID),
                'image'   => get_the_post_thumbnail_url($post->ID, 'medium'),
                'link'    => get_permalink($post->ID),
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'news'    => $items,
        ]);
    }
}
