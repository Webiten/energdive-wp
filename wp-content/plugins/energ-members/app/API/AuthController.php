<?php

namespace Energ\API;

use Energ\Auth\RequestOtp;
use Energ\Auth\VerifyOtp;
use Energ\Auth\RefreshToken;
use WP_Error;

class AuthController
{
    /* =====================================================
     * HELPERS
     * ===================================================== */

    private static function resolveWhereClause($wpdb, $identifier)
    {
        if (is_numeric($identifier)) {
            return $wpdb->prepare('user_id = %d', (int) $identifier);
        }

        if (is_email($identifier)) {
            return $wpdb->prepare('email = %s', $identifier);
        }

        return $wpdb->prepare('phone = %s', $identifier);
    }

    private static function decodeJsonArray($value)
    {
        if (!$value) return [];
        if (is_array($value)) return $value;
        if (!is_string($value)) return [];

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) return [];
        if (!is_array($decoded)) return [];

        return array_values(array_filter($decoded, fn ($x) => is_string($x) && trim($x) !== ''));
    }

    private static function normalizeStringArray($value)
    {
        if (!$value) return [];
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($x) => is_string($x) && trim($x) !== ''));
        }
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }
        return [];
    }

    /* =====================================================
     * AUTH ENDPOINTS
     * ===================================================== */

    public static function requestOtp($request)
    {
        return (new RequestOtp)->handle($request);
    }

    public static function verifyOtp($request)
    {
        return (new VerifyOtp)->handle($request);
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

    /* =====================================================
     * GET /me
     * ===================================================== */

    public static function me($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user') ?: $request->get_param('auth_identifier');
        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $table = $wpdb->prefix . 'energ_members';
        $where = self::resolveWhereClause($wpdb, $identifier);

        $user = $wpdb->get_row(
            "SELECT
                id,
                user_id,
                email,
                phone,
                first_name,
                last_name,
                job_title,
                organization,
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
             WHERE {$where}
             LIMIT 1",
            ARRAY_A
        );

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        // ✅ Resolve communities (source of truth first)
        $communities = self::decodeJsonArray($user['communities_json'] ?? null);
        $subCommunities = self::decodeJsonArray($user['sub_communities_json'] ?? null);

        // Legacy fallback
        if (empty($communities) && !empty($user['community'])) {
            $communities = self::normalizeStringArray($user['community']);
        }
        if (empty($subCommunities) && !empty($user['sub_community'])) {
            $subCommunities = self::normalizeStringArray($user['sub_community']);
        }

        $user['communities'] = $communities;
        $user['sub_communities'] = $subCommunities;

        // ✅ Hide internal columns
        unset(
            $user['communities_json'],
            $user['sub_communities_json'],
            $user['community'],
            $user['sub_community']
        );

        return [
            'success' => true,
            'user'    => $user,
        ];
    }

    /* =====================================================
     * POST /me (UPDATE PROFILE)
     * ===================================================== */

    public static function updateMe($request)
    {
        global $wpdb;

        $identifier = $request->get_param('auth_user') ?: $request->get_param('auth_identifier');
        if (!$identifier) {
            return new WP_Error('unauthorized', 'Invalid or missing token', ['status' => 401]);
        }

        $table = $wpdb->prefix . 'energ_members';
        $where = self::resolveWhereClause($wpdb, $identifier);

        $member = $wpdb->get_row(
            "SELECT id FROM {$table} WHERE {$where} LIMIT 1",
            ARRAY_A
        );

        if (!$member) {
            return new WP_Error('user_not_found', 'User not found', ['status' => 404]);
        }

        $params = (array) $request->get_json_params();

        $update = [];
        $format = [];

        $map = [
            'firstName'    => 'first_name',
            'lastName'     => 'last_name',
            'jobTitle'     => 'job_title',
            'organization' => 'organization',
            'country'      => 'country',
        ];

        foreach ($map as $input => $column) {
            if (isset($params[$input])) {
                $update[$column] = sanitize_text_field($params[$input]);
                $format[] = '%s';
            }
        }

        // Industry
        if (array_key_exists('industry', $params)) {
            $val = $params['industry'];
            $update['industry'] = is_array($val)
                ? implode(', ', array_map('sanitize_text_field', $val))
                : sanitize_text_field((string) $val);
            $format[] = '%s';
        }

        // Sub-industry
        if (isset($params['subIndustry']) || isset($params['sub_industry'])) {
            $val = $params['subIndustry'] ?? $params['sub_industry'];
            $update['sub_industry'] = is_array($val)
                ? implode(', ', array_map('sanitize_text_field', $val))
                : sanitize_text_field((string) $val);
            $format[] = '%s';
        }

        // Communities
        $communities = $params['communities'] ?? null;
        if (is_array($communities)) {
            $clean = array_values(array_unique(array_map('sanitize_text_field', $communities)));
            $update['communities_json'] = wp_json_encode($clean);
            $update['community'] = sanitize_text_field($clean[0] ?? '');
            $format[] = '%s';
            $format[] = '%s';
        }

        // Sub-communities
        $subCommunities = $params['sub_communities'] ?? $params['subCommunities'] ?? null;
        if (is_array($subCommunities)) {
            $clean = array_values(array_unique(array_map('sanitize_text_field', $subCommunities)));
            $update['sub_communities_json'] = wp_json_encode($clean);
            $update['sub_community'] = sanitize_text_field($clean[0] ?? '');
            $format[] = '%s';
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

        // Return fresh profile
        $req = new \WP_REST_Request('GET', '/energ/v1/me');
        $req->set_param('auth_user', $identifier);

        return self::me($req);
    }
}
