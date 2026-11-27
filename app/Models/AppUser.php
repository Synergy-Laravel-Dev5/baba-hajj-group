<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AppUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'app_users';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'avatar',
        'fcm_token',
        'device_name',
        'status',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'password'       => 'hashed',
    ];

    protected $appends = [
        'avatar_url',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset('storage/' . ltrim($this->avatar, '/'));
    }

    public function chats()
    {
        return $this->hasMany(AppChat::class, 'app_user_id');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'app_user_id');
    }

    public function inquiries()
    {
        return $this->hasMany(PackageInquiry::class, 'app_user_id');
    }

    public function unreadChats()
    {
        return $this->hasMany(AppChat::class, 'app_user_id')
            ->where('sender_type', 'admin')
            ->where('is_read', false);
    }

    public function unreadAdminChatsCount(): int
    {
        return $this->hasMany(AppChat::class, 'app_user_id')
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
    }
}
