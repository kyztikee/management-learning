<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SearchTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRoleEnum::class
    ];

    protected $appends = [
        'role_name',
    ];

    /**
     * Made for search trait strict filter
     */
    protected $searchable = [
        'id',
        'role',
        'name',
        'email',
        'civilian-rt',
        'civilian-rw',
        'civilian-nik',
        'staff-section_no'
    ];

    protected function roleName(): Attribute
    {
        return new Attribute(
            get: fn () => UserRoleEnum::getString($this->role)
        );
    }

    public function civilian(): HasOne
    {
        return $this->hasOne(Civilian::class);
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }
}
