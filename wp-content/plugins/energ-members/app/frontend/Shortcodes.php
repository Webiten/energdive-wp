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
    return '
    <div
      id="energ-members-root"
      style="
        width:100%;
        min-height:100vh;
        background:#ffffff;
        isolation:isolate;
      "
    ></div>';
}


    public static function auth()
    {
        return '
        <div
          id="energ-auth-shell"
          style="
            all: initial;
            display: block;
            width: 100%;
            min-height: 100vh;
            background: #ffffff;
            isolation: isolate;
          "
        >
          <div id="energ-auth-root"></div>
        </div>';
    }
}
