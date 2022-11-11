<?php

namespace TinkoffAuth\Services\State;

use TinkoffAuth\Config\State as StateConfig;
use TinkoffAuth\Services\State\Providers\Cookies;
use TinkoffAuth\Services\State\Providers\Provider;

class State
{
    private Provider $provider;

    public function __construct(string $provider = null)
    {
        $stateConfig = StateConfig::getInstance();

        if ( ! is_null($provider) && is_subclass_of($provider, Provider::class)) {
            $this->provider = new $provider();
        } else {
            $provider       = $stateConfig->get(StateConfig::PROVIDER) ?? Cookies::class;
            $this->provider = new $provider;
        }
    }

    public function getState(): string
    {
        $this->provider->createState();

        return $this->provider->getStateValue();
    }

    public function validate(string $string): bool
    {
        return $this->provider->validateState($string);
    }
}