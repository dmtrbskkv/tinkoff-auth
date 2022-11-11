<?php

namespace TinkoffAuth\View\WordPress;

use TinkoffAuth\View\AuthButton;
use TinkoffAuth\View\Component;

class SelectButton extends Component
{
    const SELECT_HOOK_VALUES        = [
        'Самостоятельно расположить' => '',
        'Внутри формы регистрации'   => 'woocommerce_login_form',
        'Ниже формы регистрации'     => 'woocommerce_login_form_end',
        'Выше формы регистрации'     => 'woocommerce_login_form_start'
    ];
    const SELECT_BUTTON_SIZE_VALUES = [
        'Стандартная кнопка' => AuthButton::BUTTON_SIZE_DEFAULT,
        'Большая кнопка'     => AuthButton::BUTTON_SIZE_LARGE,
        'Маленькая кнопка'   => AuthButton::BUTTON_SIZE_SMALL
    ];

    private string $optionName;
    private array $values;

    public function __construct($optionName, $values = [])
    {
        $this->optionName = $optionName;
        $this->values     = $values;
    }

    public function render(): string
    {
        if ( ! function_exists('get_option')) {
            return '';
        }
        $option = get_option($this->optionName);

        $selectString = "<select id='{$this->optionName}' name='{$this->optionName}'>";
        foreach ($this->values as $label => $item) {
            $selected     = $option == $item ? 'selected' : '';
            $selectString .= "<option {$selected} value='{$item}'>{$label}</option>";
        }
        $selectString .= "</select>";

        return $selectString;
    }

}