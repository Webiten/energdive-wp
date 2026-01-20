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
     * ✅ GET /wp-json/energ/v1/me
     * Returns:
     * - communities: array (from communities_json)
     * - subCommunities: array (from sub_communities_json)
     * - phone always filled if available
     * - hasPassword based on WP user meta (first time set password logic)
     */
    public static function me($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user');
        if (!$identifier) $identifier = $request->get_param('auth_identifier');

        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $table = $wpdb->prefix . 'energ_members';

        $row = $wpdb->get_row(
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

        if (!$row) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        // ✅ Decode JSON arrays safely
        $communities = self::decodeJsonArray($row['communities_json'] ?? null);
        $subCommunities = self::decodeJsonArray($row['sub_communities_json'] ?? null);

        // Backward compatibility: if JSON empty but legacy single value exists
        if (empty($communities) && !empty($row['community'])) {
            $communities = [ (string) $row['community'] ];
        }
        if (empty($subCommunities) && !empty($row['sub_community'])) {
            $subCommunities = [ (string) $row['sub_community'] ];
        }

        // ✅ phone fallback: if phone null, set identifier when it looks like phone
        if (empty($row['phone']) && preg_match('/^\+?\d[\d\s\-]{6,}$/', (string)$identifier)) {
            $row['phone'] = $identifier;
        }

        // ✅ Password logic:
        // Use WP user meta 'energ_password_set' to decide first-time set password.
        $hasPassword = true;
        $wpUserId = intval($row['user_id'] ?? 0);
        if ($wpUserId > 0) {
            $flag = get_user_meta($wpUserId, 'energ_password_set', true);
            $hasPassword = ($flag === '1' || $flag === 1 || $flag === true);
        } else {
            // If no WP user linked, treat as true to avoid showing "set password" incorrectly.
            $hasPassword = true;
        }

        $user = [
            'id' => intval($row['id']),
            'user_id' => $wpUserId,
            'username' => $row['username'],
            'email' => $row['email'],
            'phone' => $row['phone'],

            'firstName' => $row['first_name'],
            'lastName' => $row['last_name'],
            'jobTitle' => null,
            'organization' => null,

            'dob' => $row['dob'],
            'country' => $row['country'],
            'state' => $row['state'],
            'industry' => $row['industry'],
            'sub_industry' => $row['sub_industry'],

            // Legacy single values:
            'community' => $row['community'],
            'sub_community' => $row['sub_community'],

            // ✅ Multi values:
            'communities' => $communities,
            'subCommunities' => $subCommunities,

            'status' => $row['status'],
            'signup_mode' => $row['signup_mode'],
            'created_at' => $row['created_at'],

            // ✅ used by frontend
            'hasPassword' => $hasPassword,
        ];

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    /**
     * ✅ POST /wp-json/energ/v1/me
     * Payload supports:
     * - firstName, lastName, jobTitle, organization, country, industry
     * - communities: array
     * - subCommunities: array
     * - notifications: object (stored as JSON in user meta, or in members JSON if you add column later)
     * - password: { currentPassword?, newPassword }
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

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, user_id, email, phone, communities_json, sub_communities_json
                 FROM {$table}
                 WHERE email = %s OR phone = %s
                 LIMIT 1",
                $identifier,
                $identifier
            ),
            ARRAY_A
        );

        if (!$row) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        $memberId = intval($row['id']);
        $wpUserId = intval($row['user_id'] ?? 0);

        // ---- sanitize helpers
        $getStr = function ($key) use ($params) {
            $v = $params[$key] ?? null;
            if (!is_string($v)) return null;
            $v = trim($v);
            return $v === '' ? null : $v;
        };

        $getArr = function ($key) use ($params) {
            $v = $params[$key] ?? null;
            if (!is_array($v)) return null;
            $out = [];
            $seen = [];
            foreach ($v as $item) {
                if (!is_string($item)) continue;
                $s = trim($item);
                if ($s === '') continue;
                $k = strtolower($s);
                if (isset($seen[$k])) continue;
                $seen[$k] = true;
                $out[] = $s;
            }
            return $out;
        };

        // ---- build DB update
        $update = [];
        $format = [];

        $firstName = $getStr('firstName');
        $lastName = $getStr('lastName');
        $country = $getStr('country');
        $industry = $getStr('industry');

        if ($firstName !== null) { $update['first_name'] = $firstName; $format[] = '%s'; }
        if ($lastName !== null)  { $update['last_name']  = $lastName;  $format[] = '%s'; }
        if ($country !== null)   { $update['country']    = $country;   $format[] = '%s'; }
        if ($industry !== null)  { $update['industry']   = $industry;  $format[] = '%s'; }

        $communities = $getArr('communities');
        if ($communities !== null) {
            $update['communities_json'] = wp_json_encode($communities);
            $format[] = '%s';
            // legacy:
            $update['community'] = $communities[0] ?? null;
            $format[] = '%s';
        }

        $subCommunities = $getArr('subCommunities');
        if ($subCommunities !== null) {
            $update['sub_communities_json'] = wp_json_encode($subCommunities);
            $format[] = '%s';
            // legacy:
            $update['sub_community'] = $subCommunities[0] ?? null;
            $format[] = '%s';
        }

        // ---- save member row
        if (!empty($update)) {
            $wpdb->update(
                $table,
                $update,
                ['id' => $memberId],
                $format,
                ['%d']
            );
        }

        // ---- notifications: store in WP user meta for now
        // (your members table doesn't have a notifications column)
        if ($wpUserId > 0 && isset($params['notifications']) && is_array($params['notifications'])) {
            update_user_meta($wpUserId, 'energ_notifications', wp_json_encode($params['notifications']));
        }

        // ---- password update: first time no current password required
        if (isset($params['password']) && is_array($params['password'])) {
            if ($wpUserId <= 0) {
                return new WP_Error('no_wp_user', 'No WordPress user linked to this member.', ['status' => 400]);
            }

            $newPassword = $params['password']['newPassword'] ?? '';
            $newPassword = is_string($newPassword) ? trim($newPassword) : '';
            if (strlen($newPassword) < 8) {
                return new WP_Error('weak_password', 'New password must be at least 8 characters.', ['status' => 400]);
            }

            $flag = get_user_meta($wpUserId, 'energ_password_set', true);
            $isFirstTime = !($flag === '1' || $flag === 1 || $flag === true);

            if (!$isFirstTime) {
                $currentPassword = $params['password']['currentPassword'] ?? '';
                $currentPassword = is_string($currentPassword) ? $currentPassword : '';

                $wpUser = get_user_by('id', $wpUserId);
                if (!$wpUser) {
                    return new WP_Error('wp_user_missing', 'Linked WordPress user not found.', ['status' => 400]);
                }

                if (!wp_check_password($currentPassword, $wpUser->user_pass, $wpUserId)) {
                    return new WP_Error('wrong_password', 'Current password is incorrect.', ['status' => 400]);
                }
            }

            wp_set_password($newPassword, $wpUserId);
            update_user_meta($wpUserId, 'energ_password_set', '1');
        }

        // Return fresh /me
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

    private static function decodeJsonArray($raw)
    {
        if (!$raw) return [];
        if (is_array($raw)) return $raw;

        $decoded = json_decode((string)$raw, true);
        if (!is_array($decoded)) return [];

        $out = [];
        $seen = [];
        foreach ($decoded as $item) {
            if (!is_string($item)) continue;
            $s = trim($item);
            if ($s === '') continue;
            $k = strtolower($s);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $out[] = $s;
        }
        return $out;
    }
}
