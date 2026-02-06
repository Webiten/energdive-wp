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
            return new WP_Error('invalid_input', 'Identifier and OTP required', ['status' => 400]);
        }

        /** 🔍 Detect identifier (email | phone) */
        $detected = Identifier::detect($input);
        if (!$detected) {
            return new WP_Error('invalid_identifier', 'Invalid email or phone', ['status' => 400]);
        }

        $identifier = $detected['value'];
        $type       = $detected['type']; // email | phone

        /** 🔐 Fetch OTP */
        $otpTable = $wpdb->prefix . 'energ_otps';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$otpTable} WHERE identifier = %s LIMIT 1", $identifier)
        );

        if (!$row) {
            return new WP_Error('otp_not_found', 'OTP not found', ['status' => 400]);
        }

        /** ⏱ Expired */
        if (strtotime($row->expires_at) < time()) {
            $wpdb->delete($otpTable, ['id' => $row->id]);
            return new WP_Error('otp_expired', 'OTP expired', ['status' => 400]);
        }

        /** ❌ Wrong OTP */
        if (!password_verify($otp, $row->otp_hash)) {
            $wpdb->update(
                $otpTable,
                ['attempts' => (int)$row->attempts + 1],
                ['id' => $row->id]
            );

            return new WP_Error('invalid_otp', 'Invalid OTP', ['status' => 401]);
        }

        /** ✅ OTP VERIFIED → DELETE */
        $wpdb->delete($otpTable, ['id' => $row->id]);

        /** 👤 Member table */
        $membersTable = $wpdb->prefix . 'energ_members';

        if ($type === 'email') {
            $member = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$membersTable} WHERE email = %s LIMIT 1", $identifier)
            );
        } else {
            $member = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$membersTable} WHERE phone = %s LIMIT 1", $identifier)
            );
        }

        /** 📱 PHONE VERIFICATION (REGISTER FLOW) */
        if ($row->context === 'register_phone') {
            return [
                'success' => true,
                'message' => 'Phone number verified successfully'
            ];
        }

        /** 📧/📱 LOGIN FLOW */
        $isNewUser = false;

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

        // ❌ REMOVE old logic that forced "new user" for pending users
        // (Tum chahte ho: existing user = home, new user = thank-you)
        // Isliye isko hata diya.

        /**
         * ✅ Create refresh session first => get SID (session id)
         */
        $refreshToken = bin2hex(random_bytes(32));
        $refreshTable = $wpdb->prefix . 'energ_refresh_tokens';

        $wpdb->insert(
            $refreshTable,
            [
                'identifier' => $identifier,
                'token_hash' => hash('sha256', $refreshToken),
                'expires_at' => gmdate('Y-m-d H:i:s', time() + (30 * DAY_IN_SECONDS)),
                'revoked'    => 0,
                'revoked_at' => null,
                'created_at' => current_time('mysql', 1),
            ]
        );

        $sid = (int) $wpdb->insert_id;

        /** 🔐 Issue JWT (15 min) with SID */
        $jwt = Jwt::issue(
            [
                'sub'   => $identifier,
                'scope' => 'user',
                'sid'   => $sid,
            ],
            15 * 60
        );

        // -------------------------------------------------------
        // 🔥 WORDPRESS USER SYNC (HEADER-ONLY MODE)
        // -------------------------------------------------------

        $wp_user = null;

        if ($type === 'email') {
            $wp_user = get_user_by('email', $identifier);
        }

        // Derive clean first name
        $firstName = $identifier;
        if (strpos($identifier, '@') !== false) {
            $firstName = explode('@', $identifier)[0];
        }

        if (!$wp_user) {

            $user_id = wp_insert_user([
                'user_login'   => $firstName . '_' . wp_rand(100, 999),
                'user_email'   => $identifier,
                'user_pass'    => wp_generate_password(),
                'display_name' => ucfirst($firstName),
                'role'         => 'subscriber',
            ]);

            if (!is_wp_error($user_id)) {
                $wp_user = get_user_by('id', $user_id);
            }
        }

        if ($wp_user) {

            wp_set_current_user($wp_user->ID);
            wp_set_auth_cookie($wp_user->ID, true);
            do_action('wp_login', $wp_user->user_login, $wp_user);

            // 🔥 ONLY FIRST NAME LOGIC
            $firstName = strpos($identifier, '@') !== false
                ? explode('@', $identifier)[0]
                : $identifier;

            // Sirf pehla word rakho
            $firstName = ucfirst(explode(' ', str_replace(['.', '_'], ' ', $firstName))[0]);

            // Save for Elementor
            update_user_meta($wp_user->ID, 'first_name', $firstName);

            // Fix display name bhi
            wp_update_user([
                'ID' => $wp_user->ID,
                'display_name' => $firstName
            ]);
        }



        // -------------------------------------------------------

        return [
            'success'       => true,
            'message'       => 'OTP verified',
            'access_token'  => $jwt['token'],
            'refresh_token' => $refreshToken,
            'expires_in'    => $jwt['expires_in'],
            'is_new_user'   => $isNewUser,
            'sid'           => $sid,
            'wp_user_id'    => $wp_user ? $wp_user->ID : null
        ];
    }
}
