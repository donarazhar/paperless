@extends('layouts.mailbox')
@section('title', 'Detail Surat')

@section('content')
<style>
    /* ══ MODERN CARD LAYOUT ══ */
    .detail-wrapper { padding: 1.5rem; height: 100%; overflow-y: auto; }
    
    .card-custom {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; overflow: hidden;
    }
    .card-header-custom {
        background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.25rem 1.5rem;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;
    }
    .card-title-custom { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0; }
    .card-body-custom { padding: 1.5rem; }

    /* Meta Grid */
    .meta-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;
    }
    .meta-item { display: flex; flex-direction: column; gap: 0.25rem; }
    .meta-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    .meta-value { font-size: 0.95rem; font-weight: 600; color: #0f172a; }

    /* Body text */
    .letter-body {
        font-size: 1rem; color: #334155; line-height: 1.8; white-space: pre-line;
    }

    /* Buttons */
    .btn-action {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;
        border-radius: 8px; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none;
    }
    .btn-primary-custom { background: #4f46e5; color: #fff; }
    .btn-primary-custom:hover { background: #4338ca; }
    .btn-secondary-custom { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .btn-secondary-custom:hover { background: #e2e8f0; }
    .btn-success-custom { background: #16a34a; color: #fff; }
    .btn-success-custom:hover { background: #15803d; }
    
    .status-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; width: fit-content; }
    .bg-soft-blue { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .bg-soft-red { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Timeline */
    .tl-list { position: relative; padding-left: 1.5rem; margin-top: 0.5rem; }
    .tl-list::before { content: ''; position: absolute; left: 5px; top: 5px; bottom: 5px; width: 2px; background: #e2e8f0; }
    .tl-item { position: relative; margin-bottom: 1.5rem; }
    .tl-dot { position: absolute; left: -1.5rem; top: 0.3rem; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; }
    .tl-dot.blue { background: #3b82f6; }
    .tl-dot.orange { background: #f97316; }
    .tl-content { background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    .tl-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem; }
    .tl-actor { font-weight: 600; color: #0f172a; font-size: 0.9rem; }
    .tl-time { font-size: 0.8rem; color: #64748b; }
    .tl-note { font-size: 0.9rem; color: #334155; }

    /* Attachments */
    .att-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; transition: border-color 0.2s; text-decoration: none;}
    .att-card:hover { border-color: #94a3b8; }
    .att-icon { font-size: 1.5rem; }
    .att-icon.pdf { color: #ef4444; }
    .att-icon.doc { color: #3b82f6; }
    .att-icon.img { color: #10b981; }
    .att-info { display: flex; flex-direction: column; overflow: hidden; }
    .att-name { font-weight: 600; color: #0f172a; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .att-view { font-size: 0.8rem; color: #64748b; }
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

<div class="detail-wrapper">
    
    {{-- Header Card / Meta --}}
    <div class="card card-custom">
        <div class="card-header-custom">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url()->previous() }}" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-arrow-left"></i></a>
                <h4 class="card-title-custom">Informasi Surat</h4>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(in_array($role, ['admin_sekretariat', 'admin_unit']))
                    <a href="{{ route('letters.printDisposition', ['letter' => $encodedId]) }}" target="_blank" class="btn-action btn-secondary-custom"><i class="bi bi-printer"></i> Cetak PDF</a>
                @endif
                @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']))
                    @php $hideCatatanAndModifyDispo = $role === 'admin_unit' && $isDispoToMyUnit; @endphp
                    @if(!$hideCatatanAndModifyDispo)
                        @if($isRespondingDispo)
                            <button class="btn-action btn-secondary-custom" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-reply"></i> Balas</button>
                        @else
                            <button class="btn-action btn-secondary-custom" data-bs-toggle="modal" data-bs-target="#replyModal"><i class="bi bi-chat-left-text"></i> Catatan</button>
                        @endif
                    @endif
                    @if($canDispose)
                        <button class="btn-action btn-primary-custom" data-bs-toggle="modal" data-bs-target="#dispoModal"><i class="bi bi-forward"></i> {{ $hideCatatanAndModifyDispo ? 'Meneruskan' : 'Mendisposisikan' }}</button>
                    @else
                        <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="btn-action btn-primary-custom"><i class="bi bi-forward"></i> Teruskan</a>
                    @endif
                @endif
                @if(in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status === 'pending_approval')
                    <button onclick="event.preventDefault(); document.getElementById('formApprove').submit();" class="btn-action btn-success-custom"><i class="bi bi-check2-circle"></i> ACC Surat</button>
                @endif
                @if(in_array($role, ['admin_unit', 'admin_sekretariat']) && $letter->status === 'pending_sending')
                    <button onclick="event.preventDefault(); document.getElementById('formSendFinal').submit();" class="btn-action btn-success-custom"><i class="bi bi-send-fill"></i> Kirim Fisik</button>
                @endif
                @if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
                    <button data-bs-toggle="modal" data-bs-target="#agendaModal" class="btn-action btn-primary-custom"><i class="bi bi-journal-plus"></i> Beri Agenda</button>
                @endif
            </div>
        </div>
        <div class="card-body-custom">
            @php
                $isExt = in_array($letter->type, ['outbound_external', 'external']);
                if($letter->type==='outbound_external') {
                    $senderName = $letter->sender->name ?? 'Sistem';
                    $recipientName = $letter->external_recipient_name;
                } else if($letter->type==='internal') {
                    $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                    $recipientName = $letter->recipientUnit->name ?? ($letter->recipientUser->name ?? '—');
                } else { 
                    $senderName = $letter->external_sender_name;
                    $recipientName = 'Unit Tujuan (Sistem)';
                }
            @endphp
            <div class="meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Nomor Surat</span>
                    <span class="meta-value">{{ $letter->letter_number ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Nomor Agenda</span>
                    <span class="meta-value">{{ $letter->agenda_number ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status</span>
                    <span class="status-badge {{ $letter->status === 'pending_approval' ? 'bg-soft-red' : 'bg-soft-blue' }}">{{ $letter->status_label }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Pengirim</span>
                    <span class="meta-value">{{ $senderName }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tujuan</span>
                    <span class="meta-value">{{ $recipientName }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Masuk</span>
                    <span class="meta-value">{{ $letter->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Content & Attachments --}}
        <div class="col-lg-8">
            <div class="card card-custom h-100 mb-0">
                <div class="card-body-custom">
                    <h3 class="fw-bold mb-4" style="color: #0f172a; line-height: 1.4;">{{ $letter->subject }}</h3>
                    <div class="letter-body mb-5">{{ $letter->body }}</div>

                    @if($letter->external_notes)
                        <div class="alert alert-warning mb-4" style="border-radius: 8px;">
                            <strong><i class="bi bi-info-circle me-1"></i> Catatan Eksternal:</strong><br>
                            {{ $letter->external_notes }}
                        </div>
                    @endif

                    @if($letter->attachments->count() > 0)
                        <h6 class="fw-bold mb-3" style="color: #475569;"><i class="bi bi-paperclip"></i> Lampiran ({{ $letter->attachments->count() }})</h6>
                        <div class="row g-3">
                            @foreach($letter->attachments as $att)
                                @php
                                    $url = asset('storage/' . $att->file_path);
                                    $name = basename($att->file_path);
                                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $icon = 'doc';
                                    if($ext === 'pdf') $icon = 'pdf';
                                    elseif(in_array($ext, ['jpg','jpeg','png'])) $icon = 'img';
                                @endphp
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ $url }}" target="_blank" class="att-card {{ $ext === 'pdf' ? 'view-pdf' : '' }}" {{ $ext === 'pdf' ? 'data-src='.$url.' onclick="event.preventDefault();"' : '' }}>
                                        <div class="att-icon {{ $icon }}">
                                            <i class="bi {{ $icon==='pdf' ? 'bi-file-pdf-fill' : ($icon==='img' ? 'bi-file-image-fill' : 'bi-file-word-fill') }}"></i>
                                        </div>
                                        <div class="att-info">
                                            <span class="att-name" title="{{ $name }}">{{ $name }}</span>
                                            <span class="att-view">Lihat file</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        {{-- PDF Inline Preview --}}
                        <div id="pdfInlinePreview" style="display:none;" class="mt-4 border rounded overflow-hidden">
                            <div class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary ms-2"><i class="bi bi-file-pdf"></i> Pratinjau Dokumen</span>
                                <button class="btn btn-sm btn-light" onclick="document.getElementById('pdfInlinePreview').style.display='none'"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <iframe id="pdfInlineFrame" style="width: 100%; height: 600px; border: none; display: block; background:#f1f5f9;"></iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Timeline --}}
        <div class="col-lg-4">
            <div class="card card-custom h-100 mb-0">
                <div class="card-header-custom" style="padding: 1rem 1.5rem;">
                    <h5 class="card-title-custom"><i class="bi bi-clock-history me-1"></i> Riwayat & Catatan</h5>
                </div>
                <div class="card-body-custom pt-2">
                    @if($letter->histories->whereIn('action', ['disposed', 'replied'])->isNotEmpty())
                        <div class="tl-list">
                            @if($letter->histories->where('action', 'disposed')->isNotEmpty())
                            <div class="tl-item">
                                <div class="tl-dot blue"></div>
                                <div class="tl-content">
                                    <div class="tl-header">
                                        <span class="tl-actor">Subag Persuratan</span>
                                        <span class="tl-time">{{ $letter->created_at->format('d M, H:i') }}</span>
                                    </div>
                                    <div class="tl-note">Mengagendakan surat</div>
                                </div>
                            </div>
                            @endif
                            @foreach($letter->histories->whereIn('action', ['disposed', 'replied'])->sortBy('created_at') as $h)
                                @if($h->action === 'disposed' && !Str::contains($h->note, 'Diteruskan kepada personal terkait di unit'))
                                    @php
                                        $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                                        $targetName = $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—');
                                    @endphp
                                    <div class="tl-item">
                                        <div class="tl-dot blue"></div>
                                        <div class="tl-content">
                                            <div class="tl-header">
                                                <span class="tl-actor">Ke: {{ $targetName }}</span>
                                                <span class="tl-time">{{ $h->created_at->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="tl-note">
                                                Oleh {{ $h->user->name ?? 'User' }}
                                                @if($h->user_id === Auth::id())
                                                    <span class="badge bg-primary ms-1 px-2 py-1" style="font-size: 0.65rem;">Task Anda</span>
                                                @endif
                                                — "{{ preg_replace('/^\[.*?\]\s*/', '', $h->note) }}"
                                            </div>
                                        </div>
                                    </div>
                                @elseif($h->action === 'replied')
                                    <div class="tl-item">
                                        <div class="tl-dot orange"></div>
                                        <div class="tl-content" style="background: #fff7ed; border-color: #ffedd5;">
                                            <div class="tl-header">
                                                <span class="tl-actor" style="color: #c2410c;">
                                                    Catatan: {{ $h->user->name ?? 'User' }}
                                                    @if($h->user_id === Auth::id())
                                                        <span class="badge bg-warning text-dark ms-1 px-2 py-1" style="font-size: 0.65rem;">Task Anda</span>
                                                    @endif
                                                </span>
                                                <span class="tl-time">{{ $h->created_at->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="tl-note">"{{ $h->note }}"</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">Belum ada riwayat tercatat.</div>
                    @endif
                </div>
            </div>
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
                <h5 class="modal-title fw-bold">{{ isset($hideCatatanAndModifyDispo) && $hideCatatanAndModifyDispo ? 'Teruskan Surat' : 'Buat Disposisi Baru' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if(isset($hideCatatanAndModifyDispo) && $hideCatatanAndModifyDispo)
                        <input type="hidden" name="recipient_type" value="user">
                    @else
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
                    @endif
                    @if(!isset($hideCatatanAndModifyDispo) || !$hideCatatanAndModifyDispo)
                    <div class="mb-3" id="selectUnit">
                        <select name="to_unit_id" class="form-select">
                            <option value="">— Daftar Unit —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                @if($unit->id !== $user->unit_id) <option value="{{ $unit->id }}">{{ $unit->name }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="mb-3" id="selectUser" style="{{ isset($hideCatatanAndModifyDispo) && $hideCatatanAndModifyDispo ? '' : 'display:none;' }}">
                        <select name="to_user_id" class="form-select" {{ isset($hideCatatanAndModifyDispo) && $hideCatatanAndModifyDispo ? 'required' : '' }}>
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
                    
                    @if(isset($hideCatatanAndModifyDispo) && $hideCatatanAndModifyDispo)
                        <input type="hidden" name="note" value="Diteruskan kepada personal terkait di unit">
                    @else
                        <textarea name="note" class="form-control" rows="3" placeholder="Tulis instruksi disposisi..." required></textarea>
                    @endif
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

{{-- Modal Tambah Catatan --}}
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambahkan Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.reply', $letter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <textarea name="response_note" class="form-control mb-3" rows="3" placeholder="Isi catatan Anda..." required></textarea>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Catatan</button>
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
