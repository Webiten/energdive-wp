<?php
namespace Energ\Routes;

use WP_REST_Request;
use WP_Error;

class UserRoutes {

    public static function register() {

        register_rest_route('energ/v1', '/zoho-create-user', [
            'methods'  => 'POST',
            'callback' => [self::class, 'createFromZoho'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function createFromZoho(WP_REST_Request $request) {

        global $wpdb;

        $name  = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        $token = sanitize_text_field($request->get_param('token'));
        $secret = sanitize_text_field($request->get_param('secret'));

        if ($secret !== "ZOHO_ENERGDIVE_SECRET") {
            return new WP_Error('unauthorized', 'Invalid secret', ['status' => 403]);
        }

        $table = $wpdb->prefix . 'energ_members';

        $exists = $wpdb->get_row(
            $wpdb->prepare("SELECT id FROM $table WHERE email=%s", $email)
        );

        if ($exists) {
            return ['status'=>'exists'];
        }

        $parts = explode(' ', $name, 2);

        $wpdb->insert($table,[
            'email'=>$email,
            'first_name'=>$parts[0],
            'last_name'=>$parts[1] ?? '',
            'signup_mode'=>'zoho',
            'zoho_token'=>$token,
            'status'=>'pending',
            'created_at'=>current_time('mysql')
        ]);

        return ['status'=>'success'];
    }
}

add_action('rest_api_init', function () {
    UserRoutes::register();
});


add_shortcode('activate_zoho_user', function () {

    global $wpdb;

    if (!isset($_GET['token'])) return "Invalid link";

    $token = sanitize_text_field($_GET['token']);
    $table = $wpdb->prefix."energ_members";

    $member = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE zoho_token=%s",$token)
    );

    if(!$member) return "Invalid or expired link";

    // mark active
    $wpdb->update($table,['status'=>'active'],['id'=>$member->id]);

    // create wp user
    if(!email_exists($member->email)){

        $uid = wp_insert_user([
            'user_login'=>$member->email,
            'user_email'=>$member->email,
            'user_pass'=>wp_generate_password(),
            'display_name'=>$member->first_name,
            'role'=>'subscriber'
        ]);

    } else {
        $u = get_user_by('email',$member->email);
        $uid = $u->ID;
    }

    // LOGIN PROPER WAY
    wp_clear_auth_cookie();
    wp_set_current_user($uid);
    wp_set_auth_cookie($uid,true);
    do_action('wp_login',$member->email,get_user_by('ID',$uid));

    update_user_meta($uid,'first_name',$member->first_name);

    // one time token
    $wpdb->update($table,['zoho_token'=>null],['id'=>$member->id]);

    wp_safe_redirect(home_url());
    exit;
});