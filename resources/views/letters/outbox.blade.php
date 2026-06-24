@extends('layouts.mailbox')

@section('title', 'Kotak Keluar')

@section('content')
<style>
    .page-container { padding: 0; display: flex; flex-direction: column; height: 100%; background: #f8fafc; }
    
    /* Modern Header */
    .inbox-header {
        background: #ffffff;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .ih-title { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em; }
    .ih-sub { font-size: 0.85rem; color: #64748b; margin-top: 0.2rem; }
    
    /* Toolbar */
    .mail-toolbar {
        background: #ffffff;
        padding: 0.75rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .tb-btn {
        background: transparent; border: none; color: #64748b;
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer;
    }
    .tb-btn:hover { background: #f1f5f9; color: #0f172a; }
    .page-info { font-size: 0.85rem; font-weight: 600; color: #64748b; }
    
    /* Mail List */
    .mail-list { flex: 1; overflow-y: auto; background: #ffffff; }
    .m-item {
        display: flex; align-items: center; padding: 0.85rem 2rem;
        border-bottom: 1px solid #f1f5f9; transition: all 0.2s;
        text-decoration: none; color: inherit; position: relative;
    }
    .m-item::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        background: transparent; transition: all 0.2s;
    }
    .m-item:hover { background: #f8fafc; box-shadow: 0 2px 5px rgba(0,0,0,0.02); z-index: 2; transform: translateY(-1px); }
    .m-item:hover::before { background: #cbd5e1; }
    
    .m-item.unread { background: #ffffff; font-weight: 700; }
    .m-item.unread::before { background: #4f46e5; }
    .m-item.unread .m-subject { color: #0f172a; font-weight: 700; }
    .m-item.unread .m-sender { color: #0f172a; font-weight: 700; }
    
    .m-item.read { background: #f8fafc; color: #475569; }
    .m-item.read .m-subject { color: #334155; font-weight: 500; }
    .m-item.read .m-sender { color: #475569; font-weight: 500; }
    
    /* Avatars & Elements */
    .m-avatar {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.9rem; flex-shrink: 0; margin-right: 1rem;
    }
    .av-int { background: #e0e7ff; color: #4338ca; }
    .av-ext { background: #fce7f3; color: #be185d; }
    
    .m-sender { font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 220px; flex-shrink: 0; }
    .m-content { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; gap: 0.2rem; padding-right: 1rem; }
    .m-subject { font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 40%; }
    .m-snippet { font-size: 0.85rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 400; flex: 1; }
    
    .m-date { font-size: 0.8rem; color: #64748b; font-weight: 500; white-space: nowrap; width: 80px; text-align: right; flex-shrink: 0; }
    
    .badge-type {
        font-size: 0.65rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; letter-spacing: 0.05em; margin-left: 0.5rem;
    }
    .bt-ext { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }

    /* Custom Checkbox */
    .m-check { width: 18px; height: 18px; accent-color: #4f46e5; cursor: pointer; margin-right: 1rem; }
    
    /* Empty State */
    .empty-state { text-align: center; padding: 5rem 2rem; background: #fff; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .empty-state i { font-size: 4rem; color: #e2e8f0; margin-bottom: 1rem; }
    .empty-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
    .empty-desc { color: #64748b; font-size: 0.95rem; max-width: 400px; margin: 0 auto; }

    @media (max-width: 768px) {
        .m-item { padding: 1rem; flex-wrap: wrap; }
        .m-sender { width: 100%; margin-bottom: 0.25rem; }
        .m-content { width: 100%; padding-right: 0; display: block; }
        .m-subject { max-width: 100%; margin-bottom: 0.2rem; }
        .m-snippet { display: none; }
        .m-date { position: absolute; right: 1rem; top: 1rem; }
        .m-check, .tb-btn { display: none; }
        .m-avatar { width: 32px; height: 32px; font-size: 0.8rem; }
    }
</style>

<div class="page-container">
    <div class="inbox-header">
        <div>
            <h1 class="ih-title">Kotak Keluar</h1>
            <div class="ih-sub">Pusat pemantauan surat yang telah Anda kirimkan</div>
        </div>
        <div style="background: #eef2ff; color: #4f46e5; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem;">
            <i class="bi bi-send-fill me-2"></i>{{ $letters->total() }} Pesan
        </div>
    </div>

    <div class="mail-toolbar">
        <div class="d-flex align-items-center gap-1">
            <input type="checkbox" class="m-check" style="margin-right: 1.5rem;" title="Pilih Semua">
            <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i></button>
            <button class="tb-btn" title="Opsi Lainnya"><i class="bi bi-three-dots-vertical"></i></button>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="page-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}-{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    0 dari 0
                @endif
            </span>
            <div class="d-flex gap-1">
                <a href="{{ $letters->previousPageUrl() }}" class="tb-btn {{ $letters->onFirstPage() ? 'disabled opacity-50' : '' }}" style="text-decoration:none;"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ $letters->nextPageUrl() }}" class="tb-btn {{ !$letters->hasMorePages() ? 'disabled opacity-50' : '' }}" style="text-decoration:none;"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isUnread = $letter->is_unread;
                $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                
                $isExternal = $letter->type === 'outbound_external';
                $senderName = $isExternal ? $letter->external_recipient_name : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $avatarInitials = mb_strtoupper(mb_substr($senderName, 0, 1));
            @endphp
            <a href="{{ $showUrl }}" class="m-item {{ $isUnread ? 'unread' : 'read' }}">
                
                <input type="checkbox" class="m-check" onclick="event.stopPropagation()">
                
                <div class="m-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">
                    {{ $avatarInitials }}
                </div>
                
                <div class="m-sender">
                    Ke: {{ $senderName }}
                </div>
                
                <div class="m-content">
                    <div class="d-flex align-items-center gap-1">
                        @if($isExternal)
                            <span class="badge" style="background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; font-size: 0.6rem; padding: 0.15rem 0.4rem; border-radius: 4px; letter-spacing: 0.05em; flex-shrink: 0;"><i class="bi bi-globe me-1"></i>EKSTERNAL</span>
                        @endif
                        @if($letter->agenda_number)
                            <span class="badge" style="background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; font-size: 0.6rem; padding: 0.15rem 0.4rem; border-radius: 4px; flex-shrink: 0;"><i class="bi bi-journal-text me-1"></i>{{ $letter->agenda_number }}</span>
                        @endif
                        @php
                            $sBg = $letter->status === 'pending_approval' ? '#fef2f2' : '#f1f5f9';
                            $sCol = $letter->status === 'pending_approval' ? '#dc2626' : '#475569';
                            $sBor = $letter->status === 'pending_approval' ? '1px solid #fecaca' : '1px solid transparent';
                        @endphp
                        <span class="badge" style="background: {{ $sBg }}; color: {{ $sCol }}; border: {{ $sBor }}; font-size: 0.6rem; padding: 0.15rem 0.4rem; border-radius: 4px; text-transform: uppercase; flex-shrink: 0;">{{ $letter->status_label }}</span>
                    </div>
                    <div class="d-flex align-items-center" style="width: 100%;">
                        <span class="m-subject">{{ $letter->subject }}</span>
                        <span class="text-muted mx-2" style="font-size: 0.85rem; font-weight: 400;">—</span>
                        <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                    </div>
                </div>
                
                <div class="m-date">
                    @if($letter->created_at->isToday())
                        <strong style="color: #0f172a;">{{ $letter->created_at->format('H:i') }}</strong>
                    @else
                        {{ $letter->created_at->format('d M') }}
                    @endif
                </div>
            </a>
        @empty
            <div class="empty-state">
                <i class="bi bi-send-fill" style="background: linear-gradient(135deg, #e2e8f0, #cbd5e1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <div class="empty-title">Kotak Keluar Kosong</div>
                <div class="empty-desc">Belum ada surat yang Anda kirimkan untuk saat ini.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection