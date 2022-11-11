<?php

namespace TinkoffAuth\View\WordPress;

use TinkoffAuth\View\Component;

class SettingInput extends Component
{
    private string $optionName;

    public function __construct($optionName)
    {
        $this->optionName = $optionName;
    }

    public function render(): string
    {
        if ( ! function_exists('get_option')) {
            return '';
        }

        $option = get_option($this->optionName);

        $inputString = "<input name='{$this->optionName}'";
        $inputString .= "type='text' id='{$this->optionName}' class='regular-text'";
        $inputString .= "value='{$option}'>";

        return $inputString;
    }

}