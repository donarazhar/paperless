@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->sortByDesc('created_at')->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    $statusMap = [
        'pending_approval'      => ['bg'=>'#ffedd5', 'color'=>'#c2410c', 'border'=>'#fed7aa', 'label'=>'Menunggu ACC Kepala', 'icon'=>'bi-clock'],
        'pending_sending'       => ['bg'=>'#e0f2fe', 'color'=>'#0369a1', 'border'=>'#bae6fd', 'label'=>'Menunggu Dikirim',    'icon'=>'bi-send'],
        'pending_agenda'        => ['bg'=>'#fef9c3', 'color'=>'#92400e', 'border'=>'#fde68a', 'label'=>'Antre Agenda',        'icon'=>'bi-journal-plus'],
        'in_review_subag'       => ['bg'=>'#e0e7ff', 'color'=>'#4338ca', 'border'=>'#c7d2fe', 'label'=>'Review Subag',        'icon'=>'bi-envelope-paper'],
        'in_review_bagian_tu'   => ['bg'=>'#fce7f3', 'color'=>'#be185d', 'border'=>'#fbcfe8', 'label'=>'Review Bagian TU',    'icon'=>'bi-eye-fill'],
        'in_consideration'      => ['bg'=>'#ede9fe', 'color'=>'#8b5cf6', 'border'=>'#ddd6fe', 'label'=>'Disposisi Aktif',     'icon'=>'bi-arrow-repeat'],
        'completed'             => ['bg'=>'#dcfce7', 'color'=>'#166534', 'border'=>'#bbf7d0', 'label'=>'Selesai',             'icon'=>'bi-check-circle-fill'],
        'draft'                 => ['bg'=>'#f1f5f9', 'color'=>'#475569', 'border'=>'#e2e8f0', 'label'=>'Draft',               'icon'=>'bi-pencil'],
    ];
    $sm = $statusMap[$letter->status] ?? ['bg'=>'#f1f5f9', 'color'=>'#475569', 'border'=>'#e2e8f0', 'label'=>ucfirst($letter->status),'icon'=>'bi-info-circle'];
    
    $canDispose = false;
    if ($letter->status !== 'completed') {
        if (in_array($role, ['bagian_tu', 'kepala_sekretariat'])) {
            if (in_array($letter->status, ['in_review_bagian_tu', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } elseif ($role === 'subag_persuratan') {
            if (in_array($letter->status, ['in_review_subag', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } else {
            if ($letter->to_unit_id == $user->unit_id && in_array($role, ['admin_unit', 'kepala_unit'])) {
                // If the letter is sent directly to the unit
                $canDispose = true;
            }
            elseif ($dispRecv && $dispRecv->status === 'pending' && in_array($role, ['kepala_unit', 'sub_unit'])) {
                // For unit, only Kepala Unit and Sub Unit can make new dispositions off an incoming disposition
                $canDispose = true;
            }
        }
    }

    $isDispoToMyUnit = $dispRecv && $dispRecv->to_unit_id === $user->unit_id && is_null($dispRecv->to_user_id) && $dispRecv->status === 'pending';

    $hasAction = $letter->status !== 'completed' && (
        ($role==='kepala_unit' && $letter->status==='pending_approval')
        || ($role==='admin_unit' && $letter->status==='pending_sending')
        || ($role==='admin_unit' && $isDispoToMyUnit)
        || ($role==='admin_sekretariat' && $letter->status==='pending_agenda')
        || ($role==='subag_persuratan' && $letter->status==='in_review_subag')
        || ($role==='bagian_tu' && $letter->status==='in_review_bagian_tu')
        || ($dispRecv && $dispRecv->status==='pending')
        || $canDispose
    );
@endphp



{{-- ═══ HERO HEADER ═══ --}}
<div class="detail-hero" style="align-items: flex-start; padding-bottom: 2rem;">
    <div class="hero-content" style="flex: 1;">
        <div class="status-badge mb-2" style="background:{{$sm['bg']}};color:{{$sm['color']}};border-color:{{$sm['border']}};">
            <i class="bi {{$sm['icon']}}"></i> {{$sm['label']}}
        </div>
        <div style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.1rem;">Perihal :</div>
        <h1 class="hero-title mb-1">{{ $letter->subject }}</h1>

        <div style="background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.6); border-radius: 1rem; padding: 1.25rem; backdrop-filter: blur(4px); box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            {{-- INLINE DETAILS (Merged) --}}
            <div class="d-flex flex-wrap gap-4" style="{{ $letter->body ? 'border-bottom: 1px dashed rgba(0,0,0,0.1); padding-bottom: 0.75rem; margin-bottom: 0.75rem;' : '' }}">
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">No Agenda</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $letter->agenda_number ?: '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Diterima Hari</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $letter->created_at->locale('id')->isoFormat('dddd') }}</div>
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tanggal</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $letter->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Asal Surat</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">
                        @if($letter->type==='external')
                            <span class="text-primary">{{ $letter->external_sender_name }}</span>
                        @else
                            @if(isset($letter->sender->unit))
                                {{ $letter->sender->unit->name }}
                            @else
                                {{ $letter->sender->name ?? 'Sistem' }}
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @if($letter->body)
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;"><i class="bi bi-text-paragraph me-2"></i>Isi Surat:</div>
                <div style="font-size: 0.95rem; color: #1e293b; line-height: 1.6;">
                    {!! nl2br(e($letter->body)) !!}
                </div>
            </div>
            @endif
        </div>
        
        @if($letter->type==='outbound_external')
        <div class="content-box mt-3" style="background: rgba(255,251,235,0.8); border-color:#fde68a;">
            <span class="info-label d-block mb-2" style="color:#92400e;">Keterangan Eksternal</span>
            <span style="color:#92400e;">{!! nl2br(e($letter->external_notes ?: 'Belum ada keterangan.')) !!}</span>
            @if($letter->from_user_id == Auth::id())
                <div class="mt-3">
                    <button class="btn btn-sm btn-warning fw-bold text-dark" data-bs-toggle="collapse" data-bs-target="#extNoteForm"><i class="bi bi-pencil-square"></i> Perbarui Keterangan</button>
                    <div class="collapse mt-2" id="extNoteForm">
                        <form action="{{ route('letters.updateExternalNotes', $letter) }}" method="POST">
                            @csrf
                            <textarea name="external_notes" class="form-control mb-2" rows="2">{{ $letter->external_notes }}</textarea>
                            <button type="submit" class="btn btn-sm btn-dark fw-bold">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>
    
    <a href="{{ url()->previous() }}" class="btn-back ms-4"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="mb-4">
    {{-- Sembunyikan tombol lampiran agar langsung fokus ke preview saja --}}
    @if($letter->attachments->count())
    <div class="d-none" id="attachmentButtons">
        @foreach($letter->attachments as $att)
            @php
                $url  = Storage::url($att->file_path);
                $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                $name = basename($att->file_path);
            @endphp
            @if($ext==='pdf')
                <div class="att-card att-pdf view-pdf" data-src="{{ $url }}" data-name="{{ $name }}"></div>
            @endif
        @endforeach
    </div>
    @endif
</div>

{{-- ═══ MAIN LAYOUT GRID ═══ --}}
<div class="layout-grid">
    
    {{-- KOLOM KIRI (Aksi & Timeline) --}}
    <div>
        {{-- ACC Surat Keluar (Kepala Unit) --}}
        @if($role === 'kepala_unit' && $letter->status === 'pending_approval')
        <div class="action-box primary" style="background:#fff7ed; border-color:#fed7aa;">
            <div class="action-title" style="color:#c2410c;"><i class="bi bi-check-circle-fill"></i> ACC Surat Keluar</div>
            <div class="action-desc mb-3">Tinjau dokumen dan setujui surat untuk diteruskan ke Admin Unit.</div>
            <form action="{{ route('letters.approve', $letter) }}" method="POST">
                @csrf
                <button type="submit" class="btn-custom w-100" style="background:#f97316; color:#fff; border:none;"><i class="bi bi-check2-all"></i> ACC Surat Ini</button>
            </form>
        </div>
        @endif

        {{-- Kirim Surat (Admin Unit) --}}
        @if($role === 'admin_unit' && $letter->status === 'pending_sending')
        <div class="action-box primary" style="background:#f0f9ff; border-color:#bae6fd;">
            <div class="action-title" style="color:#0369a1;"><i class="bi bi-send-fill"></i> Kirim Fisik Surat</div>
            <div class="action-desc mb-3">Surat ini telah di-ACC Kepala Unit. Harap kirimkan fisik surat dan klik tombol di bawah.</div>
            <form action="{{ route('letters.sendFinal', $letter) }}" method="POST">
                @csrf
                <button type="submit" class="btn-custom w-100" style="background:#0ea5e9; color:#fff; border:none;"><i class="bi bi-rocket-takeoff-fill"></i> Konfirmasi Pengiriman</button>
            </form>
        </div>
        @endif

        {{-- Teruskan Disposisi ke Kepala (Admin Unit) --}}
        @if($role === 'admin_unit' && $isDispoToMyUnit)
        <div class="action-box primary">
            <div class="action-title text-primary"><i class="bi bi-arrow-right-circle-fill"></i> Teruskan Disposisi</div>
            <div class="action-desc mb-3">Ada disposisi masuk untuk unit Anda. Teruskan ke Kepala Unit untuk diproses.</div>
            <form action="{{ route('letters.dispositions.forwardToKepala', $letter) }}" method="POST">
                @csrf
                <button type="submit" class="btn-custom primary w-100"><i class="bi bi-person-up"></i> Teruskan ke Kepala Unit</button>
            </form>
        </div>
        @endif

        {{-- Selesaikan Disposisi --}}
        @if($dispRecv && $dispRecv->status==='pending' && $dispRecv->to_user_id === $user->id)
        <div class="action-box warning">
            <div class="action-title text-warning-emphasis"><i class="bi bi-exclamation-circle-fill"></i> Tindakan Diperlukan</div>
            <div class="action-desc">Disposisi dari <strong>{{ $dispRecv->fromUser->name ?? 'Sistem' }}</strong>:<br><em style="color:#92400e;">"{{ $dispRecv->note }}"</em></div>
            <div class="d-flex flex-column gap-2">
                <button class="btn-custom success w-100" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-check-circle-fill"></i> Tandai Selesai / Tanggapi</button>
            </div>
        </div>
        @endif

        {{-- Agenda --}}
        @if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
        <div class="action-box primary">
            <div class="action-title text-primary"><i class="bi bi-journal-plus"></i> Beri Nomor Agenda</div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <input type="text" name="agenda_number" class="form-control mb-2" placeholder="Nomor agenda…" required>
                <textarea name="note" class="form-control mb-3" rows="2" placeholder="Catatan pengantar (opsional)…"></textarea>
                <button type="submit" class="btn-custom primary w-100"><i class="bi bi-send-fill"></i> Agendakan & Teruskan</button>
            </form>
        </div>
        @endif

        {{-- Review Subag --}}
        @if($role==='subag_persuratan' && $letter->status==='in_review_subag')
        <div class="action-box primary">
            <div class="action-title text-primary"><i class="bi bi-envelope-paper"></i> Review Subag Persuratan</div>
            <div class="action-desc mb-3">Surat telah memiliki agenda. Silakan periksa kelengkapan, lalu teruskan ke Bagian TU.</div>
            <form action="{{ route('letters.forwardToBagianTu', $letter) }}" method="POST">
                @csrf
                <button type="submit" class="btn-custom primary w-100"><i class="bi bi-arrow-right"></i> Teruskan ke Bagian TU</button>
            </form>
        </div>
        @endif

        {{-- Selesaikan (Arsip Sekretariat) --}}
        @if(in_array($role, ['bagian_tu', 'subag_persuratan']) && $letter->status !== 'completed' && !in_array($letter->status, ['draft', 'pending_approval', 'pending_sending', 'pending_agenda']))
        <div class="action-box" style="background:#f0fdf4; border:1px solid #bbf7d0;">
            <div class="action-title" style="color:#166534;"><i class="bi bi-archive-fill"></i> Arsipkan Surat</div>
            <form action="{{ route('letters.complete', $letter) }}" method="POST">
                @csrf
                <button type="submit" class="btn-custom w-100" style="background:#22c55e; color:#fff; border:none;" onclick="return confirm('Surat ini sudah selesai diproses?')"><i class="bi bi-check-circle-fill"></i> Tandai Selesai (Arsip)</button>
            </form>
        </div>
        @endif

        {{-- Disposisi --}}
        @if($canDispose)
        <div class="action-box default">
            <div class="action-title"><i class="bi bi-arrow-right-circle-fill text-primary"></i> Disposisi</div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="d-flex gap-3 mb-3 p-2 rounded" style="background:var(--surface-2); border:1px solid var(--border);">
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                        <label class="form-check-label fw-semibold" for="typeUnit" style="font-size:0.85rem;">Ke Unit</label>
                    </div>
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                        <label class="form-check-label fw-semibold" for="typeUser" style="font-size:0.85rem;">Ke Personal</label>
                    </div>
                </div>
                <div class="mb-3" id="selectUnit">
                    <select name="to_unit_id" class="form-select">
                        <option value="">— Pilih Unit —</option>
                        @foreach(\App\Models\Unit::all() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->branch->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="selectUser" style="display:none;">
                    <select name="to_user_id" class="form-select">
                        <option value="">— Pilih Organ / Jabatan —</option>
                        @foreach(\App\Models\Unit::with('organs.users')->get() as $unit)
                            @if($unit->organs->isNotEmpty())
                                <optgroup label="{{ $unit->name }} (Cab. {{ $unit->branch->name ?? '' }})">
                                    @foreach($unit->organs as $organ)
                                        @foreach($organ->users as $u)
                                            <option value="{{ $u->id }}">{{ $organ->name }} — {{ $u->name }}</option>
                                        @endforeach
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <textarea name="note" class="form-control mb-3" rows="2" placeholder="Tulis catatan instruksi / arahan disposisi…" required></textarea>
                <button type="submit" class="btn-custom w-100" style="background:#fef9c3; color:#92400e; border:1.5px solid #fde68a;"><i class="bi bi-send-check-fill"></i> Kirim Disposisi</button>
            </form>
        </div>
        @endif


        @if(!$hasAction)
        <div class="d-flex align-items-center gap-3 p-3 mb-4" style="background:var(--surface); border:1.5px solid var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(15,23,42,0.03);">
            <div style="width:36px; height:36px; background:var(--green-soft); color:var(--green); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="bi bi-shield-check" style="font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:0.85rem; font-weight:700; color:var(--text);">Semua Beres</div>
                <div style="font-size:0.75rem; color:var(--muted);">Tidak ada tindakan yang diperlukan oleh Anda saat ini.</div>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="modern-panel">
            <div class="panel-title"><i class="bi bi-clock-history"></i> Disposisi Surat</div>
            @if($letter->histories->where('action', 'disposed')->isEmpty())
                <div class="text-center p-3 text-muted" style="font-size:0.85rem;">Belum ada disposisi.</div>
            @else
            <div class="tl">
                @foreach($letter->histories->where('action', 'disposed')->sortBy('created_at') as $h)
                    @php
                        $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot disp"><i class="bi bi-circle-fill" style="font-size:6px;color:inherit;"></i></div>
                        <div class="tl-card">
                            <div class="tl-header">
                                <span class="tl-title">Disposisi Surat</span>
                                <span class="tl-time">{{ $h->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($dispMatch)
                                <div class="tl-user">Ke: <strong style="color:var(--text);">{{ $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—') }}</strong></div>
                            @endif
                            @if($h->note)
                                @php $cleanNote = preg_replace('/^\[.*?\]\s*/', '', $h->note); @endphp
                                <div class="tl-note">"{{ $cleanNote }}"</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- KOLOM KANAN (Preview) --}}
    <div>
        <div class="modern-panel h-100" style="margin-bottom:0; padding:0; overflow:hidden;">
            @if($letter->attachments->filter(fn($att) => strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION)) === 'pdf')->count() > 0)
                <div id="pdfInlinePreview" style="display:none;" class="h-100">
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="background:#fff;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--text);" id="pdfPreviewName"></span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Pratinjau Dokumen</span>
                    </div>
                    <iframe id="pdfInlineFrame" style="width:100%;height:800px;border:none;background:#f8fafc;display:block;"></iframe>
                </div>
            @else
                <div class="text-center py-5 my-4 m-3" style="border:2px dashed var(--border); border-radius:1rem; background:var(--surface-2);">
                    <div style="width:72px; height:72px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                        <i class="bi bi-file-earmark-x" style="font-size:2rem;color:var(--muted-light);"></i>
                    </div>
                    <div style="font-size:1rem; font-weight:700; color:var(--text); margin-bottom:0.25rem;">Tidak Ada Pratinjau Dokumen</div>
                    <div style="font-size:0.85rem; color:var(--muted);">Hanya lampiran format PDF yang dapat ditampilkan di sini.</div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- MODALS --}}
@if($dispRecv)
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1.5rem;">
            <div class="modal-header bg-light border-0" style="border-radius:1.5rem 1.5rem 0 0;">
                <h5 class="modal-title fw-bold">Tanggapi Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.respond', $dispRecv->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tindakan / Status Lanjutan <span class="text-danger">*</span></label>
                        <select name="action" class="form-select" required>
                            <option value="">— Pilih Tindakan —</option>
                            <option value="accepted">Selesai / Diterima</option>
                            <option value="pertimbangan">Membutuhkan Pertimbangan</option>
                            <option value="followup">Akan Ditindaklanjuti</option>
                            <option value="rejected">Ditolak / Tidak Valid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Tanggapan <span class="text-danger">*</span></label>
                        <textarea name="response_note" class="form-control" rows="3" placeholder="Tuliskan keterangan hasil proses Anda..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold" style="border-radius:0.75rem;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="border-radius:0.75rem;"><i class="bi bi-save-fill"></i> Simpan Tanggapan</button>
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
    const prev = document.getElementById('pdfInlinePreview');
    const frame = document.getElementById('pdfInlineFrame');
    const nameEl = document.getElementById('pdfPreviewName');
    const pdfBtns = document.querySelectorAll('.view-pdf');

    pdfBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if(!frame || !prev) return;
            frame.src = btn.dataset.src;
            nameEl.textContent = btn.dataset.name;
            prev.style.display = 'block';
            
            // Activate state
            pdfBtns.forEach(b => b.style.borderColor = 'var(--border)');
            btn.style.borderColor = '#dc2626';

            if(e.isTrusted) prev.scrollIntoView({behavior:'smooth', block:'center'});
        });
    });

    if(pdfBtns.length > 0) pdfBtns[0].click();

    const sU = document.getElementById('selectUnit');
    const sP = document.getElementById('selectUser');
    document.getElementsByName('recipient_type').forEach(r => r.addEventListener('change', () => {
        const u = document.getElementById('typeUser').checked;
        if(sU) sU.style.display = u ? 'none' : 'block';
        if(sP) sP.style.display = u ? 'block' : 'none';
    }));
});
</script>
@endpush
