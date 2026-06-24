@extends('layouts.mailbox')

@section('title', 'Draft Surat')

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
        @if($letters->total() > 0)
        <span>{{ $letters->firstItem() }}-{{ $letters->lastItem() }} dari {{ $letters->total() }}</span>
        @else
        <span>0 dari 0</span>
        @endif
        <div class="d-flex gap-1">
            <a href="{{ $letters->previousPageUrl() }}" class="btn btn-light btn-sm text-muted border-0 bg-transparent {{ $letters->onFirstPage() ? 'disabled' : '' }}"><i class="bi bi-chevron-left"></i></a>
            <a href="{{ $letters->nextPageUrl() }}" class="btn btn-light btn-sm text-muted border-0 bg-transparent {{ !$letters->hasMorePages() ? 'disabled' : '' }}"><i class="bi bi-chevron-right"></i></a>
        </div>
    </div>
</div>

<div class="mail-scroll">
    @forelse($letters as $letter)
        @php
            $isUnread = false; // Outbox messages are usually "read" to the sender
            $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
        @endphp
        <a href="{{ $showUrl }}" class="mail-item {{ $isUnread ? 'unread' : 'read' }}">
            <div class="d-flex align-items-center me-3">
                <div class="form-check m-0" onclick="event.stopPropagation()">
                    <input class="form-check-input" type="checkbox">
                </div>
                <i class="bi bi-star ms-3 text-muted" style="font-size: 1.1rem;"></i>
            </div>
            
            <div class="mail-content d-flex align-items-center justify-content-between">
                <div class="mail-sender" style="flex: 0 0 200px;">
                    Ke: {{ $letter->type === 'outbound_external' ? $letter->external_recipient_name : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal')) }}
                </div>
                
                <div class="d-flex align-items-center px-3" style="flex: 1; min-width: 0;">
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
                        {{ $letter->created_at->format('d M') }}
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-send text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Draft kosong</h5>
            <p class="text-muted" style="font-size: 0.9rem;">Belum ada konsep surat untuk saat ini.</p>
        </div>
    @endforelse
</div>
@endsection