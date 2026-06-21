@extends('layouts.app')
@section('title', 'Buat Surat Baru')

@section('content')
<style>
    /* Form Styles */
    .form-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:2rem; }
    .form-label { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:0.5rem; }
    .form-control, .form-select { height:48px;border-radius:0.75rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.95rem;font-weight:500;color:#0f172a;transition:all .2s; }
    textarea.form-control { height:auto;padding-top:0.75rem; }
    .form-control:focus, .form-select:focus { border-color:#6366f1;background:#fff;box-shadow:0 0 0 4px rgba(99,102,241,0.08);outline:none; }
    .form-control::placeholder { color:#94a3b8;font-weight:400; }
    
    /* Upload Box */
    .upload-box { background:#f8faff;border:2px dashed #cbd5e1;border-radius:1rem;padding:2rem 1.5rem;text-align:center;transition:all .2s;position:relative; }
    .upload-box:hover { border-color:#93c5fd;background:#eef2ff; }
    .upload-box .ub-icon { font-size:2.5rem;color:#94a3b8;margin-bottom:0.5rem;display:block;transition:color .2s; }
    .upload-box:hover .ub-icon { color:#3b82f6; }
    .upload-box .ub-title { font-weight:700;color:#334155;margin-bottom:0.25rem;font-size:1rem; }
    .upload-box .ub-desc { font-size:0.8rem;color:#64748b;margin-bottom:1rem; }
    .upload-input { position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer; }
    
    /* Buttons */
    .btn-submit { background:linear-gradient(135deg,#16a34a,#6366f1);color:#fff;border:none;border-radius:0.75rem;font-size:0.9rem;font-weight:700;padding:0 2rem;height:48px;transition:all .2s;display:inline-flex;align-items:center;gap:0.5rem; }
    .btn-submit:hover { transform:translateY(-2px);box-shadow:0 8px 16px rgba(99,102,241,0.2);color:#fff; }
    .btn-draft { background:#fff;color:#475569;border:1.5px solid #cbd5e1;border-radius:0.75rem;font-size:0.9rem;font-weight:700;padding:0 1.5rem;height:48px;transition:all .2s;display:inline-flex;align-items:center;gap:0.5rem; }
    .btn-draft:hover { background:#f8fafc;border-color:#94a3b8;color:#0f172a; }
    
    /* Guide Panel */
    .guide-panel { background:linear-gradient(to bottom right,#eef2ff,#fff);border:1px solid #c7d2fe;border-radius:1rem;padding:1.75rem; }
    .guide-title { display:flex;align-items:center;gap:0.5rem;font-weight:800;color:#1e3a8a;margin-bottom:1rem;font-size:1.05rem; }
    .guide-item { display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1rem; }
    .guide-item:last-child { margin-bottom:0; }
    .guide-icon { width:24px;height:24px;border-radius:6px;background:#e0e7ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;margin-top:2px; }
    .guide-text { font-size:0.85rem;color:#334155;line-height:1.5; }
    .guide-text strong { color:#0f172a; }

    /* Page Header */
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; }
    .btn-back { display:inline-flex;align-items:center;gap:0.5rem;background:#f8faff;border:1.5px solid #e8edf4;color:#475569;border-radius:0.6rem;padding:0.45rem 1rem;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all .2s; }
    .btn-back:hover { background:#eef2ff;color:#6366f1;border-color:#c7d2fe; }
    
    /* Error Alert */
    .err-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:0.75rem;padding:1rem 1.25rem;color:#991b1b;display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.5rem; }
    .err-alert i { font-size:1.25rem;color:#dc2626; }
    .err-alert ul { margin:0;padding-left:1.25rem;font-size:0.85rem;margin-top:0.25rem; }
    
    /* Preview Items */
    .file-preview-item { background:#fff;border:1px solid #e8edf4;border-radius:0.75rem;padding:0.75rem 1rem;display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem; }
    .fpi-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem; }
    .fpi-icon.pdf { background:#fef2f2;color:#dc2626; }
    .fpi-icon.doc { background:#eef2ff;color:#6366f1; }
    .fpi-icon.other { background:#f8fafc;color:#64748b; }
    .fpi-info { flex-grow:1;overflow:hidden; }
    .fpi-name { font-weight:600;font-size:0.85rem;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .fpi-meta { font-size:0.7rem;color:#64748b;margin-top:2px; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="page-title">Buat Surat Baru</h1>
        <p class="page-sub">Kirim surat internal (Peer-to-Peer) antar unit di YPI Al Azhar.</p>
    </div>
    <a href="{{ route('letters.outbound') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Surat Keluar</a>
</div>

@if($errors->any())
    <div class="err-alert">
        <i class="bi bi-exclamation-octagon-fill"></i>
        <div>
            <strong>Gagal menyimpan surat</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<style>
    .create-grid { display: grid; grid-template-columns: 340px 1fr; gap: 1.5rem; align-items: start; }
    @media(max-width:991px) { .create-grid { grid-template-columns: 1fr; } }
</style>

<div class="create-grid">
    {{-- KIRI: INPUTAN (30%) --}}
    <div>
        <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" class="form-panel shadow-sm" style="padding:1.5rem;">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nomor Surat Internal</label>
                <input type="text" name="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Opsional (Kosongkan = otomatis)">
                <div style="font-size:0.7rem;color:#94a3b8;margin-top:6px;display:flex;gap:4px;">
                    <i class="bi bi-info-circle text-primary"></i> Otomatis terisi saat dikirim jika kosong.
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tujuan Unit / Cabang <span class="text-danger">*</span></label>
                <select name="to_unit_id" class="form-select" required>
                    <option value="">— Pilih Unit Tujuan —</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('to_unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} {{ $unit->branch ? '(Cabang '.$unit->branch->name.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Perihal / Judul Surat <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Contoh: Permohonan Pengajuan..." required>
            </div>

            <div class="mb-3">
                <label class="form-label">Isi Ringkas <span class="text-danger">*</span></label>
                <textarea name="body" class="form-control" rows="4" placeholder="Keterangan singkat..." required>{{ old('body') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Lampiran Dokumen <span class="text-danger">*</span></label>
                <div class="upload-box" id="uploadBox" style="padding:1.5rem 1rem;">
                    <i class="bi bi-cloud-arrow-up-fill ub-icon" style="font-size:2rem;"></i>
                    <div class="ub-title" style="font-size:0.9rem;">Pilih / Tarik File</div>
                    <div class="ub-desc" style="font-size:0.75rem;margin-bottom:0;">Format: PDF, DOC, DOCX</div>
                    <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 pt-3" style="border-top:1px solid #e8edf4;">
                <button type="submit" name="action" value="send" class="btn-submit w-100 justify-content-center">
                    <i class="bi bi-send-fill"></i> Kirim Surat
                </button>
                <button type="submit" name="action" value="draft" class="btn-draft w-100 justify-content-center">
                    <i class="bi bi-floppy"></i> Simpan Draft
                </button>
            </div>
        </form>
        
        <div class="guide-panel shadow-sm mt-4" style="padding:1.5rem;">
            <div class="guide-title"><i class="bi bi-lightbulb-fill" style="color:#f59e0b;"></i> Panduan</div>
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-send-check-fill"></i></div>
                <div class="guide-text"><strong>Tujuan Langsung</strong><br>Pilih unit spesifik yang dituju.</div>
            </div>
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-building-fill-check"></i></div>
                <div class="guide-text"><strong>Sekretariat</strong><br>Pilih Administrator/Sekretariat YPIA.</div>
            </div>
            <hr style="border-color:#c7d2fe;margin:1rem 0;">
            <div style="font-size:0.75rem;color:#475569;">
                Lampirkan file <strong>PDF</strong> agar penerima dapat langsung membaca (preview) surat di dalam aplikasi.
            </div>
        </div>
    </div>
    
    {{-- KANAN: PREVIEW FILE (70%) --}}
    <div>
        <div class="form-panel shadow-sm h-100" style="padding:1.5rem;min-height:700px;">
            <div class="form-label mb-3">Pratinjau Dokumen Lampiran</div>
            <div id="previewList">
                <div class="text-center p-5 mt-4" style="border:2px dashed #e8edf4;border-radius:1rem;color:#94a3b8;">
                    <i class="bi bi-file-earmark-pdf" style="font-size:3rem;color:#cbd5e1;margin-bottom:1rem;display:block;"></i>
                    Belum ada dokumen yang diunggah.<br>Silakan unggah file PDF untuk melihat pratinjau.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('attachmentInput').addEventListener('change', function () {
    const files = this.files;
    const previewList = document.getElementById('previewList');
    previewList.innerHTML = '';

    if (files.length === 0) {
        previewList.innerHTML = `
            <div class="text-center p-5 mt-4" style="border:2px dashed #e8edf4;border-radius:1rem;color:#94a3b8;">
                <i class="bi bi-file-earmark-pdf" style="font-size:3rem;color:#cbd5e1;margin-bottom:1rem;display:block;"></i>
                Belum ada dokumen yang diunggah.<br>Silakan unggah file PDF untuk melihat pratinjau.
            </div>
        `;
        return;
    }

    const fileListDiv = document.createElement('div');
    fileListDiv.className = 'd-flex flex-wrap gap-2 mb-3';
    previewList.appendChild(fileListDiv);

    Array.from(files).forEach((file) => {
        const ext = file.name.split('.').pop().toLowerCase();
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        let typeClass = 'other';
        let iconName = 'bi-file-earmark';
        if (ext === 'pdf') { typeClass = 'pdf'; iconName = 'bi-file-earmark-pdf-fill'; }
        else if (['doc','docx'].includes(ext)) { typeClass = 'doc'; iconName = 'bi-file-earmark-word-fill'; }

        const item = document.createElement('div');
        item.className = 'file-preview-item flex-fill';
        item.innerHTML = `
            <div class="fpi-icon ${typeClass}"><i class="bi ${iconName}"></i></div>
            <div class="fpi-info">
                <div class="fpi-name" title="${file.name}">${file.name}</div>
                <div class="fpi-meta">${sizeMB} MB &bull; File ${ext.toUpperCase()}</div>
            </div>
            <i class="bi bi-check-circle-fill text-success ms-2"></i>
        `;
        fileListDiv.appendChild(item);

        if (files.length === 1 && ext === 'pdf') {
            const url = URL.createObjectURL(file);
            const frame = document.createElement('iframe');
            frame.src = url;
            frame.style.cssText = 'width:100%;height:650px;border:1px solid #e8edf4;border-radius:0.75rem;background:#fff;';
            previewList.appendChild(frame);
        }
    });
});
</script>
@endpush
@endsection
