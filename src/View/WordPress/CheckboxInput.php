<?php

namespace TinkoffAuth\View\WordPress;

class CheckboxInput extends WordPressComponent
{
    /**
     * @return string
     */
    public function render()
    {
        $checked = get_option($this->optionName) ? 'checked' : '';

        $checkboxString = "<label for='{$this->optionName}'>";
        $checkboxString .= "<input name='{$this->optionName}' type='checkbox' id='{$this->optionName}' {$checked}>";
        $checkboxString .= "Включить</label>";

        return $checkboxString;
    }

}