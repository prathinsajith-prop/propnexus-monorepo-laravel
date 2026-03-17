<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Litepie\Database\Traits\Searchable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'password',
    ];

    /**
     * Additional attributes appended to serialized model output.
     *
     * @var list<string>
     */
    protected $appends = [
        'profile_image_url',
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
     * Resolve a usable avatar URL for the user profile image.
     */
    protected function profileImageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $path = $this->profile_image;

                if (is_string($path) && $path !== '') {
                    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                        return $path;
                    }

                    return '/storage/'.ltrim($path, '/');
                }

                return '/storage/avatars/temp-profile-image.png';
            }
        );
    }
}
