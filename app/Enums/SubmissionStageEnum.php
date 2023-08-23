<?php

namespace App\Enums;

enum SubmissionStageEnum: int {
    case RT = 0;
    CASE RW = 1;
    case LURAH = 2;

    public static function getString($val): string
    {
        return match ($val) {
            self::LURAH => 'LURAH',
            self::RT => 'RT',
            self::RW => 'RW',
            default => 'Unknown'
        };
    }
}
