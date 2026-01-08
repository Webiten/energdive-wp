<?php
defined('ABSPATH') || exit;

add_action('wp_ajax_energ_logout', 'energ_handle_logout');
add_action('wp_ajax_nopriv_energ_logout', 'energ_handle_logout');

function energ_handle_logout(){
    check_ajax_referer('energ_nonce','nonce');
    energ_clear_session();
    wp_send_json_success();
}
