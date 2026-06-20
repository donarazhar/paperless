<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'is_sekretariat', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inboundLetters()
    {
        return $this->hasMany(Letter::class, 'to_unit_id');
    }
}
