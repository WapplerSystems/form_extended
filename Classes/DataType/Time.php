<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\DataType;

class Time
{

    public static function createFromFormat(string $dateFormat, string $dateAsString): int
    {
        [$hours, $minutes] = explode(':', $dateAsString);
        return (int)$hours * 100 + (int)$minutes;
    }

}
