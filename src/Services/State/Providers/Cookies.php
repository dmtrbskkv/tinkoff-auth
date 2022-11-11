<?php

namespace TinkoffAuth\Services\State\Providers;

class Cookies extends Provider
{
    const COOKIE_NAME = 'tinkoff_auth_state';

    public function createState(): bool
    {
        $this->state = bin2hex(random_bytes(20));
        setcookie(self::COOKIE_NAME, $this->state, time() + 60 * 60, '/');

        return true;
    }

    public function validateState($string): bool
    {
        $cookieState = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (is_null($cookieState)) {
            return false;
        }

        return $cookieState == $string;
    }

}