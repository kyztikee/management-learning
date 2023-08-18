<?php

namespace App\Enums;

enum UserRoleEnum: int {
    case CIVILIAN = 0;
    case LURAH = 1;
    case RT = 2;
    CASE RW = 3;

    public static function getString($val): string
    {
        return match ($val) {
            self::CIVILIAN => 'Warga',
            self::LURAH => 'LURAH',
            self::RT => 'RT',
            self::RW => 'RW',
            default => 'Unknown'
        };
    }
}
