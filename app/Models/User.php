<?php

namespace App\Models;

use App\Casts\AvatarCast;
use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasAvatar
{
    use HasFactory, HasRoles, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
        'avatar',
        'status',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'avatar' => AvatarCast::class,
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('panel-admin');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }

    public function userDetails(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    public function companyActivityTypes(): BelongsToMany
    {
        return $this->belongsToMany(CompanyActivityType::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(UserLocation::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(UserChat::class, 'user_id');
    }
}
