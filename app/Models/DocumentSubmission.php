<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SubmissionStatusEnum;
use App\Enums\SubmissionTypeEnum;
use App\Enums\SubmissionStageEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSubmission extends Model
{
    use HasFactory, SearchTrait;

    protected $fillable = [
        'type',
        'user_id',
        'status',
        'stage',
    ];

    protected $casts = [
        'status' => SubmissionStatusEnum::class,
        'type' => SubmissionTypeEnum::class,
        'stage' => SubmissionStageEnum::class,
    ];

    protected $appends = [
        'status_name',
        'type_name',
        'stage_name'
    ];

    protected $searchable = [
        'id',
        'type',
        'document_attachments-document_type',
    ];

    protected function statusName(): Attribute
    {
        return new Attribute(
            get: fn () => SubmissionStatusEnum::getString($this->status)
        );
    }

    protected function typeName(): Attribute
    {
        return new Attribute(
            get: fn () => SubmissionTypeEnum::getString($this->type)
        );
    }

    protected function stageName(): Attribute
    {
        return new Attribute(
            get: fn () => SubmissionStageEnum::getString($this->stage)
        );
    }


    public function document_attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    public function document_progresses(): HasMany
    {
        return $this->hasMany(DocumentProgress::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
