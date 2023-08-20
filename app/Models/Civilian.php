<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CivilianStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Civilian extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'nik',
        'rt',
        'rw',
        'phone_no',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'status' => CivilianStatusEnum::class,
    ];

    protected $appends = [
        'status_name',
    ];


    protected $visible = [
        'user_id',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'nik',
        'rt',
        'rw',
        'phone_no',
        'status',
        'approved_by',
        'approved_at',
        'status_name',
        'created_at',
        'updated_at',
        'approved_by_user'
    ];


    protected function statusName(): Attribute
    {
        return new Attribute(
            get: fn () => CivilianStatusEnum::getString($this->status)
        );
    }

    public function approved_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
