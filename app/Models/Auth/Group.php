<?php
namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Group extends Model
{
    protected $table = 'groups';
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id')
                    ->withPivot('nama_group');
    }
}
