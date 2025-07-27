<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'type',
        'letter_number',
        'subject',
        'body',
        'from_user_id',
        'to_user_id',
        'to_unit_id',
        'status'
    ];

    public function histories()
    {
        return $this->hasMany(LetterHistory::class)
            ->orderBy('created_at', 'desc');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function recipientUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function dispositions()
    {
        return $this->hasMany(Disposition::class);
    }
}
