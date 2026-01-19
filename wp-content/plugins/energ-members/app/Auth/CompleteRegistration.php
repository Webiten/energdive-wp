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

        // Required fields
        $required = [
            'first_name',
            'last_name',
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

        // Optional fields
        $state       = isset($params['state']) ? sanitize_text_field($params['state']) : '';
        $subIndustry = isset($params['sub_industry']) ? sanitize_text_field($params['sub_industry']) : '';

        /**
         * Communities (support both):
         * - old: community (string)
         * - new: communities (array)
         */
        $communities = [];
        if (isset($params['communities']) && is_array($params['communities'])) {
            $communities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['communities']))));
        }

        if (empty($communities)) {
            $singleCommunity = isset($params['community']) ? sanitize_text_field($params['community']) : '';
            if ($singleCommunity !== '') {
                $communities = [$singleCommunity];
            }
        }

        if (empty($communities)) {
            return new WP_Error('missing_field', 'Missing field: community', ['status' => 400]);
        }

        /**
         * Sub-communities (support both):
         * - old: sub_community (string)
         * - new: sub_communities (array)
         */
        $subCommunities = [];
        if (isset($params['sub_communities']) && is_array($params['sub_communities'])) {
            $subCommunities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['sub_communities']))));
        }

        if (empty($subCommunities)) {
            $singleSub = isset($params['sub_community']) ? sanitize_text_field($params['sub_community']) : '';
            if ($singleSub !== '') {
                $subCommunities = [$singleSub];
            }
        }

        /**
         * ✅ Validation:
         * If sub-communities are provided, each sub must be valid for at least one selected community.
         *
         * IMPORTANT: This expects CommunityValidator::isValid() to support slugs
         * (which you already confirmed exists via slugList()).
         */
        if (!empty($subCommunities)) {
            foreach ($subCommunities as $sub) {
                $ok = false;
                foreach ($communities as $community) {
                    if (CommunityValidator::isValid($community, $sub)) {
                        $ok = true;
                        break;
                    }
                }
                if (!$ok) {
                    return new WP_Error(
                        'invalid_community',
                        'Invalid community or sub-community',
                        ['status' => 400]
                    );
                }
            }
        }

        /**
         * ✅ Primary columns (backward compatible)
         * Store first selected community and a sub-community that matches it (if available)
         */
        $primaryCommunity = $communities[0];
        $primarySubCommunity = '';

        if (!empty($subCommunities)) {
            foreach ($subCommunities as $sub) {
                if (CommunityValidator::isValid($primaryCommunity, $sub)) {
                    $primarySubCommunity = $sub;
                    break;
                }
            }
        }

        $table = $wpdb->prefix . 'energ_members';

        $dataToUpdate = [
            'first_name'           => sanitize_text_field($params['first_name']),
            'last_name'            => sanitize_text_field($params['last_name']),
            'country'              => sanitize_text_field($params['country']),
            'state'                => $state,

            // ✅ store SLUGS in main columns
            'community'            => sanitize_text_field($primaryCommunity),
            'sub_community'        => sanitize_text_field($primarySubCommunity),

            'industry'             => sanitize_text_field($params['industry']),
            'sub_industry'         => $subIndustry,
            'status'               => 'active',

            // ✅ store arrays as JSON (SLUGS)
            'communities_json'     => wp_json_encode(array_values($communities)),
            'sub_communities_json' => wp_json_encode(array_values($subCommunities)),
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
