@extends('layouts.mailbox')
@section('title', 'Detail Surat')

@section('content')
<style>
    /* Page Container */
    .show-container { background: #f8fafc; height: 100%; overflow-y: auto; display: flex; flex-direction: column; }
    
    /* Top Toolbar (Sticky) */
    .show-toolbar { 
        background: #ffffff; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0; 
        display: flex; align-items: center; justify-content: space-between; 
        position: sticky; top: 0; z-index: 20; 
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 0.5rem;
        color: #475569; font-weight: 600; font-size: 0.95rem; text-decoration: none;
        padding: 0.5rem 1rem; border-radius: 100px; transition: all 0.2s;
    }
    .btn-back:hover { background: #f1f5f9; color: #0f172a; }
    
    /* Main Content Wrapper */
    .show-main { max-width: 1000px; margin: 2rem auto; width: 100%; padding: 0 1.5rem; }
    
    /* Card Styles */
    .letter-card {
        background: #ffffff; border-radius: 1.5rem; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); 
        border: 1px solid rgba(0,0,0,0.04); overflow: hidden;
    }

    /* Header Section */
    .lc-header { padding: 2.5rem 2.5rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
    .lc-subject { font-size: 1.75rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 1.5rem; line-height: 1.3; }
    
    /* Badges */
    .status-badge { padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; vertical-align: middle; }
    .sb-status { background: #f1f5f9; color: #475569; }
    .sb-status-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .sb-agenda { background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; margin-left: 0.5rem; }

    /* Sender Info */
    .sender-block { display: flex; align-items: center; gap: 1.25rem; }
    .sender-avatar {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; font-weight: 800; flex-shrink: 0;
    }
    .av-internal { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: #ffffff; box-shadow: 0 4px 10px rgba(79,70,229,0.2); }
    .av-external { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); color: #ffffff; box-shadow: 0 4px 10px rgba(236,72,153,0.2); }
    
    .sender-name { font-weight: 700; color: #0f172a; font-size: 1.05rem; margin-bottom: 0.15rem; }
    .sender-meta { font-size: 0.85rem; color: #64748b; }
    .sender-date { font-size: 0.85rem; color: #64748b; font-weight: 500; }
    
    /* Action Buttons */
    .btn-action {
        background: #ffffff; border: 1.5px solid #e2e8f0; color: #475569;
        border-radius: 100px; padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 600;
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;
    }
    .btn-action:hover { border-color: #cbd5e1; background: #f8fafc; color: #0f172a; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .btn-circle { width: 38px; height: 38px; padding: 0; justify-content: center; border-radius: 50%; }

    /* Letter Body */
    .lc-body { padding: 2.5rem; font-size: 1rem; color: #334155; line-height: 1.8; }
    .ext-notes { background: #fffbeb; border: 1px solid #fef3c7; border-radius: 1rem; padding: 1.5rem; margin-top: 2rem; }
    .ext-notes strong { color: #b45309; display: block; margin-bottom: 0.5rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }

    /* Attachments */
    .lc-attachments { padding: 2rem 2.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; }
    .att-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 1rem; }
    .att-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem 1.25rem;
        display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; transition: all 0.2s;
        cursor: pointer; text-decoration: none; color: inherit; width: 100%; max-width: 400px;
    }
    .att-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transform: translateY(-2px); }
    .att-card.active { border-color: #4f46e5; background: #eef2ff; }
    .att-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .att-pdf { background: #fef2f2; color: #dc2626; }
    .att-doc { background: #eef2ff; color: #4f46e5; }
    .att-img { background: #f0fdf4; color: #16a34a; }
    .att-name { font-weight: 600; font-size: 0.9rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    /* PDF Preview */
    .pdf-preview-box { border-top: 1px solid #f1f5f9; background: #e2e8f0; padding: 1.5rem; }
    .pdf-frame-wrapper { background: #ffffff; border-radius: 1rem; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .pdf-header { background: #ffffff; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }

    /* Timeline / History */
    .history-section { padding: 2rem 2.5rem; background: #ffffff; border-top: 1px solid #f1f5f9; }
    .timeline { position: relative; padding-left: 2rem; margin-top: 1.5rem; }
    .timeline::before { content: ''; position: absolute; left: 6px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; border-radius: 2px; }
    .tl-item { position: relative; margin-bottom: 1.5rem; }
    .tl-item:last-child { margin-bottom: 0; }
    .tl-dot { position: absolute; left: -2rem; top: 0.25rem; width: 14px; height: 14px; border-radius: 50%; background: #ffffff; border: 3px solid #4f46e5; box-shadow: 0 0 0 4px #ffffff; }
    .tl-time { font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.25rem; display: block; }
    .tl-title { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; }
    .tl-note { font-size: 0.85rem; color: #475569; background: #f8fafc; padding: 0.75rem 1rem; border-radius: 0.75rem; display: inline-block; margin-top: 0.5rem; border: 1px solid #f1f5f9; }

    /* Modals Modernization */
    .modal-content { border-radius: 1.5rem; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 1.5rem; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 1.25rem 1.5rem; }
    
    @media (max-width: 768px) {
        .lc-header, .lc-body, .lc-attachments, .history-section { padding: 1.5rem; }
        .sender-block { flex-wrap: wrap; }
        .sender-actions { width: 100%; display: flex; justify-content: flex-end; margin-top: 1rem; }
    }
</style>

@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->sortByDesc('created_at')->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    
    $canDispose = false;
    if ($letter->status !== 'completed') {
        if (in_array($role, ['admin_sekretariat', 'admin_unit'])) {
            $canDispose = true;
        } elseif (in_array($role, ['bagian_tu', 'kepala_sekretariat'])) {
            if (in_array($letter->status, ['in_review_bagian_tu', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } elseif ($role === 'subag_persuratan') {
            if (in_array($letter->status, ['in_review_subag', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } else {
            if ($letter->to_unit_id == $user->unit_id && in_array($role, ['kepala_unit'])) {
                $canDispose = true;
            }
            elseif ($dispRecv && $dispRecv->status === 'pending' && in_array($role, ['kepala_unit', 'sub_unit'])) {
                $canDispose = true;
            }
        }
    }

    $isDispoToMyUnit = $dispRecv && $dispRecv->to_unit_id === $user->unit_id && is_null($dispRecv->to_user_id) && $dispRecv->status === 'pending';

    $hasAction = $letter->status !== 'completed' && (
        (in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status==='pending_approval')
        || ($role==='admin_unit' && $letter->status==='pending_sending')
        || ($role==='admin_unit' && $isDispoToMyUnit)
        || ($role==='admin_sekretariat' && $letter->status==='pending_agenda')
        || ($role==='subag_persuratan' && $letter->status==='in_review_subag')
        || ($role==='bagian_tu' && $letter->status==='in_review_bagian_tu')
        || ($dispRecv && $dispRecv->status==='pending')
        || $canDispose
    );

    $encodedId = \Vinkla\Hashids\Facades\Hashids::encode($letter->id); 
    $isRespondingDispo = $dispRecv && $dispRecv->status === 'pending';
@endphp

<div class="show-container">
    <div class="show-toolbar">
        <a href="{{ url()->previous() }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
        <div class="d-flex gap-2">
            @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']))
                @if($isRespondingDispo)
                    <button class="btn-action d-none d-sm-flex" data-bs-toggle="modal" data-bs-target="#acceptModal" title="Balas / Tanggapi Disposisi"><i class="bi bi-reply-fill"></i> Balas</button>
                @else
                    <button class="btn-action d-none d-sm-flex" data-bs-toggle="modal" data-bs-target="#replyModal" title="Balas / Tambahkan Catatan"><i class="bi bi-reply-fill"></i> Balas</button>
                @endif
                @if($canDispose)
                    <button class="btn-action d-none d-sm-flex" data-bs-toggle="modal" data-bs-target="#dispoModal" title="Teruskan (Disposisi)"><i class="bi bi-forward-fill"></i> Teruskan</button>
                @else
                    <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="btn-action d-none d-sm-flex" title="Teruskan Surat"><i class="bi bi-forward-fill"></i> Teruskan</a>
                @endif
            @endif

            @if($hasAction || in_array($role, ['kepala_unit', 'subag_persuratan', 'admin_unit', 'admin_sekretariat', 'bagian_tu', 'sub_unit']))
            <div class="dropdown">
                <button class="btn-action btn-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi Lainnya">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:1rem; padding:0.5rem; min-width: 200px; border:none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    @if(in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status === 'pending_approval')
                        <li><a class="dropdown-item py-2 fw-bold text-warning" href="#" onclick="event.preventDefault(); document.getElementById('formApprove').submit();"><i class="bi bi-check-circle-fill me-2"></i>ACC Surat</a></li>
                    @endif

                    @if(in_array($role, ['admin_unit', 'admin_sekretariat']) && $letter->status === 'pending_sending')
                        <li><a class="dropdown-item py-2 fw-bold text-info" href="#" onclick="event.preventDefault(); document.getElementById('formSendFinal').submit();"><i class="bi bi-send-fill me-2"></i>Kirim Surat Fisik</a></li>
                    @endif

                    @if($dispRecv && $dispRecv->status==='pending' && $dispRecv->to_user_id === $user->id)
                        <li><a class="dropdown-item py-2 fw-bold text-danger" href="#" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-reply-fill me-2"></i>Tanggapi Disposisi</a></li>
                    @endif

                    {{-- Menu Buat Disposisi Baru dihilangkan dari titik 3 karena sudah pindah ke tombol Teruskan --}}
                    @if(false)
                        <li><a class="dropdown-item py-2 fw-bold" style="color: #4f46e5;" href="#" data-bs-toggle="modal" data-bs-target="#dispoModal"><i class="bi bi-arrow-right-circle-fill me-2"></i>Buat Disposisi Baru</a></li>
                    @endif

                    @if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
                        <li><a class="dropdown-item py-2 fw-bold" style="color: #4f46e5;" href="#" data-bs-toggle="modal" data-bs-target="#agendaModal"><i class="bi bi-journal-plus me-2"></i>Beri Nomor Agenda</a></li>
                    @endif

                    @if($role==='subag_persuratan' && $letter->status==='in_review_subag' && !$isRespondingDispo)
                        <li><a class="dropdown-item py-2 fw-bold" style="color: #4f46e5;" href="#" onclick="event.preventDefault(); document.getElementById('formForwardTU').submit();"><i class="bi bi-arrow-right me-2"></i>Teruskan ke Tata Usaha</a></li>
                    @endif

                    @php
                        $canCompleteSekretariat = in_array($role, ['bagian_tu', 'subag_persuratan']) && $letter->status !== 'completed' && !in_array($letter->status, ['draft', 'pending_approval', 'pending_sending', 'pending_agenda']);
                        $canCompleteUnit = in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit']) && $canDispose && $letter->status !== 'completed';
                    @endphp

                    @if(in_array($role, ['admin_sekretariat', 'admin_unit']))
                        <li><a class="dropdown-item py-2 fw-bold" style="color: #4f46e5;" href="{{ route('letters.printDisposition', ['letter' => $encodedId]) }}" target="_blank"><i class="bi bi-printer-fill me-2"></i>Lihat Disposisi</a></li>
                    @endif

                    @if(($canCompleteSekretariat || $canCompleteUnit) && !$isRespondingDispo)
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 fw-bold text-success" href="#" onclick="event.preventDefault(); if(confirm('Tandai surat ini sebagai selesai dan arsipkan?')) document.getElementById('formComplete').submit();"><i class="bi bi-archive-fill me-2"></i>Arsipkan Selesai</a></li>
                    @endif
                </ul>
            </div>
            @endif
        </div>
    </div>

    <div class="show-main">
        <div class="letter-card">
            
            {{-- HEADER & SENDER INFO --}}
            <div class="lc-header">
                <h1 class="lc-subject">
                    {{ $letter->subject }}
                </h1>
                <div class="mb-4">
                    <span class="status-badge {{ $letter->status === 'pending_approval' ? 'sb-status-red' : 'sb-status' }}">{{ $letter->status_label }}</span>
                    @if($letter->agenda_number)
                        <span class="status-badge sb-agenda"><i class="bi bi-journal-text me-1"></i> Agenda: {{ $letter->agenda_number }}</span>
                    @endif
                </div>

                <div class="sender-block">
                    @php
                        $isExt = in_array($letter->type, ['outbound_external', 'external']);
                        if($letter->type==='outbound_external') {
                            $senderName = $letter->sender->name ?? 'Sistem';
                            $senderMeta = 'Dikirim ke (Eksternal): ' . $letter->external_recipient_name;
                        } else if($letter->type==='internal') {
                            $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                            $senderMeta = 'Dikirim ke (Internal): ' . ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? '—'));
                        } else { 
                            $senderName = $letter->external_sender_name;
                            $senderMeta = 'Diterima dari (Eksternal)';
                        }
                        $initial = mb_substr($senderName, 0, 1);
                    @endphp
                    <div class="sender-avatar {{ $isExt ? 'av-external' : 'av-internal' }}">{{ mb_strtoupper($initial) }}</div>
                    
                    <div style="flex: 1;">
                        <div class="sender-name">{{ $senderName }}</div>
                        <div class="sender-meta">{{ $senderMeta }}</div>
                    </div>
                    
                    <div class="sender-actions text-end">
                        <div class="sender-date">{{ $letter->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                        <div class="sender-meta">{{ $letter->created_at->format('H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- BODY TEXT --}}
            <div class="lc-body">
                @if($letter->body && $letter->body !== '-')
                    {!! nl2br(e($letter->body)) !!}
                @else
                    <em class="text-muted" style="color: #94a3b8;">(Tidak ada teks pengantar dalam surat ini)</em>
                @endif

                @if($letter->type==='outbound_external' && $letter->external_notes)
                    <div class="ext-notes">
                        <strong><i class="bi bi-info-circle-fill me-1"></i> Catatan Eksternal</strong>
                        {!! nl2br(e($letter->external_notes)) !!}
                    </div>
                @endif
            </div>

            {{-- ATTACHMENTS --}}
            @if($letter->attachments->count() > 0)
            <div class="lc-attachments">
                <div class="att-title"><i class="bi bi-paperclip me-2"></i> {{ $letter->attachments->count() }} Lampiran Tersedia</div>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($letter->attachments as $att)
                        @php
                            $url  = Storage::url($att->file_path);
                            $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                            $name = basename($att->file_path);
                            
                            $iconClass = 'att-doc'; $icon = 'bi-file-earmark-word-fill';
                            if($ext==='pdf') { $iconClass = 'att-pdf'; $icon = 'bi-file-earmark-pdf-fill'; }
                            elseif(in_array($ext, ['jpg','jpeg','png'])) { $iconClass = 'att-img'; $icon = 'bi-file-earmark-image-fill'; }
                        @endphp
                        
                        @if($ext === 'pdf')
                            <div class="att-card view-pdf" data-src="{{ $url }}">
                                <div class="att-icon {{ $iconClass }}"><i class="bi {{ $icon }}"></i></div>
                                <div class="att-name" title="{{ $name }}">{{ $name }}</div>
                            </div>
                        @else
                            <a href="{{ $url }}" target="_blank" class="att-card">
                                <div class="att-icon {{ $iconClass }}"><i class="bi {{ $icon }}"></i></div>
                                <div class="att-name" title="{{ $name }}">{{ $name }}</div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- INLINE PDF PREVIEW --}}
            @if($letter->attachments->filter(fn($att) => strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION)) === 'pdf')->count() > 0)
            <div id="pdfInlinePreview" style="display:none;" class="pdf-preview-box">
                <div class="pdf-frame-wrapper">
                    <div class="pdf-header">
                        <div style="font-weight:700; color:#0f172a; font-size:0.9rem;"><i class="bi bi-eye-fill me-2" style="color:#4f46e5;"></i> Pratinjau Dokumen PDF</div>
                        <button type="button" class="btn-action btn-circle" onclick="document.getElementById('pdfInlinePreview').style.display='none'" style="width: 32px; height: 32px;"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <iframe id="pdfInlineFrame" style="width:100%; height:750px; border:none; display:block; background:#fff;"></iframe>
                </div>
            </div>
            @endif

            {{-- TIMELINE HISTORY --}}
            @if($letter->histories->where('action', 'disposed')->isNotEmpty())
            <div class="history-section">
                <div class="att-title mb-0"><i class="bi bi-clock-history me-2"></i> Lacak Disposisi</div>
                <div class="timeline">
                    {{-- Initial Process: Subag Persuratan --}}
                    <div class="tl-item">
                        <div class="tl-dot"></div>
                        <div style="font-size: 0.9rem; color: #334155;">
                            <span class="tl-time" style="display:inline; margin-right: 5px;">{{ $letter->created_at->locale('id')->isoFormat('D MMMM YYYY • HH:mm') }}</span>
                            <span class="fw-bold text-dark">— Dari :</span> <span style="color: #4f46e5; font-weight: 600;">Subag Persuratan</span>
                            <span class="fst-italic ms-1">"-"</span>
                        </div>
                    </div>

                    @foreach($letter->histories->where('action', 'disposed')->sortBy('created_at') as $h)
                        @php
                            $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                        @endphp
                        <div class="tl-item">
                            <div class="tl-dot"></div>
                            <div style="font-size: 0.9rem; color: #334155;">
                                <span class="tl-time" style="display:inline; margin-right: 5px;">{{ $h->created_at->locale('id')->isoFormat('D MMMM YYYY • HH:mm') }}</span>
                                <span class="fw-bold text-dark">— Ke :</span> <span style="color: #4f46e5; font-weight: 600;">{{ $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—') }}</span>
                                <span class="fst-italic ms-1">"{{ preg_replace('/^\[.*?\]\s*/', '', $h->note) }}"</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- HIDDEN FORMS FOR SIMPLE ACTIONS --}}
<form id="formApprove" action="{{ route('letters.approve', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formSendFinal" action="{{ route('letters.sendFinal', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formForwardTU" action="{{ route('letters.forwardToBagianTu', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formComplete" action="{{ route('letters.complete', $letter) }}" method="POST" style="display:none;">@csrf</form>

{{-- MODALS FOR COMPLEX ACTIONS --}}

{{-- Modal Disposisi Baru --}}
@if($canDispose)
<div class="modal fade" id="dispoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Buat Disposisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="d-flex gap-4 mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked style="accent-color: #4f46e5; transform: scale(1.2);">
                            <label class="form-check-label fw-bold ms-2" for="typeUnit" style="color: #334155;">Ke Unit Kerja</label>
                        </div>
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user" style="accent-color: #4f46e5; transform: scale(1.2);">
                            <label class="form-check-label fw-bold ms-2" for="typeUser" style="color: #334155;">Ke Personal (Individu)</label>
                        </div>
                    </div>
                    
                    <div class="mb-4" id="selectUnit">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Pilih Unit Tujuan <span class="text-danger">*</span></label>
                        <select name="to_unit_id" class="form-select" style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                            <option value="">— Daftar Unit —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                @if($unit->id !== $user->unit_id)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->branch->name ?? '' }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4" id="selectUser" style="display:none;">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Pilih Personal Tujuan <span class="text-danger">*</span></label>
                        <select name="to_user_id" class="form-select" style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                            <option value="">— Daftar Pegawai di Unit Anda —</option>
                            @php
                                $myUnit = \App\Models\Unit::with('organs.users')->find($user->unit_id);
                            @endphp
                            @if($myUnit && $myUnit->organs->isNotEmpty())
                                @foreach($myUnit->organs as $organ)
                                    @foreach($organ->users as $u)
                                        @if($u->id !== $user->id)
                                            <option value="{{ $u->id }}">{{ $organ->name }} — {{ $u->name }}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Catatan Instruksi <span class="text-danger">*</span></label>
                    <textarea name="note" class="form-control" rows="4" placeholder="Tuliskan instruksi disposisi secara jelas..." required style="border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc; padding: 1rem;"></textarea>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 100px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn px-4" style="background: #4f46e5; color: #fff; border-radius: 100px; font-weight: 600;"><i class="bi bi-send-fill me-2"></i> Kirim Disposisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Beri Agenda --}}
@if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
<div class="modal fade" id="agendaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Beri Nomor Agenda Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Nomor Agenda Resmi <span class="text-danger">*</span></label>
                        <input type="text" name="agenda_number" class="form-control" placeholder="Contoh: 123/YPIA/2026" required style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                    </div>
                    <div>
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Catatan (Opsional)</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..." style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;"></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 100px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn px-4" style="background: #4f46e5; color: #fff; border-radius: 100px; font-weight: 600;"><i class="bi bi-journal-plus me-2"></i> Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Tanggapi Disposisi --}}
<!-- General Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Tambahkan Catatan / Balasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.reply', $letter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Catatan Balasan <span class="text-danger">*</span></label>
                        <textarea name="response_note" class="form-control" rows="4" placeholder="Tuliskan catatan atau balasan Anda di sini..." required style="padding: 1rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;"></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Lampiran Tambahan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="file" name="attachment" class="form-control" style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                    </div>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 100px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 100px; font-weight: 600;"><i class="bi bi-send-fill me-2"></i> Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($dispRecv)
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Tanggapi Instruksi Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.respond', $dispRecv->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Pilih Status Tindakan <span class="text-danger">*</span></label>
                        <select name="action" class="form-select" required style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                            <option value="">— Tentukan Status —</option>
                            <option value="accepted">Selesai / Dikerjakan</option>
                            <option value="pertimbangan">Butuh Pertimbangan Lebih Lanjut</option>
                            <option value="followup">Akan Segera Ditindaklanjuti</option>
                            <option value="rejected">Tolak / Informasi Tidak Valid</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Catatan Hasil <span class="text-danger">*</span></label>
                        <textarea name="response_note" class="form-control" rows="4" placeholder="Jelaskan detail tindakan yang telah diambil..." required style="padding: 1rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;"></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Lampiran Tambahan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="file" name="attachment" class="form-control" style="padding: 0.75rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background: #f8fafc;">
                    </div>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 100px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn px-4" style="background: #e11d48; color: #fff; border-radius: 100px; font-weight: 600;"><i class="bi bi-save-fill me-2"></i> Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // PDF Viewer Logic
    const prev = document.getElementById('pdfInlinePreview');
    const frame = document.getElementById('pdfInlineFrame');
    const pdfBtns = document.querySelectorAll('.view-pdf');

    pdfBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if(!frame || !prev) return;
            frame.src = btn.dataset.src;
            prev.style.display = 'block';
            
            pdfBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            if(e.isTrusted) setTimeout(() => prev.scrollIntoView({behavior:'smooth', block:'start'}), 100);
        });
    });

    // Auto-open first PDF is disabled by user request

    // Dispo Modal Toggle Logic
    const sU = document.getElementById('selectUnit');
    const sP = document.getElementById('selectUser');
    document.getElementsByName('recipient_type').forEach(r => r.addEventListener('change', () => {
        const u = document.getElementById('typeUser').checked;
        if(sU) sU.style.display = u ? 'none' : 'block';
        if(sP) sP.style.display = u ? 'block' : 'none';
        
        // Handle required attributes
        if(u) {
            sU.querySelector('select').removeAttribute('required');
            sP.querySelector('select').setAttribute('required', 'required');
        } else {
            sP.querySelector('select').removeAttribute('required');
            sU.querySelector('select').setAttribute('required', 'required');
        }
    }));
});
</script>
@endpush
