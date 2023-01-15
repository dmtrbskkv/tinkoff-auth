<?php

namespace TinkoffAuth\View\Bitrix;

use TinkoffAuth\View\Common\OptionSelect as OptionSelectAbstract;

class OptionSelect extends BitrixComponent
{
    public static function sizes(): array
    {
        return array_flip(OptionSelectAbstract::SELECT_BUTTON_SIZE_VALUES);
    }

    public static function colors(): array
    {
        return array_flip(OptionSelectAbstract::SELECT_BUTTON_COLORS_VALUES);
    }

    public static function languages(): array
    {
        return array_flip(OptionSelectAbstract::SELECT_BUTTON_LANG_VALUES);
    }
}