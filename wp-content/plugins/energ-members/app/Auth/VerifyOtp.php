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

        $params  = $request->get_json_params();
        $input   = trim($params['identifier'] ?? '');
        $otp     = trim($params['otp'] ?? '');

        if (!$input || !$otp) {
            return new WP_Error(
                'invalid_input',
                'Identifier and OTP required',
                ['status' => 400]
            );
        }

        /** 🔍 Detect identifier */
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

        /** 🔐 Fetch OTP */
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

        /** ⏱ Expired */
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

        /** ✅ OTP VERIFIED → DELETE */
        $wpdb->delete($otpTable, ['id' => $row->id]);

        /** 👤 Member table */
        $membersTable = $wpdb->prefix . 'energ_members';

        $member = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$membersTable} WHERE email = %s OR phone = %s LIMIT 1",
                $identifier,
                $identifier
            )
        );

        /** 📱 PHONE VERIFICATION (REGISTER FLOW) */
        if ($row->context === 'register_phone') {

            if ($member) {
                $wpdb->update(
                    $membersTable,
                    ['phone_verified_at' => current_time('mysql')],
                    ['id' => $member->id]
                );
            }

            return [
                'success' => true,
                'message' => 'Phone number verified successfully'
            ];
        }

        /** 📧 EMAIL LOGIN FLOW */
        $isNewUser = false;

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

        /** 🔐 Issue JWT */
        $jwt = Jwt::issue(
            [
                'sub'   => $identifier,
                'scope' => 'user',
            ],
            15 * 60
        );

        /** 🔁 Refresh token */
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
            'success'       => true,
            'message'       => 'OTP verified',
            'access_token'  => $jwt['token'],
            'refresh_token' => $refreshToken,
            'expires_in'    => $jwt['expires_in'],
            'is_new_user'   => $isNewUser
        ];
    }
}
