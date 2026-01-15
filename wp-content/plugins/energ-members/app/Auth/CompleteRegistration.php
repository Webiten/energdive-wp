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

        // Required fields (match your UI: state/sub_community/sub_industry are optional)
        $required = [
            'first_name',
            'last_name',
            'country',
            'community',
            'industry',
            'privacy_accepted'
        ];

        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '' || $params[$field] === null) {
                return new WP_Error(
                    'missing_field',
                    "Missing field: {$field}",
                    ['status' => 400]
                );
            }
        }

        if ($params['privacy_accepted'] !== true) {
            return new WP_Error(
                'privacy_required',
                'Privacy policy must be accepted',
                ['status' => 400]
            );
        }

        // Optional fields (safe defaults)
        $state        = isset($params['state']) ? sanitize_text_field($params['state']) : '';
        $subCommunity = isset($params['sub_community']) ? sanitize_text_field($params['sub_community']) : '';
        $subIndustry  = isset($params['sub_industry']) ? sanitize_text_field($params['sub_industry']) : '';

        // Validate community/sub-community only if sub_community is provided
        if ($subCommunity !== '' && !CommunityValidator::isValid($params['community'], $subCommunity)) {
            return new WP_Error(
                'invalid_community',
                'Invalid community or sub-community',
                ['status' => 400]
            );
        }

        $table = $wpdb->prefix . 'energ_members';

        $updated = $wpdb->update(
            $table,
            [
                'first_name'    => sanitize_text_field($params['first_name']),
                'last_name'     => sanitize_text_field($params['last_name']),
                'country'       => sanitize_text_field($params['country']),
                'state'         => $state,
                'community'     => sanitize_text_field($params['community']),
                'sub_community' => $subCommunity,
                'industry'      => sanitize_text_field($params['industry']),
                'sub_industry'  => $subIndustry,
                'status'        => 'active',
            ],
            [
                is_email($user) ? 'email' : 'phone' => $user
            ]
        );

        if ($updated === false) {
            return new WP_Error(
                'db_error',
                'Could not complete registration',
                ['status' => 500]
            );
        }

        // If no row matched, update returns 0 (not false). That likely means user row doesn't exist.
        // In that case, return a clean error.
        if ($updated === 0) {
            return new WP_Error(
                'member_not_found',
                'Member record not found for this user',
                ['status' => 404]
            );
        }

        return [
            'success' => true,
            'message' => 'Registration completed successfully'
        ];
    }
}
