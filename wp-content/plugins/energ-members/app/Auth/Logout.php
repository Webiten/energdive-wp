<?php
namespace Energ\Auth;

class Logout
{
    public function handle($request)
    {
        global $wpdb;

        // Accept refresh token from JSON OR header OR query (flexible)
        $params  = method_exists($request, 'get_json_params') ? (array) $request->get_json_params() : [];
        $refresh = $params['refresh_token'] ?? '';

        if (!$refresh && method_exists($request, 'get_header')) {
            $refresh = (string) $request->get_header('x-refresh-token');
        }

        if (!$refresh && method_exists($request, 'get_param')) {
            $refresh = (string) $request->get_param('refresh_token');
        }

        // Also accept SID from middleware (if logout endpoint is JWT protected)
        $sid = 0;
        if (method_exists($request, 'get_param')) {
            $sid = (int) $request->get_param('auth_sid');
        }

        $table = $wpdb->prefix . 'energ_refresh_tokens';
        $revoked = false;

        // 1) Revoke by refresh token hash (recommended)
        if ($refresh) {
            $hash  = hash('sha256', $refresh);

            $updated = $wpdb->update(
                $table,
                ['revoked' => 1, 'revoked_at' => current_time('mysql', 1)],
                ['token_hash' => $hash],
                ['%d', '%s'],
                ['%s']
            );

            if ($updated) $revoked = true;
        }

        // 2) If refresh token not provided, revoke by SID (works for JWT-protected logout)
        if (!$revoked && $sid > 0) {
            $updated = $wpdb->update(
                $table,
                ['revoked' => 1, 'revoked_at' => current_time('mysql', 1)],
                ['id' => $sid],
                ['%d', '%s'],
                ['%d']
            );

            if ($updated) $revoked = true;
        }

        // 3) Clear WP auth session/cookies (safe even if not used)
        if (function_exists('wp_logout')) {
            wp_logout();
        }

        if (function_exists('wp_clear_auth_cookie')) {
            wp_clear_auth_cookie();
        }

        // 4) Clear current WP session token if possible
        if (function_exists('wp_get_session_token') && class_exists('\WP_Session_Tokens')) {
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
            'revoked' => $revoked,
        ];
    }
}
