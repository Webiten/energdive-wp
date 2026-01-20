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

    private static function decodeJsonArray($value)
    {
        if (!$value) return [];
        if (is_array($value)) return $value;
        if (!is_string($value)) return [];

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) return [];
        if (!is_array($decoded)) return [];

        // keep only strings
        return array_values(array_filter($decoded, fn($x) => is_string($x) && trim($x) !== ''));
    }

    private static function normalizeStringArray($value)
    {
        if (!$value) return [];
        if (is_array($value)) return array_values(array_filter($value, fn($x) => is_string($x) && trim($x) !== ''));
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }
        return [];
    }

    public static function me($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user') ?: $request->get_param('auth_identifier');

        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

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

        // ✅ Decode arrays from JSON columns
        $communities = self::decodeJsonArray($user['communities_json'] ?? null);
        $subCommunities = self::decodeJsonArray($user['sub_communities_json'] ?? null);

        // Backward compatibility: if JSON empty but old single columns exist
        if (empty($communities) && !empty($user['community'])) {
            $communities = self::normalizeStringArray($user['community']);
        }
        if (empty($subCommunities) && !empty($user['sub_community'])) {
            $subCommunities = self::normalizeStringArray($user['sub_community']);
        }

        $user['communities'] = $communities;
        $user['sub_communities'] = $subCommunities;

        return [
            'success' => true,
            'user'    => $user,
        ];
    }

    /**
     * ✅ POST /me
     * Updates profile + communities/sub communities (persisted in wp_energ_members)
     */
    public static function updateMe($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user') ?: $request->get_param('auth_identifier');
        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $table = $wpdb->prefix . 'energ_members';

        $member = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, email, phone FROM {$table} WHERE email = %s OR phone = %s LIMIT 1",
                $identifier,
                $identifier
            ),
            ARRAY_A
        );

        if (!$member) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        $params = (array) $request->get_json_params();

        $first_name   = isset($params['firstName']) ? sanitize_text_field($params['firstName']) : null;
        $last_name    = isset($params['lastName']) ? sanitize_text_field($params['lastName']) : null;
        $job_title    = isset($params['jobTitle']) ? sanitize_text_field($params['jobTitle']) : null;
        $organization = isset($params['organization']) ? sanitize_text_field($params['organization']) : null;
        $country      = isset($params['country']) ? sanitize_text_field($params['country']) : null;
        $industry     = isset($params['industry']) ? sanitize_text_field($params['industry']) : null;

        $communities = isset($params['communities']) ? $params['communities'] : null;
        $subCommunities = isset($params['subCommunities']) ? $params['subCommunities'] : null;

        $update = [];
        $format = [];

        if ($first_name !== null)   { $update['first_name'] = $first_name; $format[] = '%s'; }
        if ($last_name !== null)    { $update['last_name'] = $last_name; $format[] = '%s'; }
        if ($job_title !== null)    { $update['job_title'] = $job_title; $format[] = '%s'; }
        if ($organization !== null) { $update['organization'] = $organization; $format[] = '%s'; }
        if ($country !== null)      { $update['country'] = $country; $format[] = '%s'; }
        if ($industry !== null)     { $update['industry'] = $industry; $format[] = '%s'; }

        if (is_array($communities)) {
            $clean = array_values(array_unique(array_filter(array_map('sanitize_text_field', $communities))));
            $update['communities_json'] = wp_json_encode($clean);
            $format[] = '%s';
            // backward compat
            $update['community'] = $clean[0] ?? '';
            $format[] = '%s';
        }

        if (is_array($subCommunities)) {
            $clean = array_values(array_unique(array_filter(array_map('sanitize_text_field', $subCommunities))));
            $update['sub_communities_json'] = wp_json_encode($clean);
            $format[] = '%s';
            // backward compat
            $update['sub_community'] = $clean[0] ?? '';
            $format[] = '%s';
        }

        if (empty($update)) {
            return [
                'success' => true,
                'message' => 'Nothing to update',
            ];
        }

        $wpdb->update(
            $table,
            $update,
            ['id' => (int) $member['id']],
            $format,
            ['%d']
        );

        // Return fresh /me style response
        $req = new \WP_REST_Request('GET', '/energ/v1/me');
        $req->set_param('auth_identifier', $identifier);
        $req->set_param('auth_user', $identifier);

        return self::me($req);
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
}
