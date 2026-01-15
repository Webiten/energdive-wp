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

        // 📱 Phone: normalize to digits only, support international lengths (E.164 max 15)
        $phone = preg_replace('/\D+/', '', $input);
        $len   = strlen($phone);

        // Accept 10-digit India and also numbers with country code (e.g., 91XXXXXXXXXX)
        if ($len >= 8 && $len <= 15) {
            return [
                'type'  => 'phone',
                'value' => $phone,
            ];
        }

        return false;
    }
}
