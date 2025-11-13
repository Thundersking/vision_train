<?php

declare(strict_types=1);

namespace App\Support\Enums;

enum Timezone: int
{
    case KALININGRAD = 2;
    case MOSCOW = 3;
    case SAMARA = 4;
    case YEKATERINBURG = 5;
    case OMSK = 6;
    case KRASNOYARSK = 7;
    case IRKUTSK = 8;
    case YAKUTSK = 9;
    case VLADIVOSTOK = 10;
    case MAGADAN = 11;
    case KAMCHATKA = 12;

    /**
     * Получить отображаемое название таймзоны
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::KALININGRAD => 'Калининград (UTC+2)',
            self::MOSCOW => 'Москва (UTC+3)',
            self::SAMARA => 'Самара (UTC+4)',
            self::YEKATERINBURG => 'Екатеринбург (UTC+5)',
            self::OMSK => 'Омск (UTC+6)',
            self::KRASNOYARSK => 'Красноярск (UTC+7)',
            self::IRKUTSK => 'Иркутск (UTC+8)',
            self::YAKUTSK => 'Якутск (UTC+9)',
            self::VLADIVOSTOK => 'Владивосток (UTC+10)',
            self::MAGADAN => 'Магадан (UTC+11)',
            self::KAMCHATKA => 'Камчатка (UTC+12)',
        };
    }

    /**
     * Получить краткое название города
     */
    public function getCityName(): string
    {
        return match ($this) {
            self::KALININGRAD => 'Калининград',
            self::MOSCOW => 'Москва',
            self::SAMARA => 'Самара',
            self::YEKATERINBURG => 'Екатеринбург',
            self::OMSK => 'Омск',
            self::KRASNOYARSK => 'Красноярск',
            self::IRKUTSK => 'Иркутск',
            self::YAKUTSK => 'Якутск',
            self::VLADIVOSTOK => 'Владивосток',
            self::MAGADAN => 'Магадан',
            self::KAMCHATKA => 'Камчатка',
        };
    }

    /**
     * Получить таймзону по офсету в минутах
     */
    public static function fromMinutes(int $minutes): ?self
    {
        $hours = intval($minutes / 60);
        return self::tryFrom($hours);
    }

    /**
     * Получить офсет в минутах
     */
    public function getOffsetMinutes(): int
    {
        return $this->value * 60;
    }

    /**
     * Получить офсет в формате для Carbon (+03:00)
     */
    public function getCarbonOffset(): string
    {
        $hours = $this->value;
        $sign = $hours >= 0 ? '+' : '-';
        $absHours = abs($hours);

        return sprintf('%s%02d:00', $sign, $absHours);
    }

    /**
     * Получить массив для select опций
     */
    public static function forDropdown(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($timezone) => [$timezone->value => $timezone->getDisplayName()])
            ->toArray();
    }

    /**
     * Получить таймзону по умолчанию (Москва)
     */
    public static function default(): self
    {
        return self::MOSCOW;
    }
}
