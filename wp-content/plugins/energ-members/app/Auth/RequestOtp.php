<?php
namespace Energ\Auth;

use WP_Error;

class RequestOtp {

    public function handle($request) {
        $params = $request->get_json_params();
        $email  = sanitize_email($params['email'] ?? '');

        if (!$email || !is_email($email)) {
            return new WP_Error('invalid_email', 'Invalid email', ['status' => 400]);
        }

        return [
            'success' => true,
            'message' => 'OTP generation reached'
        ];
    }
}
