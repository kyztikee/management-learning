<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SubmissionStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DocumentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => SubmissionStatusEnum::class,
    ];

    protected $appends = [
        'status_name',
    ];

    protected function statusName(): Attribute
    {
        return new Attribute(
            get: fn () => SubmissionStatusEnum::getString($this->status)
        );
    }
}
