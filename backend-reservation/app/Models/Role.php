<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RoleName;

class Role extends Model
{
    protected $fillable = ['nom'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public static function findByName(RoleName $role): self
    {
    return self::where(
        'nom',
        $role->value
    )->firstOrFail();
    }
}