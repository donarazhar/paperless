<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['letter_id', 'file_path'];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
