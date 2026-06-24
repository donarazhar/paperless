@extends('layouts.mailbox')
@section('title', 'Arsip Surat')

@section('content')
<div class="list-header">
    <div class="d-flex align-items-center gap-2">
        <div class="form-check m-0">
            <input class="form-check-input" type="checkbox" id="selectAll">
        </div>
        <button class="btn btn-light btn-sm text-muted border-0 bg-transparent"><i class="bi bi-arrow-clockwise"></i></button>
        <button class="btn btn-light btn-sm text-muted border-0 bg-transparent"><i class="bi bi-three-dots-vertical"></i></button>
    </div>
    
    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.85rem;">
        @if(isset($letters) && $letters->total() > 0)
        <span>{{ $letters->firstItem() }}-{{ $letters->lastItem() }} dari {{ $letters->total() }}</span>
        @else
        <span>0 dari 0</span>
        @endif
        <div class="d-flex gap-1">
            @if(isset($letters))
            <a href="{{ $letters->previousPageUrl() }}" class="btn btn-light btn-sm text-muted border-0 bg-transparent {{ $letters->onFirstPage() ? 'disabled' : '' }}"><i class="bi bi-chevron-left"></i></a>
            <a href="{{ $letters->nextPageUrl() }}" class="btn btn-light btn-sm text-muted border-0 bg-transparent {{ !$letters->hasMorePages() ? 'disabled' : '' }}"><i class="bi bi-chevron-right"></i></a>
            @endif
        </div>
    </div>
</div>

<div class="mail-scroll">
    @forelse($letters as $letter)
        @php
            $isUnread = $letter->is_unread;
            $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
            
            // Determine type of archive
            if ($letter->type === 'outbound_external') {
                $typeBadge = '<span class="badge bg-secondary text-white me-2">Eksternal</span>';
                $senderName = $letter->external_recipient_name;
            } else {
                $typeBadge = '<span class="badge bg-secondary text-white me-2">Internal</span>';
                $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Unknown');
            }
            
            if ($letter->status === 'completed') {
                $statusBadge = '<span class="badge bg-success text-white me-2">Selesai</span>';
            } else {
                $statusBadge = '<span class="badge bg-light text-dark me-2 border">'.$letter->status.'</span>';
            }
        @endphp
        <a href="{{ $showUrl }}" class="mail-item {{ $isUnread ? 'unread' : 'read' }}">
            <div class="d-flex align-items-center me-3">
                <div class="form-check m-0" onclick="event.stopPropagation()">
                    <input class="form-check-input" type="checkbox">
                </div>
                <i class="bi bi-archive-fill ms-3 text-muted" style="font-size: 1.1rem;"></i>
            </div>
            
            <div class="mail-content d-flex align-items-center justify-content-between">
                <div class="mail-sender" style="flex: 0 0 200px;">
                    {!! $typeBadge !!}
                    {{ $senderName }}
                </div>
                
                <div class="d-flex align-items-center px-3" style="flex: 1; min-width: 0;">
                    {!! $statusBadge !!}
                    <div class="mail-subject me-2">{{ $letter->subject }}</div>
                    <span class="text-muted mx-1">-</span>
                    <div class="mail-snippet">
                        {!! Str::limit(strip_tags($letter->body), 80) !!}
                    </div>
                </div>

                <div class="mail-date" style="flex: 0 0 100px; text-align: right;">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @else
                        {{ $letter->created_at->format('d M y') }}
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-archive text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Tidak ada arsip</h5>
            <p class="text-muted" style="font-size: 0.9rem;">Anda belum memiliki surat yang diarsipkan.</p>
        </div>
    @endforelse
</div>
@endsection