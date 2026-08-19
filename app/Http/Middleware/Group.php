<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;

class Group extends Model
{
    protected $table = 'groups';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'activ_user'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id')
                    ->withPivot('nama_group');
    }
}
