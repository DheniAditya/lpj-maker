<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    /** Batas jumlah arsip LPJ per akun (admin tidak dibatasi). */
    public const MAX_REPORTS = 5;

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
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        // Hapus laporan satu per satu agar file nota & tanda tangan ikut terhapus.
        static::deleting(function (User $user) {
            $user->expenseReports->each->delete();
        });
    }

    public function expenseReports(): HasMany
    {
        return $this->hasMany(ExpenseReport::class);
    }

    public function passwordResetRequests(): HasMany
    {
        return $this->hasMany(PasswordResetRequest::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Aturan username: huruf kecil, angka, titik, garis bawah, strip, dan @
     * (@ dan titik diizinkan agar username lama yang berasal dari email tetap valid).
     */
    public static function usernameRules(?int $ignoreId = null): array
    {
        return [
            'required', 'string', 'min:3', 'max:100', 'regex:/^[a-z0-9._@-]+$/',
            \Illuminate\Validation\Rule::unique('users', 'username')->ignore($ignoreId),
        ];
    }

    public static function normalizeUsername(?string $username): string
    {
        return strtolower(trim((string) $username));
    }

    public function hasReachedReportLimit(): bool
    {
        return ! $this->isAdmin() && $this->expenseReports()->count() >= self::MAX_REPORTS;
    }
}
