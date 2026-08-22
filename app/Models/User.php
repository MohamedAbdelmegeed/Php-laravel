<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_id',
        'telegram_username',
        'telegram_photo_url',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
        ];
    }

    /**
     * Filament only consults this when APP_ENV=production; locally every user
     * is let in. A panel that works in development therefore 403s in
     * production unless this method exists.
     *
     * Every authenticated user is allowed, which is safe only because this app
     * has no registration route - routes/web.php exposes just '/', so accounts
     * exist only if created deliberately via `php artisan make:filament-user`.
     * Add public sign-up and this becomes "anyone can reach /admin": gate it on
     * a column (`return $this->is_admin;`) before that happens.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
