<?php

namespace TinkoffAuth\View\WordPress;

use TinkoffAuth\View\AuthButton\AuthButton;
use TinkoffAuth\View\Common\OptionSelect as OptionSelectAbstract;

class OptionSelect extends WordPressComponent
{
    const SELECT_HOOK_VALUES          = [
        'Самостоятельно расположить' => '',
        'Внутри формы регистрации'   => 'woocommerce_login_form',
        'Ниже формы регистрации'     => 'woocommerce_login_form_end',
        'Выше формы регистрации'     => 'woocommerce_login_form_start'
    ];
    const SELECT_HOOK_CHECKOUT_VALUES = [
        'Самостоятельно расположить' => '',
        'Выше деталей заказа'        => 'woocommerce_checkout_billing',
        'Внутри деталей заказа'      => 'woocommerce_checkout_shipping',
        'После деталей заказа'       => 'woocommerce_checkout_after_customer_details',
    ];

    private array $values;

    public function __construct($optionName, $values = [])
    {
        parent::__construct($optionName);
        $this->values = $values;
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

    public static function sizes(): array
    {
        return OptionSelectAbstract::SELECT_BUTTON_SIZE_VALUES;
    }

    public static function colors(): array
    {
        return OptionSelectAbstract::SELECT_BUTTON_COLORS_VALUES;
    }

    public static function languages(): array
    {
        return OptionSelectAbstract::SELECT_BUTTON_LANG_VALUES;
    }
}