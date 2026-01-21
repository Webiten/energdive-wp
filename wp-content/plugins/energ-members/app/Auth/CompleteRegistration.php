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

        if ($params['privacy_accepted'] !== true) {
            return new WP_Error('privacy_required', 'Privacy policy must be accepted', ['status' => 400]);
        }

        /* ================= OPTIONAL FIELDS ================= */

        $state = isset($params['state']) ? sanitize_text_field($params['state']) : '';

        // Industry / sub-industry (string OR array)
        $industryVal = $params['industry'] ?? '';
        if (is_array($industryVal)) {
            $industryVal = implode(', ', array_values(array_unique(array_filter(array_map('sanitize_text_field', $industryVal)))));
        } else {
            $industryVal = sanitize_text_field((string) $industryVal);
        }

        $subIndustryVal = $params['sub_industry'] ?? '';
        if (is_array($subIndustryVal)) {
            $subIndustryVal = implode(', ', array_values(array_unique(array_filter(array_map('sanitize_text_field', $subIndustryVal)))));
        } else {
            $subIndustryVal = sanitize_text_field((string) $subIndustryVal);
        }

        /* ================= COMMUNITIES ================= */

        // New: communities (array)
        $communities = [];
        if (isset($params['communities']) && is_array($params['communities'])) {
            $communities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['communities']))));
        }

        // Old fallback: community (string)
        if (empty($communities)) {
            $singleCommunity = isset($params['community'] ) ? sanitize_text_field($params['community']) : '';
            if ($singleCommunity !== '') {
                $communities = [$singleCommunity];
            }
        }

        if (empty($communities)) {
            return new WP_Error('missing_field', 'Missing field: community', ['status' => 400]);
        }

        /* ================= SUB-COMMUNITIES ================= */

        $subCommunities = [];
        if (isset($params['sub_communities']) && is_array($params['sub_communities'])) {
            $subCommunities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['sub_communities']))));
        }

        // Old fallback
        if (empty($subCommunities)) {
            $singleSub = isset($params['sub_community']) ? sanitize_text_field($params['sub_community']) : '';
            if ($singleSub !== '') {
                $subCommunities = [$singleSub];
            }
        }

        /* ================= VALIDATION ================= */

        if (!empty($subCommunities)) {
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
        }

        /* ================= DATABASE UPDATE ================= */

        $primaryCommunity = $communities[0] ?? '';

        $table = $wpdb->prefix . 'energ_members';

        $dataToUpdate = [
            'first_name'           => sanitize_text_field($params['first_name']),
            'last_name'            => sanitize_text_field($params['last_name']),
            'job_title'            => sanitize_text_field($params['job_title']),
            'organization'         => sanitize_text_field($params['organization']),
            'country'              => sanitize_text_field($params['country']),
            'state'                => $state,

            // ✅ Legacy column (ONLY primary community)
            'community'            => sanitize_text_field($primaryCommunity),

            // ❌ STOP using single sub_community (kills multi-select)
            'sub_community'        => null,

            'industry'             => $industryVal,
            'sub_industry'         => $subIndustryVal,
            'status'               => 'active',

            // ✅ SOURCE OF TRUTH
            'communities_json'     => wp_json_encode($communities),
            'sub_communities_json' => wp_json_encode($subCommunities),
        ];

        $where = [ is_email($user) ? 'email' : 'phone' => $user ];

        $updated = $wpdb->update($table, $dataToUpdate, $where);

        if ($updated === false) {
            return new WP_Error('db_error', 'Could not complete registration', ['status' => 500]);
        }

        if ($updated === 0) {
            return new WP_Error('member_not_found', 'Member record not found for this user', ['status' => 404]);
        }

        return [
            'success' => true,
            'message' => 'Registration completed successfully',
        ];
    }
}
