<?php

namespace Energ\API;

use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;
use Energ\Auth\RefreshToken;
use WP_Error;

class AuthController
{
    public static function requestOtp($request)
    {
        return (new RequestOtp)->handle($request);
    }

    public static function verifyOtp($request)
    {
        return (new VerifyOtp)->handle($request);
    }

    /**
     * GET /wp-json/energ/v1/me
     * JWT protected (JwtAuth middleware)
     */
    public static function me($request)
    {
        global $wpdb;

        // ✅ Set by JwtAuth middleware (identifier = email or phone)
        $identifier = $request->get_param('auth_user');
        if (!$identifier) $identifier = $request->get_param('auth_identifier');

        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $table = $wpdb->prefix . 'energ_members';

        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    id,
                    user_id,
                    username,
                    email,
                    phone,
                    first_name,
                    last_name,
                    dob,
                    country,
                    state,
                    industry,
                    sub_industry,
                    community,
                    sub_community,
                    communities_json,
                    sub_communities_json,
                    status,
                    signup_mode,
                    created_at
                 FROM {$table}
                 WHERE email = %s OR phone = %s
                 LIMIT 1",
                $identifier,
                $identifier
            ),
            ARRAY_A
        );

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        // Phone fallback if DB phone empty but identifier is phone
        if (empty($user['phone']) && is_string($identifier) && preg_match('/^\+?\d[\d\s\-]{6,}$/', $identifier)) {
            $user['phone'] = $identifier;
        }

        // Normalize communities/subCommunities arrays
        $communities = self::decode_json_array($user['communities_json'] ?? null);
        if (empty($communities) && !empty($user['community'])) {
            $communities = [ (string) $user['community'] ];
        }

        $subCommunities = self::decode_json_array($user['sub_communities_json'] ?? null);
        if (empty($subCommunities) && !empty($user['sub_community'])) {
            $subCommunities = [ (string) $user['sub_community'] ];
        }

        // Notifications stored in WP user meta (recommended since table doesn't have notifications_json)
        $notifications = [];
        $wpUserId = intval($user['user_id'] ?? 0);
        if ($wpUserId > 0) {
            $raw = get_user_meta($wpUserId, 'energ_notifications', true);
            $notifications = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
            if (!is_array($notifications)) $notifications = [];
        }

        // hasPassword from WP user
        $hasPassword = null;
        if ($wpUserId > 0) {
            $wpUser = get_user_by('id', $wpUserId);
            $hasPassword = ($wpUser && !empty($wpUser->user_pass)) ? true : false;
        }

        // Attach normalized fields for frontend
        $user['communities'] = $communities;
        $user['subCommunities'] = $subCommunities;
        $user['notifications'] = $notifications;
        $user['hasPassword'] = $hasPassword;

        return [
            'success' => true,
            'user'    => $user,
        ];
    }

    /**
     * POST /wp-json/energ/v1/me
     * JWT protected (JwtAuth middleware)
     *
     * Supports:
     * - Profile update (energ_members)
     * - Communities/subCommunities update (JSON columns)
     * - Notifications update (WP user meta)
     * - Password set/change (WP user; first time no current password)
     */
    public static function updateMe($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user');
        if (!$identifier) $identifier = $request->get_param('auth_identifier');
        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $params = $request->get_json_params();
        if (!is_array($params)) $params = [];

        $table = $wpdb->prefix . 'energ_members';

        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    id,
                    user_id,
                    email,
                    phone,
                    first_name,
                    last_name,
                    country,
                    state,
                    industry,
                    sub_industry,
                    community,
                    sub_community,
                    communities_json,
                    sub_communities_json
                 FROM {$table}
                 WHERE email = %s OR phone = %s
                 LIMIT 1",
                $identifier,
                $identifier
            ),
            ARRAY_A
        );

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        $memberId = intval($user['id']);
        $wpUserId = intval($user['user_id'] ?? 0);

        // -----------------------------
        // 1) Update energ_members profile fields
        // -----------------------------
        $updates = [];
        $formats = [];

        $cols = self::get_table_columns($table);

        $map = [
            'firstName'   => 'first_name',
            'lastName'    => 'last_name',
            'country'     => 'country',
            'state'       => 'state',
            'industry'    => 'industry',
            'subIndustry' => 'sub_industry',
        ];

        foreach ($map as $inKey => $dbCol) {
            if (!array_key_exists($inKey, $params)) continue;
            if (!in_array($dbCol, $cols, true)) continue;

            $updates[$dbCol] = is_null($params[$inKey]) ? '' : (string) $params[$inKey];
            $formats[] = '%s';
        }

        // Communities (multi)
        if (array_key_exists('communities', $params)) {
            $communities = self::sanitize_string_array($params['communities']);

            if (in_array('communities_json', $cols, true)) {
                $updates['communities_json'] = wp_json_encode($communities);
                $formats[] = '%s';
            }

            // Keep legacy single value too
            if (in_array('community', $cols, true)) {
                $updates['community'] = isset($communities[0]) ? $communities[0] : '';
                $formats[] = '%s';
            }
        }

        // Sub-communities (multi)
        if (array_key_exists('subCommunities', $params)) {
            $subCommunities = self::sanitize_string_array($params['subCommunities']);

            if (in_array('sub_communities_json', $cols, true)) {
                $updates['sub_communities_json'] = wp_json_encode($subCommunities);
                $formats[] = '%s';
            }

            // Keep legacy single value too
            if (in_array('sub_community', $cols, true)) {
                $updates['sub_community'] = isset($subCommunities[0]) ? $subCommunities[0] : '';
                $formats[] = '%s';
            }
        }

        if (!empty($updates)) {
            $ok = $wpdb->update(
                $table,
                $updates,
                ['id' => $memberId],
                $formats,
                ['%d']
            );

            if ($ok === false) {
                return new WP_Error('update_failed', 'Failed to update member profile', ['status' => 500]);
            }
        }

        // -----------------------------
        // 2) Notifications -> WP user meta
        // -----------------------------
        if (array_key_exists('notifications', $params)) {
            if ($wpUserId <= 0) {
                return new WP_Error('missing_wp_user', 'User ID missing for notifications update', ['status' => 400]);
            }

            $n = is_array($params['notifications']) ? $params['notifications'] : [];
            $notifications = [
                'emailNotifications' => !empty($n['emailNotifications']),
                'weeklyDigest'       => !empty($n['weeklyDigest']),
                'eventReminders'     => !empty($n['eventReminders']),
                'communityActivity'  => !empty($n['communityActivity']),
            ];

            update_user_meta($wpUserId, 'energ_notifications', $notifications);
        }

        // -----------------------------
        // 3) Password set/change
        // -----------------------------
        if (!empty($params['password']) && is_array($params['password'])) {
            if ($wpUserId <= 0) {
                return new WP_Error('missing_wp_user', 'User ID missing for password update', ['status' => 400]);
            }

            $pw = $params['password'];
            $newPassword = isset($pw['newPassword']) ? (string) $pw['newPassword'] : '';
            $currentPassword = isset($pw['currentPassword']) ? (string) $pw['currentPassword'] : '';

            if (strlen($newPassword) < 8) {
                return new WP_Error('weak_password', 'New password must be at least 8 characters', ['status' => 400]);
            }

            $wpUser = get_user_by('id', $wpUserId);
            if (!$wpUser) {
                return new WP_Error('wp_user_not_found', 'WP user not found for password update', ['status' => 404]);
            }

            $passwordAlreadySet = !empty($wpUser->user_pass);

            // If already set, require current password check
            if ($passwordAlreadySet) {
                if ($currentPassword === '') {
                    return new WP_Error('current_password_required', 'Current password is required', ['status' => 400]);
                }
                if (!wp_check_password($currentPassword, $wpUser->user_pass, $wpUser->ID)) {
                    return new WP_Error('invalid_current_password', 'Current password is incorrect', ['status' => 400]);
                }
            }

            wp_set_password($newPassword, $wpUser->ID);
        }

        // Return fresh data
        return self::me($request);
    }

    public static function refreshToken($request)
    {
        return (new RefreshToken)->handle($request);
    }

    public static function logout($request)
    {
        return (new \Energ\Auth\Logout)->handle($request);
    }

    public static function completeRegistration($request)
    {
        return (new \Energ\Auth\CompleteRegistration)->handle($request);
    }

    /* =========================
       Helpers
    ========================= */

    private static function sanitize_string_array($value)
    {
        $arr = [];
        if (is_array($value)) {
            $arr = $value;
        } elseif (is_string($value)) {
            $arr = explode(',', $value);
        } else {
            return [];
        }

        $out = [];
        $seen = [];
        foreach ($arr as $v) {
            if (!is_string($v)) continue;
            $s = trim($v);
            if ($s === '') continue;
            $k = strtolower($s);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $out[] = $s;
        }
        return $out;
    }

    private static function decode_json_array($json)
    {
        if (!is_string($json) || trim($json) === '') return [];
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) return [];
        $out = [];
        foreach ($decoded as $v) {
            if (is_string($v) && trim($v) !== '') {
                $out[] = trim($v);
            }
        }
        return $out;
    }

    private static function get_table_columns($table)
    {
        global $wpdb;
        static $cacheCols = [];
        if (isset($cacheCols[$table])) return $cacheCols[$table];

        $cols = [];
        $rows = $wpdb->get_results("SHOW COLUMNS FROM {$table}", ARRAY_A);
        if (is_array($rows)) {
            foreach ($rows as $r) {
                if (!empty($r['Field'])) $cols[] = $r['Field'];
            }
        }
        $cacheCols[$table] = $cols;
        return $cols;
    }
}
