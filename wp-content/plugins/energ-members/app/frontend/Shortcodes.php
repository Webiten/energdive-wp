<?php
error_log('ENERG Shortcodes loaded');
namespace Energ\Frontend;

class Shortcodes
{
    public static function register()
    {
        add_shortcode('energ_members_dashboard', [self::class, 'dashboard']);
        add_shortcode('energ_members_auth', [self::class, 'auth']);
    }

    /**
     * React Dashboard Mount
     * ❌ NO PHP auth check here
     * ✅ React handles login / session
     */
    public static function dashboard()
    {
        return '<div id="energ-dashboard-root"></div>';
    }

    /**
     * React Auth Mount (Login / OTP / Register)
     */
    public static function auth()
    {
        return '<div id="energ-auth-root"></div>';
    }
}
