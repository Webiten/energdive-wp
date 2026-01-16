<?php
namespace Energ\Auth;

use WP_Error;

class Logout {

    public function handle($request) {
        global $wpdb;

        // Accept refresh token from JSON OR header OR query (flexible)
        $params  = method_exists($request, 'get_json_params') ? (array) $request->get_json_params() : [];
        $refresh = $params['refresh_token'] ?? '';

        if (!$refresh && method_exists($request, 'get_header')) {
            // Optional: allow passing refresh token in header: X-Refresh-Token
            $refresh = (string) $request->get_header('x-refresh-token');
        }

        if (!$refresh && method_exists($request, 'get_param')) {
            // Optional: allow passing refresh token in query/body param
            $refresh = (string) $request->get_param('refresh_token');
        }

        // 1) If refresh token exists, revoke it (your existing behavior)
        if ($refresh) {
            $hash  = hash('sha256', $refresh);
            $table = $wpdb->prefix . 'energ_refresh_tokens';

            $updated = $wpdb->update(
                $table,
                ['revoked' => 1],
                ['token_hash' => $hash],
                ['%d'],
                ['%s']
            );

            if (!$updated) {
                // Important: Do NOT block cookie logout if token revoke fails.
                // Some clients might not have refresh tokens.
                // We'll continue to clear WP session below.
            }
        }

        // 2) Always clear WP auth session/cookies (this is what fixes "still logged in")
        if (function_exists('wp_logout')) {
            wp_logout();
        }

        if (function_exists('wp_clear_auth_cookie')) {
            wp_clear_auth_cookie();
        }

        // Also clear the current session token if possible
        if (function_exists('wp_get_session_token') && function_exists('WP_Session_Tokens')) {
            $token = wp_get_session_token();
            if ($token) {
                $manager = \WP_Session_Tokens::get_instance(get_current_user_id());
                if ($manager) {
                    $manager->destroy($token);
                }
            }
        }

        return [
            'success' => true,
            'message' => 'Logged out successfully',
            'revoked_refresh_token' => $refresh ? true : false
        ];
    }
}
