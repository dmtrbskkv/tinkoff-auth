<?php

namespace TinkoffAuth\Config;

use TinkoffAuth\Exceptions\UnknownConfig;

abstract class Config
{
    protected array $store = [];
    protected array $availableIndexes = [];

    protected function __construct()
    {
    }

    public function get($index)
    {
        return $this->store[$index] ?? null;
    }

    public function push($index, $value): bool
    {
        if ( ! in_array($index, $this->availableIndexes)) {
            return false;
        }
        $this->store[$index] = $value;

        return true;
    }

    public function remove($index)
    {
        unset($this->store[$index]);
    }
}