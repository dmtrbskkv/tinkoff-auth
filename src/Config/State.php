<?php

namespace TinkoffAuth\Config;

class State extends Config
{
    const PROVIDER = 'provider';

    protected array $availableIndexes = [
        self::PROVIDER
    ];

    protected static ?State $instance = null;

    public static function getInstance(): State
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::getInstance();
    }
}