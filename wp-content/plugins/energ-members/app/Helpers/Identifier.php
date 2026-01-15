<?php

namespace Energ\Helpers;

class Identifier
{
    /**
     * Detect email or phone
     *
     * @param string $input
     * @return array|false
     */
    public static function detect($input)
    {
        $input = trim((string) $input);

        // 📧 Email
        if (is_email($input)) {
            return [
                'type'  => 'email',
                'value' => sanitize_email(strtolower($input)),
            ];
        }

        // 📱 Phone: digits only, allow 8–15 digits (supports country code)
        $phone = preg_replace('/\D+/', '', $input);
        $len   = strlen($phone);

        if ($len >= 8 && $len <= 15) {
            return [
                'type'  => 'phone',
                'value' => $phone,
            ];
        }

        return false;
    }
}
