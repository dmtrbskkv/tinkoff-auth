<?php

namespace TinkoffAuth\View;

abstract class Component
{
    public function render(): string
    {
        return '<div> I\'m component</div>';
    }
}