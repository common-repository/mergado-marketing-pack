<?php

namespace Mergado\Manager;

class TokenManager
{
    public static function generateToken(): string
    {
        $token = sha1(get_site_url() . 'MMP2017WP' . uniqid('', true));
        update_option('mmp_token', $token, true);
        return $token;
    }

    public static function generateOrGetTokenIfExists() {
        $token = get_option('mmp_token');

        if ($token === NULL || $token === '' || !$token) {
            $token = self::generateToken();
        }

        return $token;
    }

    public static function getToken() {
        return self::generateOrGetTokenIfExists();
    }

    public static function tokenMatches($requestToken): bool
    {
        return self::generateOrGetTokenIfExists() === $requestToken;
    }
}
