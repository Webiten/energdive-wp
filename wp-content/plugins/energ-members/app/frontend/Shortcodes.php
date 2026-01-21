<?php
namespace Energ\Frontend;

class Shortcodes
{
    public static function register()
    {
        add_shortcode('energ_members_dashboard', [self::class, 'dashboard']);
    }

    public static function dashboard()
    {
        if (!is_user_logged_in()) {
            return '<p>Please login to access dashboard.</p>';
        }

        return '<div id="energ-dashboard-root"></div>';
    }
}
