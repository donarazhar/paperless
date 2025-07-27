<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterHistory extends Model
{
    protected $fillable = [
        'letter_id',
        'user_id',
        'action',
        'note',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
