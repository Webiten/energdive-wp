<?php

namespace Energ\Auth;

use WP_Error;

class VerifyOtp
{
    public function handle($request)
    {
        global $wpdb;

        $params     = $request->get_json_params();
        $identifier = trim($params['identifier'] ?? '');
        $otp        = trim($params['otp'] ?? '');

        if (!$identifier || !$otp) {
            return new WP_Error('invalid_input', 'Identifier & OTP required', ['status' => 400]);
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}energ_otps WHERE identifier = %s",
                $identifier
            )
        );

        if (!$row || strtotime($row->expires_at) < time()) {
            return new WP_Error('otp_invalid', 'OTP expired or invalid', ['status' => 401]);
        }

        if (!password_verify($otp, $row->otp_hash)) {
            return new WP_Error('otp_invalid', 'Invalid OTP', ['status' => 401]);
        }

        // 🧹 cleanup
        $wpdb->delete($wpdb->prefix . 'energ_otps', ['id' => $row->id]);

        // 👤 Check member
        $members = $wpdb->prefix . 'energ_members';

        $member = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$members} WHERE email = %s OR phone = %s",
                $identifier,
                $identifier
            )
        );

        $isNewUser = false;

        if (!$member) {
            $wpdb->insert($members, [
                is_email($identifier) ? 'email' : 'phone' => $identifier,
                'signup_mode' => 'otp',
                'created_at'  => current_time('mysql'),
            ]);
            $isNewUser = true;
        }

        // 🔐 Issue JWT
        $jwt = Jwt::issue(
            ['sub' => $identifier, 'scope' => 'user'],
            15 * 60
        );

        // 🔁 Refresh token
        $refresh = bin2hex(random_bytes(32));

        $wpdb->insert($wpdb->prefix . 'energ_refresh_tokens', [
            'identifier' => $identifier,
            'token_hash' => hash('sha256', $refresh),
            'expires_at' => gmdate('Y-m-d H:i:s', time() + (30 * DAY_IN_SECONDS)),
        ]);

        return [
            'success'        => true,
            'new_user'       => $isNewUser,
            'access_token'  => $jwt['token'],
            'refresh_token' => $refresh,
            'expires_in'    => $jwt['expires_in'],
        ];
    }
}
