<?php

namespace Energ\Auth;

use WP_Error;
use Energ\Helpers\CommunityValidator;

class CompleteRegistration
{
    public function handle($request)
    {
        global $wpdb;

        $user = $request->get_param('auth_user'); // from JWT
        if (!$user) {
            return new WP_Error('unauthorized', 'JWT valid but user not resolved', ['status' => 401]);
        }

        $params = $request->get_json_params();
        if (!is_array($params)) {
            $params = [];
        }

        /* ================= REQUIRED FIELDS ================= */

        $required = [
            'first_name',
            'last_name',
            'job_title',
            'organization',
            'country',
            'industry',
            'privacy_accepted',
        ];

        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '' || $params[$field] === null) {
                return new WP_Error('missing_field', "Missing field: {$field}", ['status' => 400]);
            }
        }

        // normalize privacy boolean
        $privacyAccepted = filter_var($params['privacy_accepted'], FILTER_VALIDATE_BOOLEAN);
        if ($privacyAccepted !== true) {
            return new WP_Error('privacy_required', 'Privacy policy must be accepted', ['status' => 400]);
        }

        /* ================= OPTIONAL FIELDS ================= */

        $state = isset($params['state']) ? sanitize_text_field($params['state']) : '';

        // Industry / sub-industry
        $industryVal = $params['industry'];
        $industryVal = is_array($industryVal)
            ? implode(', ', array_map('sanitize_text_field', $industryVal))
            : sanitize_text_field((string) $industryVal);

        $subIndustryVal = $params['sub_industry'] ?? '';
        $subIndustryVal = is_array($subIndustryVal)
            ? implode(', ', array_map('sanitize_text_field', $subIndustryVal))
            : sanitize_text_field((string) $subIndustryVal);

        /* ================= COMMUNITIES ================= */

        $communities = [];
        if (!empty($params['communities']) && is_array($params['communities'])) {
            $communities = array_values(array_unique(array_map('sanitize_text_field', $params['communities'])));
        }

        if (empty($communities) && !empty($params['community'])) {
            $communities = [sanitize_text_field($params['community'])];
        }

        if (empty($communities)) {
            return new WP_Error('missing_field', 'Missing field: community', ['status' => 400]);
        }

        /* ================= SUB-COMMUNITIES ================= */

        $subCommunities = [];
        if (!empty($params['sub_communities']) && is_array($params['sub_communities'])) {
            $subCommunities = array_values(array_unique(array_map('sanitize_text_field', $params['sub_communities'])));
        }

        if (empty($subCommunities) && !empty($params['sub_community'])) {
            $subCommunities = [sanitize_text_field($params['sub_community'])];
        }

        /* ================= VALIDATION ================= */

        foreach ($subCommunities as $sub) {
            $valid = false;
            foreach ($communities as $community) {
                if (CommunityValidator::isValid($community, $sub)) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return new WP_Error(
                    'invalid_community',
                    'Invalid community or sub-community',
                    ['status' => 400]
                );
            }
        }

        /* ================= DATABASE UPDATE ================= */

        $table = $wpdb->prefix . 'energ_members';

        // Safer WHERE resolution
        if (is_numeric($user)) {
            $where = ['user_id' => (int) $user];
        } elseif (is_email($user)) {
            $where = ['email' => sanitize_email($user)];
        } else {
            $where = ['phone' => sanitize_text_field($user)];
        }

        // Fallback lookup if above misses the row
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE email = %s OR phone = %s LIMIT 1",
                $params['email'] ?? '',
                $params['phone'] ?? ''
            )
        );

        if ($existing) {
            $where = ['id' => (int) $existing->id];
        }

        $data = [
            'first_name'           => sanitize_text_field($params['first_name']),
            'last_name'            => sanitize_text_field($params['last_name']),
            'job_title'            => sanitize_text_field($params['job_title']),
            'organization'         => sanitize_text_field($params['organization']),
            'country'              => sanitize_text_field($params['country']),
            'state'                => $state,
            'phone'                => sanitize_text_field($params['phone'] ?? ''),

            // legacy support (keep primary values)
            'community'            => sanitize_text_field($communities[0]),
            'sub_community'        => sanitize_text_field($subCommunities[0] ?? ''),

            'industry'             => $industryVal,
            'sub_industry'         => $subIndustryVal,
            'status'               => 'active',

            // source of truth
            'communities_json'     => wp_json_encode($communities),
            'sub_communities_json' => wp_json_encode($subCommunities),
        ];

        $formats = array_fill(0, count($data), '%s');

        $updated = $wpdb->update($table, $data, $where, $formats);

        if ($updated === false) {
            return new WP_Error('db_error', 'Could not complete registration', ['status' => 500]);
        }

        if ($updated === 0) {
            return new WP_Error('member_not_found', 'Member record not found for this user', ['status' => 404]);
        }

        /* ================= SYNC WITH WORDPRESS USER (HEADER FIX) ================= */

        $wp_user = null;

        if (is_email($user)) {
            $wp_user = get_user_by('email', $user);
        } elseif (is_numeric($user)) {
            $wp_user = get_user_by('id', (int)$user);
        }

        if ($wp_user) {
            update_user_meta($wp_user->ID, 'first_name', sanitize_text_field($params['first_name']));
            update_user_meta($wp_user->ID, 'last_name', sanitize_text_field($params['last_name']));

            wp_update_user([
                'ID' => $wp_user->ID,
                'display_name' =>
                    sanitize_text_field($params['first_name'] . ' ' . $params['last_name']),
            ]);
        }

        return [
            'success' => true,
            'message' => 'Registration completed successfully',
        ];
    }
}
