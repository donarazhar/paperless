<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'type',
        'letter_number',
        'agenda_number',
        'subject',
        'body',
        'from_user_id',
        'to_user_id',
        'to_unit_id',
        'external_sender_name',
        'external_recipient_name',
        'external_notes',
        'created_by_user_id',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function dispositions()
    {
        return $this->hasMany(Disposition::class);
    }

    public function reads()
    {
        return $this->hasMany(LetterRead::class);
    }

    public function getIsUnreadAttribute()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) return false;

        if ($this->from_user_id === $user->id) return false;

        if ($this->relationLoaded('reads')) {
            return !$this->reads->where('user_id', $user->id)->isNotEmpty();
        }
        
        return !$this->reads()->where('user_id', $user->id)->exists();
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'pending_approval' => 'Menunggu Persetujuan',
            'pending_sending' => 'Menunggu Pengiriman',
            'pending_agenda' => 'Menunggu No. Agenda',
            'in_review_subag' => 'Review Subag',
            'in_review_bagian_tu' => 'Review Bagian TU',
            'in_review_kasubag' => 'Review Kasubag',
            'in_consideration' => 'Dalam Pertimbangan',
            'completed' => 'Selesai / Arsip',
        ];

        return $statuses[$this->status] ?? str_replace('_', ' ', $this->status);
    }
}
