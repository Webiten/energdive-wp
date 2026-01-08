<?php
add_action('init', function() {
    if ($_SERVER['REQUEST_URI'] === '/zoho-form-debug') {

        $raw = file_get_contents("php://input");

        error_log("\n\n========== ZOHO FORM WEBHOOK ==========\n");
        error_log($raw);
        error_log("\n=======================================\n");

        wp_send_json_success("OK");
        exit;
    }
});