<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum Locale: string implements HasColor, HasIcon, HasLabel
{
    case English = 'en';
    case Georgian = 'ka';

    public static function current(): self
    {
        return self::tryFrom(app()->getLocale()) ?? self::English;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::available())
            ->mapWithKeys(fn (self $locale): array => [$locale->value => $locale->getLabel()])
            ->all();
    }

    /**
     * @return array<int, self>
     */
    public static function available(): array
    {
        $availableLocales = config('app.available_locales', [config('app.locale')]);

        return array_values(array_filter(
            self::cases(),
            fn (self $locale): bool => in_array($locale->value, $availableLocales, true),
        ));
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            fn (self $locale): string => $locale->value,
            self::available(),
        );
    }

    public function getLabel(): string
    {
        return __("locale.{$this->value}");
    }

    public function getIcon(): Heroicon
    {
        return app()->isLocale($this->value) ? Heroicon::Check : Heroicon::Language;
    }

    public function getColor(): string
    {
        return app()->isLocale($this->value) ? 'primary' : 'gray';
    }
}
