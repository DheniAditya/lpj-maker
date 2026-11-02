<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Permintaan reset password dari pengguna yang lupa password; ditangani admin. */
class PasswordResetRequest extends Model
{
    protected $fillable = ['user_id', 'username', 'contact', 'note'];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('resolved_at');
    }

    public function resolve(User $admin): void
    {
        $this->forceFill(['resolved_at' => now(), 'resolved_by' => $admin->id])->save();
    }
}
