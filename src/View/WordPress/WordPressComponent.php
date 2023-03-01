<?php

namespace TinkoffAuth\View\WordPress;

use TinkoffAuth\View\Component;

abstract class WordPressComponent extends Component
{
    /**
     * @var string
     */
    protected $optionName;

    public function __construct($optionName)
    {
        $this->optionName = $optionName;
    }
}