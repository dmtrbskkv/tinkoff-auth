<?php

namespace TinkoffAuth\View\WordPress;

class SettingInput extends WordPressComponent
{

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