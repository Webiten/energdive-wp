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
    ob_start();
    ?>
    <div id="energ-dashboard-root"></div>
    <?php
    return ob_get_clean();
}


    public static function auth()
    {
        return '<div id="energ-auth-root"></div>';
    }
}
