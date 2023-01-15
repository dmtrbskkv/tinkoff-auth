<?php

namespace TinkoffAuth\View;

abstract class Component
{
    public function renderInline():void
    {
        echo '';
    }

    public function render(): string
    {
        return '<div> I\'m component</div>';
    }
}