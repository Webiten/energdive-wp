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

        // Required fields (community can come from community OR communities[])
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

        // Optional fields (safe defaults)
        $state       = isset($params['state']) ? sanitize_text_field($params['state']) : '';
        $subIndustry = isset($params['sub_industry']) ? sanitize_text_field($params['sub_industry']) : '';

        /**
         * ✅ Community handling (support BOTH)
         * - old: community + sub_community
         * - new: communities[] + sub_communities[]
         *
         * IMPORTANT:
         * Your CommunityValidator::list() currently uses LABELS (e.g. "Oil & Gas", "Upstream")
         * while the UI sends SLUGS (e.g. "oil-gas", "upstream").
         *
         * So we normalize input to labels BEFORE validating/storing in primary columns.
         */

        // 1) Normalize communities
        $communities = [];
        if (isset($params['communities']) && is_array($params['communities'])) {
            $communities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['communities']))));
        }

        // fallback to single community
        if (empty($communities)) {
            $singleCommunity = isset($params['community']) ? sanitize_text_field($params['community']) : '';
            if ($singleCommunity !== '') {
                $communities = [$singleCommunity];
            }
        }

        if (empty($communities)) {
            return new WP_Error('missing_field', 'Missing field: community', ['status' => 400]);
        }

        // 2) Normalize sub-communities
        $subCommunities = [];
        if (isset($params['sub_communities']) && is_array($params['sub_communities'])) {
            $subCommunities = array_values(array_unique(array_filter(array_map('sanitize_text_field', $params['sub_communities']))));
        }

        // fallback to single sub_community
        if (empty($subCommunities)) {
            $singleSub = isset($params['sub_community']) ? sanitize_text_field($params['sub_community']) : '';
            if ($singleSub !== '') {
                $subCommunities = [$singleSub];
            }
        }

        /**
         * ✅ Build slug->label maps from CommunityValidator::list()
         * This allows UI slugs to validate against the existing label-based list.
         */
        $list = CommunityValidator::list();

        // communitySlug => communityLabel
        $communitySlugToLabel = [];
        // subSlug => subLabel (not unique globally, but OK for your current set)
        $subSlugToLabel = [];

        foreach ($list as $communityLabel => $subs) {
            $cSlug = sanitize_title($communityLabel);
            $communitySlugToLabel[$cSlug] = $communityLabel;

            foreach ((array) $subs as $subLabel) {
                $sSlug = sanitize_title($subLabel);
                $subSlugToLabel[$sSlug] = $subLabel;
            }
        }

        // Convert incoming communities/subs to LABELS if they are SLUGS
        $communitiesLabel = array_map(function ($c) use ($communitySlugToLabel) {
            $c = (string) $c;
            $key = sanitize_title($c);
            return $communitySlugToLabel[$key] ?? $c; // if already label, it will pass through
        }, $communities);

        $subCommunitiesLabel = array_map(function ($s) use ($subSlugToLabel) {
            $s = (string) $s;
            $key = sanitize_title($s);
            return $subSlugToLabel[$key] ?? $s; // if already label, it will pass through
        }, $subCommunities);

        // De-dupe after mapping
        $communitiesLabel = array_values(array_unique(array_filter($communitiesLabel)));
        $subCommunitiesLabel = array_values(array_unique(array_filter($subCommunitiesLabel)));

        /**
         * ✅ Validation rules
         * - If sub-communities provided, each sub must be valid for AT LEAST ONE selected community
         */
        if (!empty($subCommunitiesLabel)) {
            foreach ($subCommunitiesLabel as $subLabel) {
                $ok = false;
                foreach ($communitiesLabel as $communityLabel) {
                    if (CommunityValidator::isValid($communityLabel, $subLabel)) {
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
         * - primaryCommunity = first selected (LABEL)
         * - primarySubCommunity = first valid sub for that primary community, else empty
         */
        $primaryCommunity = $communitiesLabel[0];
        $primarySubCommunity = '';

        if (!empty($subCommunitiesLabel)) {
            foreach ($subCommunitiesLabel as $subLabel) {
                if (CommunityValidator::isValid($primaryCommunity, $subLabel)) {
                    $primarySubCommunity = $subLabel;
                    break;
                }
            }
            // If none match the primary community, keep empty.
            // (Do NOT force an invalid pair into primary columns.)
        }

        $table = $wpdb->prefix . 'energ_members';

        // ✅ Store JSON safely (store BOTH raw slugs + labels if you want; here: store slugs in json)
        // If you prefer labels in JSON, replace $communities/$subCommunities below with $communitiesLabel/$subCommunitiesLabel.
        $communitiesJson = wp_json_encode(array_values($communities));
        $subCommunitiesJson = wp_json_encode(array_values($subCommunities));

        $dataToUpdate = [
            'first_name'            => sanitize_text_field($params['first_name']),
            'last_name'             => sanitize_text_field($params['last_name']),
            'country'               => sanitize_text_field($params['country']),
            'state'                 => $state,

            // ✅ primary columns store LABELS (because validator/list is label-based)
            'community'             => $primaryCommunity,
            'sub_community'         => $primarySubCommunity,

            'industry'              => sanitize_text_field($params['industry']),
            'sub_industry'          => $subIndustry,
            'status'                => 'active',

            // ✅ JSON columns store SLUG arrays (or switch to labels if you prefer)
            'communities_json'      => $communitiesJson,
            'sub_communities_json'  => $subCommunitiesJson,
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
