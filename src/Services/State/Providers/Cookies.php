<?php

namespace TinkoffAuth\Services\State\Providers;

class Cookies extends Provider
{
    const COOKIE_NAME = 'tinkoff_auth_state';

    public function createState(): bool
    {
        $cookieState = $_COOKIE[self::COOKIE_NAME] ?? null;
        if ($cookieState) {
            self::$state = $cookieState;

            return true;
        }

        self::$state = bin2hex(random_bytes(20));
        setcookie(self::COOKIE_NAME, self::$state, null, '/', "", true);

        return true;
    }

    public function validateState(string $string = null): bool
    {
        $cookieState = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (is_null($cookieState)) {
            return false;
        }

        return $cookieState === $string;
    }

}