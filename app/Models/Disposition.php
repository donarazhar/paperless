<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{
    protected $fillable = [
        'letter_id',
        'from_user_id',
        'to_unit_id',
        'to_user_id',
        'note',
        'status',
        'response_note',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
