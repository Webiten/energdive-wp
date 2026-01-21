<?php

namespace Energ\Frontend;

class Shortcodes
{
    public static function register()
    {
        add_shortcode('energ_members_dashboard', [self::class, 'dashboard']);
        add_shortcode('energ_members_auth', [self::class, 'auth']);
    }

    public static function dashboard()
    {
        // ✅ ALWAYS render root div
        return '<div id="energ-dashboard-root"></div>';
    }

    public static function auth()
    {
        return '<div id="energ-auth-root"></div>';
    }
}
