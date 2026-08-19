<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\Group;
use App\Models\UnitProfile;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $hidden = ['password', 'remember_code', 'salt', 'activation_code', 'forgotten_password_code'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'users_groups', 'user_id', 'group_id')
            ->withPivot('nama_group');
    }

    public function unitProfile()
    {
        return $this->belongsTo(UnitProfile::class, 'unit', 'unit_id');
    }

    // helper: array role lowercase, ex: ['owner','admin']
    public function roleNames(): array
    {
        return $this->groups()->pluck('name')->map(fn($n) => strtolower($n))->toArray();
    }
}
