<?php

namespace App\Enums;

enum SubmissionTypeEnum: int {
    case SKU = 0;
    case SPN = 1;
    case SKK = 2;
    case SW = 3;

    public static function getString($val): string
    {
        return match ($val) {
            self::SKU => 'Surat Keterangan Usaha',
            self::SPN => 'Surat Pengantar Nikah',
            self::SKK => 'Surat Keterangan Kematian',
            self::SW => 'Surat Ahli Waris',
            default => 'Unknown'
        };
    }
}
