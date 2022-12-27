<?php

namespace TinkoffAuth\Services\State\Providers;

abstract class Provider
{
    protected static string $state;

    public function createState(): bool
    {
        $this->state = '';

        return false;
    }

    public function getStateValue(): string
    {
        return self::$state;
    }

    public function validateState(string $string = null): bool
    {
        return false;
    }
}