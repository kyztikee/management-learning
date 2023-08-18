<?php

namespace App\Enums;

enum CivilianStatusEnum: int {
    case ON_PROGRESS = 0;
    case ACCEPTED = 1;
    case REJECTED = 2;

    public static function getString($val): string
    {
        return match ($val) {
            self::ON_PROGRESS => 'Sedang diperiksa',
            self::ACCEPTED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            default => 'Unknown'
        };
    }
}
