<?php

namespace TinkoffAuth\Services\State\Providers;

class Session extends Provider
{
    const STATE_NAME = 'tinkoff_auth_state';

    public function createState(): bool
    {
        session_start();

        $this->state = bin2hex(random_bytes(20));

        $_SESSION[self::STATE_NAME] = $this->state;

        return true;
    }

    public function validateState(string $string = null): bool
    {
        $sessionState = $_SESSION[self::STATE_NAME] ?? null;
        if (is_null($sessionState)) {
            return false;
        }

        return $sessionState === $string;
    }

}