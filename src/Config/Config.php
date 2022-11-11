<?php

namespace TinkoffAuth\Config;

use TinkoffAuth\Exceptions\UnknownConfig;

abstract class Config
{
    protected static ?Config $instance = null;
    protected array $store = [];
    protected array $availableIndexes = [];

    protected function __construct()
    {
    }

    public static function getInstance(): Config
    {
        if (self::$instance) {
            return self::$instance;
        }

        $className = get_called_class();
        if ( ! is_subclass_of($className, Config::class)) {
            throw new UnknownConfig();
        }

        self::$instance = new $className();

        return self::getInstance();
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