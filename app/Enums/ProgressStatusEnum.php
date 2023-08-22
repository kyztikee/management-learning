<?php

namespace App\Enums;

enum ProgressStatusEnum: int {
    case REVISE = 0;
    case COMPLETE = 1;

    public static function getString($val): string
    {
        return match ($val) {
            self::REVISE => 'Memerlukan revisi',
            self::COMPLETE => 'Selesai',
            default => 'Unknown'
        };
    }
}
