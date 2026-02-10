<?php

namespace Energ\Auth;

use WP_Error;
use Energ\Helpers\CommunityValidator;
use Energ\Services\Mailer;

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

        $privacyAccepted = filter_var($params['privacy_accepted'], FILTER_VALIDATE_BOOLEAN);
        if ($privacyAccepted !== true) {
            return new WP_Error('privacy_required', 'Privacy policy must be accepted', ['status' => 400]);
        }

        /* ================= OPTIONAL FIELDS ================= */

        $state = isset($params['state']) ? sanitize_text_field($params['state']) : '';

        $industryVal = is_array($params['industry'])
            ? implode(', ', array_map('sanitize_text_field', $params['industry']))
            : sanitize_text_field((string) $params['industry']);

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

        foreach ($subCommunities as $sub) {
            $valid = false;
            foreach ($communities as $community) {
                if (CommunityValidator::isValid($community, $sub)) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return new WP_Error('invalid_community', 'Invalid community or sub-community', ['status' => 400]);
            }
        }

        /* ================= FIND MEMBER RECORD ================= */

        $table = $wpdb->prefix . 'energ_members';
        $existing = null;

        if (is_numeric($user)) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE user_id = %d LIMIT 1",
                (int) $user
            ));
        } elseif (is_email($user)) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE email = %s LIMIT 1",
                sanitize_email($user)
            ));
        } else {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE phone = %s LIMIT 1",
                sanitize_text_field($user)
            ));
        }

        if (!$existing && !empty($params['email'])) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE email = %s LIMIT 1",
                sanitize_email($params['email'])
            ));
        }

        if (!$existing && !empty($params['phone'])) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE phone = %s LIMIT 1",
                sanitize_text_field($params['phone'])
            ));
        }

        if (!$existing) {
            return new WP_Error('member_not_found', 'Member record not found. Please contact support.', ['status' => 404]);
        }

        /* ================= UPDATE MEMBER ================= */

        $where = ['id' => (int) $existing->id];

        $data = [
            'first_name'   => sanitize_text_field($params['first_name']),
            'last_name'    => sanitize_text_field($params['last_name']),
            'job_title'    => sanitize_text_field($params['job_title']),
            'organization' => sanitize_text_field($params['organization']),
            'country'      => sanitize_text_field($params['country']),
            'state'        => $state,

            // DUPLICATE PHONE SAFE FIX
            'phone' => !empty($params['phone']) && $params['phone'] !== $existing->phone
                ? sanitize_text_field($params['phone'])
                : $existing->phone,

            'community'     => sanitize_text_field($communities[0]),
            'sub_community' => sanitize_text_field($subCommunities[0] ?? ''),

            'industry'     => $industryVal,
            'sub_industry' => $subIndustryVal,
            'status'       => 'active',

            'communities_json'     => wp_json_encode($communities),
            'sub_communities_json' => wp_json_encode($subCommunities),
        ];

        $formats = array_fill(0, count($data), '%s');

        $updated = $wpdb->update($table, $data, $where, $formats);

        if ($updated === false) {
            error_log("ENERG DEBUG - DB Update Failed: " . $wpdb->last_error);
            return new WP_Error('db_error', 'Could not complete registration', ['status' => 500]);
        }

        /* ================= SYNC WORDPRESS USER FIRST ================= */

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

        /* ================= SEND EMAIL (CORRECT WAY FOR YOUR SYSTEM) ================= */

        try {
            error_log("ENERG DEBUG - Sending welcome email to: " . $existing->email);

            $mailer->sendWelcomeEmailByAddress(
                $existing->email,
                $existing->first_name ?? 'Member',
                ucfirst(str_replace('-', ' ', $existing->community))
            );
        } catch (\Exception $e) {
            error_log("ENERG DEBUG - Welcome Email Failed: " . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'Registration completed successfully',
        ];
    }
}
