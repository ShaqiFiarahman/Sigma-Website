<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['id', 'full_name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{

    use HasFactory, Notifiable;

    protected $table = 'profiles';
    public $incrementing = false;
    protected $keyType = 'string';

    // Dapatkan atribut yang harus di-cast
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Hubungan Database
    public function disasters(): HasMany
    {
        return $this->hasMany(Disaster::class);
    }

    // Aksesor & Mutator
    public function getShortNameAttribute(): string
    {
        // Ambil maksimal dua kata pertama dari nama lengkap user
        $name = trim($this->full_name ?? '');
        $words = explode(' ', $name);
        return implode(' ', array_slice($words, 0, 2));
    }
}
