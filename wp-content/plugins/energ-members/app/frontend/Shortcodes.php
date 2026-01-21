<?php
namespace Energ\Frontend;

class Shortcodes {

    public static function register() {

        add_shortcode('energ_members_dashboard', function () {
            return '<div id="energ-dashboard-root" style="min-height:100vh"></div>';
        });

    }
}
