<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProgressStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentProgress extends Model
{
    use HasFactory;

    protected $table = 'document_progresses';

    protected $fillable = [
        'document_submission_id',
        'status',
        'user_id',
        'file_path',
        'note'
    ];

    protected $casts = [
        'status' => ProgressStatusEnum::class,
    ];

    protected $appends = [
        'status_name',
    ];

    protected function statusName(): Attribute
    {
        return new Attribute(
            get: fn () => ProgressStatusEnum::getString($this->status)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
