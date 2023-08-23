<?php

namespace App\Enums;

enum SubmissionStatusEnum: int {
    case CREATED = 0;
    case REVISE = 1;
    case COMPLETE = 2;

    public static function getString($val): string
    {
        return match ($val) {
            self::CREATED => 'Sedang diproses',
            self::REVISE => 'Memerlukan revisi',
            self::COMPLETE => 'Selesai',
            default => 'Unknown'
        };
    }
}
