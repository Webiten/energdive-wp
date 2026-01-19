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

        // Required fields (community can come from community OR communities[])
        $required = [
            'first_name',
            'last_name',
            'country',
            'industry',
            'privacy_accepted'
        ];

        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '' || $params[$field] === null) {
                return new WP_Error('missing_field', "Missing field: {$field}", ['status' => 400]);
            }
        }

        if ($params['privacy_accepted'] !== true) {
            return new WP_Error('privacy_required', 'Privacy policy must be accepted', ['status' => 400]);
        }

        // Optional fields (safe defaults)
        $state       = isset($params['state']) ? sanitize_text_field($params['state']) : '';
        $subIndustry = isset($params['sub_industry']) ? sanitize_text_field($params['sub_industry']) : '';

        /**
         * ✅ Community handling (support BOTH)
         * - old: community + sub_community
         * - new: communities[] + sub_communities[]
         */
        $communities = [];
        if (isset($params['communities']) && is_array($params['communities'])) {
            $communities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['communities']))));
        }

        // fallback to single community
        if (!$communities) {
            $singleCommunity = isset($params['community']) ? sanitize_text_field($params['community']) : '';
            if ($singleCommunity !== '') $communities = [$singleCommunity];
        }

        if (!$communities) {
            return new WP_Error('missing_field', 'Missing field: community', ['status' => 400]);
        }

        // Sub-communities array
        $subCommunities = [];
        if (isset($params['sub_communities']) && is_array($params['sub_communities'])) {
            $subCommunities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['sub_communities']))));
        }

        // fallback to single sub_community
        if (!$subCommunities) {
            $singleSub = isset($params['sub_community']) ? sanitize_text_field($params['sub_community']) : '';
            if ($singleSub !== '') $subCommunities = [$singleSub];
        }

        /**
         * ✅ Validation rules
         * - If sub-communities provided, each sub must be valid for AT LEAST ONE selected community
         */
        if (!empty($subCommunities)) {
            foreach ($subCommunities as $sub) {
                $ok = false;
                foreach ($communities as $c) {
                    if (CommunityValidator::isValid($c, $sub)) {
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
         * ✅ Choose PRIMARY values (backward compatibility)
         * - primaryCommunity = first selected
         * - primarySubCommunity = first valid sub for that primary community, else empty
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
            // if none match primary, still keep first selected as fallback (optional)
            if ($primarySubCommunity === '') {
                $primarySubCommunity = $subCommunities[0];
            }
        }

        $table = $wpdb->prefix . 'energ_members';

        // ✅ Store JSON safely
        $communitiesJson = wp_json_encode($communities);
        $subCommunitiesJson = wp_json_encode($subCommunities);

        $dataToUpdate = [
            'first_name'         => sanitize_text_field($params['first_name']),
            'last_name'          => sanitize_text_field($params['last_name']),
            'country'            => sanitize_text_field($params['country']),
            'state'              => $state,
            'community'          => $primaryCommunity,
            'sub_community'      => $primarySubCommunity,
            'industry'           => sanitize_text_field($params['industry']),
            'sub_industry'       => $subIndustry,
            'status'             => 'active',
            'communities_json'   => $communitiesJson,
            'sub_communities_json' => $subCommunitiesJson,
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
            'message' => 'Registration completed successfully'
        ];
    }
}
