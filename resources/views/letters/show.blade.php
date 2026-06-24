@extends('layouts.mailbox')
@section('title', 'Detail Surat')

@section('content')
<style>
    /* ══ GMAIL-STYLE LETTER VIEW ══ */
    .show-wrap { display: flex; flex-direction: column; height: 100%; background: #fff; overflow: hidden; }

    /* ── Toolbar ── */
    .show-toolbar {
        display: flex; align-items: center; gap: .25rem;
        padding: .5rem 1rem; border-bottom: 1px solid #f1f5f9; background: #fff;
        position: sticky; top: 0; z-index: 20; flex-shrink: 0;
    }
    .tb-btn {
        width: 36px; height: 36px; border: none; background: none;
        color: #444746; border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; transition: background .15s; text-decoration: none;
    }
    .tb-btn:hover { background: #f1f5f9; color: #1f1f1f; }
    .tb-btn-text {
        padding: 0 .75rem; width: auto; border-radius: 4px; font-size: .875rem; font-weight: 500;
    }
    .tb-divider { width: 1px; height: 20px; background: #e2e8f0; margin: 0 .5rem; }

    /* ── Scrollable Area ── */
    .show-scroll { flex: 1; overflow-y: auto; padding: 1.5rem 2rem; }

    /* ── Subject & Badges ── */
    .show-subject-row {
        display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;
        padding-left: 3.5rem; /* Sejajar dengan teks, offset avatar */
    }
    .show-subject {
        font-size: 1.35rem; font-weight: 400; color: #1f1f1f; margin: 0; line-height: 1.3;
    }
    .show-badges { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .4rem; }
    .s-badge {
        font-size: .65rem; font-weight: 600; padding: .1rem .4rem; border-radius: 4px;
        letter-spacing: .02em; border: 1px solid transparent; text-transform: uppercase;
    }
    .bdg-red  { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .bdg-gray { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
    .bdg-blue { background: #eef2ff; color: #4f46e5; border-color: #e0e7ff; }

    /* ── Sender Header ── */
    .sender-row {
        display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;
    }
    .sender-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 600; color: #fff; flex-shrink: 0;
    }
    .av-int { background: #0b57d0; } /* Biru Gmail */
    .av-ext { background: #b3261e; } /* Merah */

    .sender-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .sender-name-wrap { display: flex; align-items: baseline; gap: .5rem; flex-wrap: wrap; }
    .sender-name { font-weight: 700; font-size: .95rem; color: #1f1f1f; }
    .sender-email { font-size: .75rem; color: #444746; }
    .sender-to { font-size: .75rem; color: #444746; margin-top: .1rem; }

    .sender-date {
        font-size: .75rem; color: #444746; white-space: nowrap; margin-left: 1rem;
        display: flex; align-items: center; gap: .5rem;
    }

    /* ── Body ── */
    .show-body {
        padding-left: 3.5rem; font-size: .9rem; color: #1f1f1f; line-height: 1.6;
        white-space: pre-wrap; margin-bottom: 2rem;
    }
    .ext-notes {
        margin-top: 1.5rem; padding: 1rem; background: #fff8e1; border: 1px solid #ffecb3;
        border-radius: 8px; font-size: .85rem; color: #5d4037;
    }
    .ext-notes strong { display: block; margin-bottom: .3rem; color: #e65100; }

    /* ── Attachments ── */
    .att-area {
        padding-left: 3.5rem; margin-bottom: 2rem;
    }
    .att-count { font-size: .8rem; font-weight: 600; color: #444746; margin-bottom: .75rem; }
    .att-chips { display: flex; flex-wrap: wrap; gap: .75rem; }
    .att-chip {
        display: flex; align-items: center; gap: .5rem;
        padding: .4rem .75rem; border: 1px solid #c7c7c7; border-radius: 100px;
        background: #fff; text-decoration: none; color: #1f1f1f;
        font-size: .8rem; font-weight: 500; transition: background .15s;
        max-width: 250px; cursor: pointer;
    }
    .att-chip:hover { background: #f1f5f9; }
    .att-chip i { font-size: 1.1rem; }
    .att-chip.pdf i { color: #ea4335; }
    .att-chip.doc i { color: #4285f4; }
    .att-chip.img i { color: #34a853; }
    .att-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* PDF Inline */
    .pdf-preview {
        margin-top: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;
    }
    .pdf-header {
        background: #f8fafc; padding: .5rem 1rem; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
        font-size: .8rem; font-weight: 600; color: #1f1f1f;
    }
    .pdf-frame { width: 100%; height: 600px; border: none; display: block; background: #fff; }

    /* ── Timeline ── */
    .tl-area {
        padding-left: 3.5rem; margin-top: 2rem; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;
    }
    .tl-title { font-size: .85rem; font-weight: 600; color: #444746; margin-bottom: 1rem; }
    .tl-list {
        position: relative; padding-left: 1.5rem;
    }
    .tl-list::before {
        content: ''; position: absolute; left: 5px; top: 5px; bottom: 5px; width: 2px; background: #e2e8f0;
    }
    .tl-item { position: relative; margin-bottom: 1.25rem; }
    .tl-dot {
        position: absolute; left: -1.5rem; top: .3rem; width: 12px; height: 12px;
        border-radius: 50%; background: #0b57d0; border: 2px solid #fff;
    }
    .tl-item-text { font-size: .85rem; color: #1f1f1f; }
    .tl-time { font-size: .75rem; color: #444746; }

    /* ── Dropdown / Action btn ── */
    .btn-action-reply {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .5rem 1rem; border: 1px solid #747775; border-radius: 100px;
        background: #fff; color: #1f1f1f; font-size: .875rem; font-weight: 500;
        text-decoration: none; cursor: pointer; transition: background .15s;
    }
    .btn-action-reply:hover { background: #f1f5f9; }

    /* Responsive */
    @media(max-width: 768px) {
        .show-scroll { padding: 1rem; }
        .show-subject-row { padding-left: 0; }
        .show-body, .att-area, .tl-area { padding-left: 0; }
        .sender-date { margin-left: auto; }
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
            } elseif ($dispRecv && $dispRecv->status === 'pending' && in_array($role, ['kepala_unit', 'sub_unit'])) {
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

<div class="show-wrap">
    {{-- ── Toolbar ── --}}
    <div class="show-toolbar">
        <a href="{{ url()->previous() }}" class="tb-btn" title="Kembali"><i class="bi bi-arrow-left"></i></a>
        <div class="tb-divider"></div>

        @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']))
            @if($isRespondingDispo)
                <button class="tb-btn" data-bs-toggle="modal" data-bs-target="#acceptModal" title="Tanggapi Disposisi"><i class="bi bi-reply-fill"></i></button>
            @else
                <button class="tb-btn" data-bs-toggle="modal" data-bs-target="#replyModal" title="Balas"><i class="bi bi-reply-fill"></i></button>
            @endif
            @if($canDispose)
                <button class="tb-btn" data-bs-toggle="modal" data-bs-target="#dispoModal" title="Teruskan"><i class="bi bi-forward-fill"></i></button>
            @else
                <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="tb-btn" title="Teruskan"><i class="bi bi-forward-fill"></i></a>
            @endif
        @endif

        @if(in_array($role, ['admin_sekretariat', 'admin_unit']))
            <a href="{{ route('letters.printDisposition', ['letter' => $encodedId]) }}" target="_blank" class="tb-btn" title="Cetak Disposisi"><i class="bi bi-printer"></i></a>
        @endif

        <div class="ms-auto d-flex align-items-center">
            @if($hasAction || in_array($role, ['kepala_unit', 'subag_persuratan', 'admin_unit', 'admin_sekretariat', 'bagian_tu', 'sub_unit']))
            <div class="dropdown">
                <button class="tb-btn" type="button" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:8px; border:1px solid #e2e8f0;">
                    @if(in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status === 'pending_approval')
                        <li><a class="dropdown-item py-2 fw-bold text-danger" href="#" onclick="event.preventDefault(); document.getElementById('formApprove').submit();"><i class="bi bi-check-lg me-2"></i>ACC Surat</a></li>
                    @endif

                    @if(in_array($role, ['admin_unit', 'admin_sekretariat']) && $letter->status === 'pending_sending')
                        <li><a class="dropdown-item py-2 fw-bold text-primary" href="#" onclick="event.preventDefault(); document.getElementById('formSendFinal').submit();"><i class="bi bi-send me-2"></i>Kirim Surat Fisik</a></li>
                    @endif

                    @if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#agendaModal"><i class="bi bi-journal-plus me-2"></i>Beri Nomor Agenda</a></li>
                    @endif

                    @if($role==='subag_persuratan' && $letter->status==='in_review_subag' && !$isRespondingDispo)
                        <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('formForwardTU').submit();"><i class="bi bi-arrow-right me-2"></i>Teruskan ke Tata Usaha</a></li>
                    @endif

                    @php
                        $canCompleteSekretariat = in_array($role, ['bagian_tu', 'subag_persuratan']) && $letter->status !== 'completed' && !in_array($letter->status, ['draft', 'pending_approval', 'pending_sending', 'pending_agenda']);
                        $canCompleteUnit = in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit']) && $canDispose && $letter->status !== 'completed';
                    @endphp

                    @if(($canCompleteSekretariat || $canCompleteUnit) && !$isRespondingDispo)
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 fw-bold text-success" href="#" onclick="event.preventDefault(); if(confirm('Tandai selesai?')) document.getElementById('formComplete').submit();"><i class="bi bi-archive me-2"></i>Arsipkan Selesai</a></li>
                    @endif
                </ul>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Scrollable Area ── --}}
    <div class="show-scroll">
        {{-- Subject --}}
        <div class="show-subject-row">
            <div>
                <h1 class="show-subject">{{ $letter->subject }}</h1>
                <div class="show-badges">
                    <span class="s-badge {{ $letter->status === 'pending_approval' ? 'bdg-red' : 'bdg-gray' }}">{{ $letter->status_label }}</span>
                    @if($letter->agenda_number)
                        <span class="s-badge bdg-blue"><i class="bi bi-journal-text me-1"></i> Agenda: {{ $letter->agenda_number }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sender Info --}}
        @php
            $isExt = in_array($letter->type, ['outbound_external', 'external']);
            if($letter->type==='outbound_external') {
                $senderName = $letter->sender->name ?? 'Sistem';
                $senderMeta = $letter->external_recipient_name;
            } else if($letter->type==='internal') {
                $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                $senderMeta = $letter->recipientUnit->name ?? ($letter->recipientUser->name ?? '—');
            } else { 
                $senderName = $letter->external_sender_name;
                $senderMeta = 'Unit Tujuan (Sistem)';
            }
            $initial = mb_substr($senderName, 0, 1);
        @endphp
        <div class="sender-row">
            <div class="sender-avatar {{ $isExt ? 'av-ext' : 'av-int' }}">{{ mb_strtoupper($initial) }}</div>
            <div class="sender-info">
                <div class="sender-name-wrap">
                    <span class="sender-name">{{ $senderName }}</span>
                    <span class="sender-email">&lt;{{ $isExt ? 'external' : 'internal' }}&gt;</span>
                </div>
                <div class="sender-to">kepada {{ $senderMeta }}</div>
            </div>
            <div class="sender-date">
                {{ $letter->created_at->format('d M Y, H:i') }}
            </div>
        </div>

        {{-- Body --}}
        <div class="show-body">
            @if($letter->body && $letter->body !== '-')
                {!! nl2br(e($letter->body)) !!}
            @else
                <em style="color:#94a3b8;">(Tidak ada teks pengantar dalam surat ini)</em>
            @endif

            @if($letter->type==='outbound_external' && $letter->external_notes)
                <div class="ext-notes">
                    <strong>Catatan Eksternal:</strong>
                    {!! nl2br(e($letter->external_notes)) !!}
                </div>
            @endif
        </div>

        {{-- Attachments --}}
        @if($letter->attachments->count() > 0)
        <div class="att-area">
            <div class="att-count">{{ $letter->attachments->count() }} Lampiran</div>
            <div class="att-chips">
                @foreach($letter->attachments as $att)
                    @php
                        $url  = Storage::url($att->file_path);
                        $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                        $name = basename($att->file_path);
                        $type = 'doc';
                        if($ext==='pdf') $type = 'pdf';
                        elseif(in_array($ext, ['jpg','jpeg','png'])) $type = 'img';
                    @endphp
                    @if($ext === 'pdf')
                        <div class="att-chip {{ $type }} view-pdf" data-src="{{ $url }}">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            <span class="att-name" title="{{ $name }}">{{ $name }}</span>
                        </div>
                    @else
                        <a href="{{ $url }}" target="_blank" class="att-chip {{ $type }}">
                            <i class="bi {{ $type==='img' ? 'bi-file-earmark-image-fill' : 'bi-file-earmark-word-fill' }}"></i>
                            <span class="att-name" title="{{ $name }}">{{ $name }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Inline PDF --}}
            <div id="pdfInlinePreview" style="display:none;" class="pdf-preview">
                <div class="pdf-header">
                    <span><i class="bi bi-file-pdf"></i> Pratinjau Dokumen</span>
                    <button class="tb-btn" onclick="document.getElementById('pdfInlinePreview').style.display='none'" style="width:28px;height:28px;"><i class="bi bi-x"></i></button>
                </div>
                <iframe id="pdfInlineFrame" class="pdf-frame"></iframe>
            </div>
        </div>
        @endif

        {{-- Timeline / History --}}
        @if($letter->histories->where('action', 'disposed')->isNotEmpty())
        <div class="tl-area">
            <div class="tl-title">Alur Disposisi</div>
            <div class="tl-list">
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-item-text">
                        <strong>Subag Persuratan</strong>
                        <span class="text-muted ms-1">— Mengagendakan surat</span>
                    </div>
                    <div class="tl-time">{{ $letter->created_at->format('d M, H:i') }}</div>
                </div>
                @foreach($letter->histories->where('action', 'disposed')->sortBy('created_at') as $h)
                    @php
                        $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot" style="background:#eef2ff; border-color:#4f46e5;"></div>
                        <div class="tl-item-text">
                            <strong>Ke: {{ $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—') }}</strong>
                            <span class="text-muted ms-1">— "{{ preg_replace('/^\[.*?\]\s*/', '', $h->note) }}"</span>
                        </div>
                        <div class="tl-time">{{ $h->created_at->format('d M, H:i') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick action di bawah --}}
        <div style="padding-left: 3.5rem; margin-top: 1.5rem;">
            @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']))
                @if($isRespondingDispo)
                    <button class="btn-action-reply" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-reply"></i> Balas</button>
                @else
                    <button class="btn-action-reply" data-bs-toggle="modal" data-bs-target="#replyModal"><i class="bi bi-reply"></i> Balas</button>
                @endif
                @if($canDispose)
                    <button class="btn-action-reply" data-bs-toggle="modal" data-bs-target="#dispoModal"><i class="bi bi-forward"></i> Teruskan</button>
                @else
                    <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="btn-action-reply"><i class="bi bi-forward"></i> Teruskan</a>
                @endif
            @endif
        </div>

    </div>
</div>

{{-- ── HIDDEN FORMS ── --}}
<form id="formApprove" action="{{ route('letters.approve', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formSendFinal" action="{{ route('letters.sendFinal', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formForwardTU" action="{{ route('letters.forwardToBagianTu', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formComplete" action="{{ route('letters.complete', $letter) }}" method="POST" style="display:none;">@csrf</form>

{{-- ── MODALS (Styling Bootstrap Standard agar ringkas) ── --}}
<style>
    .modal-content { border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,.1); }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem; }
</style>

{{-- Modal Disposisi Baru --}}
@if($canDispose)
<div class="modal fade" id="dispoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buat Disposisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="d-flex gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                            <label class="form-check-label fw-bold" for="typeUnit">Ke Unit Kerja</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                            <label class="form-check-label fw-bold" for="typeUser">Ke Personal</label>
                        </div>
                    </div>
                    <div class="mb-3" id="selectUnit">
                        <select name="to_unit_id" class="form-select">
                            <option value="">— Daftar Unit —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                @if($unit->id !== $user->unit_id) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="selectUser" style="display:none;">
                        <select name="to_user_id" class="form-select">
                            <option value="">— Daftar Pegawai di Unit Anda —</option>
                            @php $myUnit = \App\Models\Unit::with('organs.users')->find($user->unit_id); @endphp
                            @if($myUnit && $myUnit->organs->isNotEmpty())
                                @foreach($myUnit->organs as $organ)
                                    @foreach($organ->users as $u)
                                        @if($u->id !== $user->id) <option value="{{ $u->id }}">{{ $u->name }}</option> @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <textarea name="note" class="form-control" rows="3" placeholder="Tulis instruksi disposisi..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Kirim</button>
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
                <h5 class="modal-title fw-bold">Nomor Agenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="text" name="agenda_number" class="form-control mb-3" placeholder="Contoh: 123/YPIA/2026" required>
                    <textarea name="note" class="form-control" rows="2" placeholder="Catatan (Opsional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Tanggapan / Balas --}}
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catatan / Balasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.reply', $letter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <textarea name="response_note" class="form-control mb-3" rows="3" placeholder="Balasan..." required></textarea>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Kirim</button>
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
                <h5 class="modal-title fw-bold">Tanggapi Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.respond', $dispRecv->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <select name="action" class="form-select mb-3" required>
                        <option value="">— Status —</option>
                        <option value="accepted">Selesai / Dikerjakan</option>
                        <option value="pertimbangan">Butuh Pertimbangan</option>
                        <option value="followup">Akan Ditindaklanjuti</option>
                        <option value="rejected">Tolak</option>
                    </select>
                    <textarea name="response_note" class="form-control mb-3" rows="3" placeholder="Catatan hasil..." required></textarea>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
            if(e.isTrusted) setTimeout(() => prev.scrollIntoView({behavior:'smooth', block:'start'}), 100);
        });
    });

    // Dispo Modal Toggle Logic
    const sU = document.getElementById('selectUnit');
    const sP = document.getElementById('selectUser');
    document.getElementsByName('recipient_type').forEach(r => r.addEventListener('change', () => {
        const u = document.getElementById('typeUser').checked;
        if(sU) sU.style.display = u ? 'none' : 'block';
        if(sP) sP.style.display = u ? 'block' : 'none';
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
@endsection
