<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppChat extends Model
{
    protected $table = 'app_chats';

    protected $fillable = [
        'app_user_id',
        'sender_type',
        'admin_id',
        'message',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $appends = [
        'attachment_url',
    ];

    public function getAttachmentUrlAttribute(): ?string
    {
        if (empty($this->attachment)) {
            return null;
        }

        if (str_starts_with($this->attachment, 'http://') || str_starts_with($this->attachment, 'https://')) {
            return $this->attachment;
        }

        return asset('storage/' . ltrim($this->attachment, '/'));
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
