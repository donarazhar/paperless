<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'unit_id'];
    protected $hidden = ['password', 'remember_token'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sentLetters()
    {
        return $this->hasMany(Letter::class, 'from_user_id');
    }

    public function receivedLetters()
    {
        return $this->hasMany(Letter::class, 'to_user_id');
    }

    public function dispositionsFrom()
    {
        return $this->hasMany(Disposition::class, 'from_user_id');
    }

    public function dispositionsTo()
    {
        return $this->hasMany(Disposition::class, 'to_user_id');
    }
}
