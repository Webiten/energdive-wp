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

        /** 🔍 Detect identifier (email | phone) */
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
                ['attempts' => (int)$row->attempts + 1],
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

        /**
         * IMPORTANT:
         * - Email flow should match by email only
         * - Phone flow should match by phone only
         * This avoids accidentally matching email to phone column, etc.
         */
        if ($type === 'email') {
            $member = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$membersTable} WHERE email = %s LIMIT 1",
                    $identifier
                )
            );
        } else {
            $member = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$membersTable} WHERE phone = %s LIMIT 1",
                    $identifier
                )
            );
        }

        /** 📱 PHONE VERIFICATION (REGISTER FLOW) */
        if ($row->context === 'register_phone') {
            // Your current wp_energ_members schema does NOT contain phone_verified_at.
            // So we only confirm OTP verification here (and keep response stable).
            return [
                'success' => true,
                'message' => 'Phone number verified successfully'
            ];
        }

        /** 📧 EMAIL LOGIN FLOW */
        $isNewUser = false;

        // If no member record, create it as pending onboarding
        if (!$member) {
            $wpdb->insert(
                $membersTable,
                [
                    $type === 'email' ? 'email' : 'phone' => $identifier,
                    'signup_mode' => 'otp',
                    'status'      => 'pending',
                    'created_at'  => current_time('mysql'),
                ]
            );

            $isNewUser = true;
            $member = $wpdb->get_row(
                $wpdb->prepare(
                    $type === 'email'
                        ? "SELECT * FROM {$membersTable} WHERE email = %s LIMIT 1"
                        : "SELECT * FROM {$membersTable} WHERE phone = %s LIMIT 1",
                    $identifier
                )
            );
        }

        /**
         * CRITICAL FIX:
         * Even if member exists, if profile is NOT active, user must go to RegisterPage.
         * So treat pending/blocked as "new user" for routing purposes.
         */
        if ($member && isset($member->status) && $member->status !== 'active') {
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
