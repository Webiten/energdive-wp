<?php

namespace Energ\Auth;

use WP_Error;

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

        $required = [
            'first_name',
            'last_name',
            'country',
            'state',
            'community',
            'sub_community',
            'industry',
            'sub_industry',
            'privacy_accepted'
        ];

        foreach ($required as $field) {
            if (empty($params[$field])) {
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

        $table = $wpdb->prefix . 'energ_members';

        $updated = $wpdb->update(
            $table,
            [
                'first_name'    => sanitize_text_field($params['first_name']),
                'last_name'     => sanitize_text_field($params['last_name']),
                'country'       => sanitize_text_field($params['country']),
                'state'         => sanitize_text_field($params['state']),
                'community'     => sanitize_text_field($params['community']),
                'sub_community' => sanitize_text_field($params['sub_community']),
                'industry'      => sanitize_text_field($params['industry']),
                'sub_industry'  => sanitize_text_field($params['sub_industry']),
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

        return [
            'success' => true,
            'message' => 'Registration completed successfully'
        ];
    }
}
