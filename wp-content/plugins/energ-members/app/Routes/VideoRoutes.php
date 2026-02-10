<?php

namespace Energ\Routes;

class VideoRoutes
{
    public static function register()
    {
        register_rest_route('energ/v1', '/videos', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_videos'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_videos()
    {
        $query = new \WP_Query([
            'post_type'      => 'videos',   // 👈 tumhara CPT slug
            'posts_per_page' => 15,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $items = [];

        foreach ($query->posts as $post) {
            $items[] = [
                'id'      => $post->ID,
                'title'   => get_the_title($post->ID),
                // 'date'    => get_the_date('', $post->ID),
                'thumbnail' => get_the_post_thumbnail_url($post->ID, 'medium'),
                'video_url' => get_post_meta($post->ID, 'video_url', true), // 👈 custom field
            ];
        }

        return rest_ensure_response([
            'success' => true,
            'videos'  => $items,
        ]);
    }
}
