<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'organ_id'];
    protected $hidden = ['password', 'remember_token'];

    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit()
    {
        return $this->hasOneThrough(Unit::class, Organ::class, 'id', 'id', 'organ_id', 'unit_id');
    }

    public function getUnitIdAttribute()
    {
        return $this->organ ? $this->organ->unit_id : null;
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
