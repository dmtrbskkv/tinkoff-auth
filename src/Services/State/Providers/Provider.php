<?php

namespace TinkoffAuth\Services\State\Providers;

abstract class Provider
{
    protected string $state;

    public function createState(): bool
    {
        $this->state = '';

        return false;
    }

    public function getStateValue(): string
    {
        return $this->state;
    }

    public function validateState(string $string = null): bool
    {
        return false;
    }
}