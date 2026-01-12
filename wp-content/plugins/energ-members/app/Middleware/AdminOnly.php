<?php

namespace Energ\Middleware;

use WP_Error;

class AdminOnly
{
    public static function allow()
    {
        if (!is_user_logged_in()) {
            return new WP_Error('not_logged_in', 'Admin login required', ['status' => 401]);
        }

        if (!current_user_can('manage_options')) {
            return new WP_Error('forbidden', 'Admin access required', ['status' => 403]);
        }

        return true;
    }
}
