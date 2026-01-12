<?php

namespace Energ\Auth;

use WP_Error;
use Energ\Auth\Jwt;
use Energ\Helpers\Identifier;

class VerifyOtp
{
    public function handle($request)
    {
        global $wpdb;

        $params = $request->get_json_params();
        $input  = trim($params['identifier'] ?? '');
        $otp    = trim($params['otp'] ?? '');

        if (!$input || !$otp) {
            return new WP_Error(
                'invalid_input',
                'Identifier and OTP required',
                ['status' => 400]
            );
        }

        /** 🔍 Detect identifier (email / phone) */
        $detected = Identifier::detect($input);
        if (!$detected) {
            return new WP_Error(
                'invalid_identifier',
                'Invalid email or phone',
                ['status' => 400]
            );
        }

        $identifier = $detected['value'];
        $type       = $detected['type']; // email | phone

        /** 🔐 OTP lookup */
        $otpTable = $wpdb->prefix . 'energ_otps';

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$otpTable} WHERE identifier = %s LIMIT 1",
                $identifier
            )
        );

        if (!$row) {
            return new WP_Error(
                'otp_not_found',
                'OTP not found',
                ['status' => 400]
            );
        }

        /** ⏱ Expired OTP */
        if (strtotime($row->expires_at) < time()) {
            $wpdb->delete($otpTable, ['id' => $row->id]);

            return new WP_Error(
                'otp_expired',
                'OTP expired',
                ['status' => 400]
            );
        }

        /** ❌ Wrong OTP */
        if (!password_verify($otp, $row->otp_hash)) {
            $wpdb->update(
                $otpTable,
                ['attempts' => $row->attempts + 1],
                ['id' => $row->id]
            );

            return new WP_Error(
                'invalid_otp',
                'Invalid OTP',
                ['status' => 401]
            );
        }

        /** ✅ OTP VERIFIED — CLEANUP */
        $wpdb->delete($otpTable, ['id' => $row->id]);

        /** 👤 Ensure member exists */
        $membersTable = $wpdb->prefix . 'energ_members';
        $isNewUser    = false;

        $member = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$membersTable} WHERE email = %s OR phone = %s LIMIT 1",
                $identifier,
                $identifier
            )
        );

        if (!$member) {
            $wpdb->insert(
                $membersTable,
                [
                    $type === 'email' ? 'email' : 'phone' => $identifier,
                    'signup_mode' => 'otp',
                    'created_at'  => current_time('mysql'),
                ]
            );

            $isNewUser = true;
        }

        /** 🔐 Issue JWT (15 minutes) */
        $jwt = Jwt::issue(
            [
                'sub'   => $identifier,
                'scope' => 'user',
            ],
            15 * 60
        );

        /** 🔁 Create refresh token (30 days) */
        $refreshToken = bin2hex(random_bytes(32));

        $wpdb->insert(
            $wpdb->prefix . 'energ_refresh_tokens',
            [
                'identifier' => $identifier,
                'token_hash' => hash('sha256', $refreshToken),
                'expires_at' => gmdate(
                    'Y-m-d H:i:s',
                    time() + (30 * DAY_IN_SECONDS)
                ),
            ]
        );

        return [
            'success'      => true,
            'message'      => 'OTP verified',
            'access_token' => $jwt['token'],
            'refresh_token'=> $refreshToken,
            'expires_in'   => $jwt['expires_in'],
            'is_new_user'  => $isNewUser,
        ];
    }
}
