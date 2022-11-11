<?php

namespace TinkoffAuth\View;

class AuthButton extends Component
{
    const BUTTON_SIZE_DEFAULT = 'size_default';
    const BUTTON_SIZE_SMALL   = 'size_small';
    const BUTTON_SIZE_LARGE   = 'size_large';

    const AVAILABLE_SIZES = [
        self::BUTTON_SIZE_DEFAULT,
        self::BUTTON_SIZE_SMALL,
        self::BUTTON_SIZE_LARGE,
    ];

    const SIZES_CLASSES = [
        self::BUTTON_SIZE_SMALL => 'button-tinkoff-auth-small',
        self::BUTTON_SIZE_LARGE => 'button-tinkoff-auth-large',
    ];

    private static bool $styleInjected = false;

    private string $link;
    private ?string $buttonSize = null;

    public function __construct($link, $buttonSize = null)
    {
        $this->link = $link;
        if (in_array($buttonSize, self::AVAILABLE_SIZES)) {
            $this->buttonSize = $buttonSize;
        }
    }

    public function render(): string
    {
        $additionalClass = self::SIZES_CLASSES[$this->buttonSize] ?? '';

        $string = $this->styles();
        $string .= "<a class='button-tinkoff-auth {$additionalClass}' href='{$this->link}'>";
        $string .= "<span>Войти с Тинькофф</span>";
        $string .= (new TinkoffLogo())->render();
        $string .= "</a>";

        return $string;
    }

    private function styles()
    {
        if (self::$styleInjected) {
            return '';
        }
        $styles = file_get_contents(__DIR__ . '/AuthButton.css');
        $styles = "<style>{$styles}</style>";

        self::$styleInjected = true;

        return $styles;
    }
}