<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SubmissionStatusEnum;
use App\Enums\SubmissionTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentSubmission extends Model
{
    use HasFactory, SearchTrait;

    protected $fillable = [
        'type',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => SubmissionStatusEnum::class,
        'type' => SubmissionTypeEnum::class
    ];

    protected $appends = [
        'status_name',
        'type_name'
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

    public function document_attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }
}
