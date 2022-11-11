<?php

namespace TinkoffAuth\Config;

class State extends Config
{
    const PROVIDER = 'provider';

    protected array $availableIndexes = [
        self::PROVIDER
    ];
}