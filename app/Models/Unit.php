<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inboundLetters()
    {
        return $this->hasMany(Letter::class, 'to_unit_id');
    }
}
