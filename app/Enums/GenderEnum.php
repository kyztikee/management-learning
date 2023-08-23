<?php

namespace App\Enums;

enum GenderEnum: int {
    case MALE = 0;
    case FEMALE = 1;

    public static function getString($val): string
    {
        return match ($val) {
            self::MALE => 'Laki - laki',
            self::FEMALE => 'Perempuan',
            default => 'Unknown'
        };
    }
}
