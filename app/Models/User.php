<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Auth\Group;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // sesuaikan kalau perlu
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'NO_KEC',
    ];

    // kolom sensitif/non-pakai
    protected $hidden = [
        'password',
        'remember_code',
        'salt',
        'activation_code',
        'forgotten_password_code',
    ];

    /**
     * Relasi ke groups via pivot users_groups
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'users_groups', 'user_id', 'group_id')
            ->withPivot('nama_group');
    }

    /**
     * Helper: ambil nama role dalam lowercase (['owner','admin',...])
     */
    public function roleNames(): array
    {
        return $this->groups()->pluck('name')->map(fn($n) => strtolower($n))->toArray();
    }
}
