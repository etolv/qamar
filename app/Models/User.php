<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;
    use InteractsWithMedia;
    // use Prunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'dial_code',
        'email_verified_at',
        'code',
        'notification_token',
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

    protected $appends = ['user_type'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // public function prunable()
    // {
    //     // Files matching this query will be pruned
    //     return static::query()->where('deleted_at', '<=', now()->subDays(14));
    // }

    public function account(): MorphTo
    {
        return $this->morphTo('type');
    }

    public function type(): MorphTo
    {
        return $this->morphTo('type');
    }

    public function getUserTypeAttribute()
    {
        return strtolower(class_basename($this->type_type));
    }

    public function scopeOnlyEmployees(Builder $query)
    {
        $query->hasMorph('account', [Employee::class]);
    }

    public function scopeOnlyAdmins(Builder $query)
    {
        $query->hasMorph('account', [Admin::class]);
    }

    public function scopeOnlyCustomers(Builder $query)
    {
        $query->hasMorph('account', [Customer::class]);
    }

    public function scopeOnlyDrivers(Builder $query)
    {
        $query->hasMorph('account', [Driver::class]);
    }

    public function records()
    {
        return $this->hasMany(ModelRecord::class, 'user_id');
    }
}
