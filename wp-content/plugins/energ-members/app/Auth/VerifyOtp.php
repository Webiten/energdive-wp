<?php

namespace Energ\Auth;

use WP_Error;

class VerifyOtp
{
    public function handle($request)
    {
        global $wpdb;

        $params = $request->get_json_params();
        $email  = sanitize_email($params['email'] ?? '');
        $otp    = trim($params['otp'] ?? '');

        if (!$email || !$otp) {
            return new WP_Error(
                'invalid_input',
                'Email & OTP required',
                ['status' => 400]
            );
        }

        $otpTable = $wpdb->prefix . 'energ_otps';

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$otpTable} WHERE identifier = %s LIMIT 1",
                $email
            )
        );

        if (!$row) {
            return new WP_Error(
                'otp_not_found',
                'OTP not found',
                ['status' => 400]
            );
        }

        /** ❌ EXPIRED OTP */
        if (strtotime($row->expires_at) < time()) {
            $wpdb->delete($otpTable, ['id' => $row->id]);

            return new WP_Error(
                'otp_expired',
                'OTP expired',
                ['status' => 400]
            );
        }

        /** 🚫 TOO MANY WRONG ATTEMPTS */
        if ($row->attempts >= 5) {
            $wpdb->delete($otpTable, ['id' => $row->id]);

            return new WP_Error(
                'otp_blocked',
                'Too many incorrect attempts. Request a new OTP.',
                ['status' => 403]
            );
        }

        /** ❌ WRONG OTP */
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

        /** 🔐 ISSUE JWT (15 MIN) */
        $jwt = Jwt::issue(
            [
                'sub'   => $email,
                'scope' => 'user'
            ],
            15 * 60
        );

        /** 🔁 CREATE REFRESH TOKEN (30 DAYS) */
        $refreshToken = bin2hex(random_bytes(32));

        $wpdb->insert(
            $wpdb->prefix . 'energ_refresh_tokens',
            [
                'identifier' => $email,
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
            'expires_in'    => $jwt['expires_in']
        ];
    }
}
