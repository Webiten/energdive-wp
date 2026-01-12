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
        $input = trim($input);

        // 📧 Email
        if (is_email($input)) {
            return [
                'type'  => 'email',
                'value' => sanitize_email($input),
            ];
        }

        // 📱 Phone (India – 10 digits)
        $phone = preg_replace('/\D/', '', $input);

        if (strlen($phone) === 10) {
            return [
                'type'  => 'phone',
                'value' => $phone,
            ];
        }

        return false;
    }
}
