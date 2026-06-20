@extends('layouts.app')
@section('title', 'Buat Surat Eksternal')

@section('content')
<style>
    /* Form Styles */
    .form-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:2rem; }
    .form-label { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:0.5rem; }
    .form-control, .form-select { height:48px;border-radius:0.75rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.95rem;font-weight:500;color:#0f172a;transition:all .2s; }
    textarea.form-control { height:auto;padding-top:0.75rem; }
    .form-control:focus, .form-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,0.08);outline:none; }
    .form-control::placeholder { color:#94a3b8;font-weight:400; }
    
    /* Upload Box */
    .upload-box { background:#f8faff;border:2px dashed #cbd5e1;border-radius:1rem;padding:2rem 1.5rem;text-align:center;transition:all .2s;position:relative; }
    .upload-box:hover { border-color:#93c5fd;background:#eff6ff; }
    .upload-box .ub-icon { font-size:2.5rem;color:#94a3b8;margin-bottom:0.5rem;display:block;transition:color .2s; }
    .upload-box:hover .ub-icon { color:#3b82f6; }
    .upload-box .ub-title { font-weight:700;color:#334155;margin-bottom:0.25rem;font-size:1rem; }
    .upload-box .ub-desc { font-size:0.8rem;color:#64748b;margin-bottom:1rem; }
    .upload-input { position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer; }
    
    /* Buttons */
    .btn-submit { background:linear-gradient(135deg,#7e22ce,#2563eb);color:#fff;border:none;border-radius:0.75rem;font-size:0.9rem;font-weight:700;padding:0 2rem;height:48px;transition:all .2s;display:inline-flex;align-items:center;gap:0.5rem; }
    .btn-submit:hover { transform:translateY(-2px);box-shadow:0 8px 16px rgba(126,34,206,0.2);color:#fff; }
    
    /* Guide Panel */
    .guide-panel { background:linear-gradient(to bottom right,#fdf4ff,#fff);border:1px solid #e9d5ff;border-radius:1rem;padding:1.75rem; }
    .guide-title { display:flex;align-items:center;gap:0.5rem;font-weight:800;color:#581c87;margin-bottom:1rem;font-size:1.05rem; }
    .guide-item { display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1rem; }
    .guide-item:last-child { margin-bottom:0; }
    .guide-icon { width:24px;height:24px;border-radius:6px;background:#fae8ff;color:#9333ea;display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;margin-top:2px; }
    .guide-text { font-size:0.85rem;color:#334155;line-height:1.5; }
    .guide-text strong { color:#0f172a; }

    /* Page Header */
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; }
    .btn-back { display:inline-flex;align-items:center;gap:0.5rem;background:#f8faff;border:1.5px solid #e8edf4;color:#475569;border-radius:0.6rem;padding:0.45rem 1rem;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all .2s; }
    .btn-back:hover { background:#eff6ff;color:#2563eb;border-color:#bfdbfe; }
    
    /* Error Alert */
    .err-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:0.75rem;padding:1rem 1.25rem;color:#991b1b;display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.5rem; }
    .err-alert i { font-size:1.25rem;color:#dc2626; }
    .err-alert ul { margin:0;padding-left:1.25rem;font-size:0.85rem;margin-top:0.25rem; }
    
    /* Preview Items */
    .file-preview-item { background:#fff;border:1px solid #e8edf4;border-radius:0.75rem;padding:0.75rem 1rem;display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem; }
    .fpi-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem; }
    .fpi-icon.pdf { background:#fef2f2;color:#dc2626; }
    .fpi-icon.doc { background:#eff6ff;color:#2563eb; }
    .fpi-icon.other { background:#f8fafc;color:#64748b; }
    .fpi-info { flex-grow:1;overflow:hidden; }
    .fpi-name { font-weight:600;font-size:0.85rem;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .fpi-meta { font-size:0.7rem;color:#64748b;margin-top:2px; }

    /* Action Radio */
    .action-box { background:#f8faff;border:1px solid #e8edf4;border-radius:1rem;padding:1.5rem;margin-bottom:2rem; }
    .radio-card { display:flex;gap:1rem;padding:1rem;border-radius:0.75rem;border:1.5px solid #e8edf4;background:#fff;margin-bottom:0.75rem;cursor:pointer;transition:all .2s; }
    .radio-card:last-child { margin-bottom:0; }
    .radio-card:hover { border-color:#bfdbfe;background:#eff6ff; }
    .radio-card.active { border-color:#2563eb;background:#eff6ff;box-shadow:0 4px 12px rgba(37,99,235,0.08); }
    .radio-card input { margin-top:3px;accent-color:#2563eb;width:1.1rem;height:1.1rem; }
    .radio-info strong { display:block;font-size:0.9rem;color:#0f172a;margin-bottom:2px; }
    .radio-info span { font-size:0.8rem;color:#64748b;line-height:1.4;display:block; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="page-title">Input Surat Eksternal</h1>
        <p class="page-sub">Catat surat fisik/digital yang masuk dari instansi luar ke unit Anda.</p>
    </div>
    <a href="{{ route('letters.inboundExternal') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Antrean</a>
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

<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('letters.storeExternal') }}" method="POST" enctype="multipart/form-data" class="form-panel shadow-sm">
            @csrf
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Instansi Pengirim <span class="text-danger">*</span></label>
                    <input type="text" name="external_sender_name" class="form-control" value="{{ old('external_sender_name') }}" placeholder="Contoh: Kementerian Pendidikan..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Surat Fisik <span class="text-danger">*</span></label>
                    <input type="text" name="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Sesuai nomor pada fisik surat..." required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Perihal / Judul Surat <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Contoh: Undangan Sosialisasi Program..." required>
            </div>

            <div class="mb-4">
                <label class="form-label">Keterangan / Ringkasan Isi <span class="text-danger">*</span></label>
                <textarea name="body" class="form-control" rows="5" placeholder="Tuliskan intisari dari surat masuk ini..." required>{{ old('body') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Scan Dokumen Fisik <span class="text-danger">*</span></label>
                <div class="upload-box" id="uploadBox">
                    <i class="bi bi-cloud-arrow-up-fill ub-icon"></i>
                    <div class="ub-title">Unggah Scan Surat Asli</div>
                    <div class="ub-desc">Format: PDF, DOC (Maks. 5MB/file)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary" style="font-weight:600;border-radius:0.5rem;pointer-events:none;">Pilih Dokumen</button>
                    <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
                </div>
                
                {{-- Preview Area --}}
                <div id="previewList" class="mt-3"></div>
            </div>

            <div class="action-box">
                <label class="form-label mb-3" style="color:#2563eb;"><i class="bi bi-signpost-split-fill me-1"></i> Tindakan Lanjutan</label>
                
                <label class="radio-card active">
                    <input type="radio" name="action_type" value="archive" checked>
                    <div class="radio-info">
                        <strong>Simpan sebagai Arsip Unit (Selesai)</strong>
                        <span>Pilih opsi ini jika surat sudah dieksekusi langsung oleh unit Anda dan hanya butuh diarsipkan.</span>
                    </div>
                </label>
                
                <label class="radio-card">
                    <input type="radio" name="action_type" value="forward">
                    <div class="radio-info">
                        <strong>Teruskan ke Sekretariat YPIA</strong>
                        <span>Pilih jika membutuhkan pertimbangan, arahan, atau disposisi Sekretariat Yayasan ke unit lain.</span>
                    </div>
                </label>
            </div>

            <div class="d-flex justify-content-end pt-3" style="border-top:1px solid #e8edf4;">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-save2-fill"></i> Simpan Surat Eksternal
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="guide-panel shadow-sm" style="position:sticky;top:2rem;">
            <div class="guide-title"><i class="bi bi-building" style="color:#9333ea;"></i> Panduan Eksternal</div>
            
            <p class="text-muted" style="font-size:0.85rem;line-height:1.6;margin-bottom:1.5rem;">
                Gunakan form ini untuk mencatat surat masuk dari <strong>pihak luar</strong> (Kementerian, instansi lain, vendor, dll).
            </p>
            
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-archive-fill"></i></div>
                <div class="guide-text"><strong>Opsi Arsip Unit</strong><br>Surat akan langsung ditandai selesai dan masuk ke arsip lokal unit Anda.</div>
            </div>
            
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                <div class="guide-text"><strong>Opsi Teruskan YPIA</strong><br>Surat akan masuk antrean Sekretariat Pusat untuk diberi nomor agenda dan disposisi pimpinan.</div>
            </div>
            
            <hr style="border-color:#e9d5ff;margin:1.25rem 0;">
            
            <div style="background:#fff;border-radius:0.75rem;padding:1rem;border:1px solid #e8edf4;font-size:0.8rem;color:#475569;">
                <i class="bi bi-file-earmark-check-fill text-purple mb-2 d-block fs-6" style="color:#9333ea;"></i>
                Pastikan hasil scan terbaca dengan jelas agar memudahkan pimpinan saat membaca pratinjau surat (PDF).
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.radio-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        this.querySelector('input').checked = true;
    });
});

document.getElementById('attachmentInput').addEventListener('change', function () {
    const files = this.files;
    const previewList = document.getElementById('previewList');
    previewList.innerHTML = '';

    if (files.length === 0) return;

    Array.from(files).forEach((file) => {
        const ext = file.name.split('.').pop().toLowerCase();
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        let typeClass = 'other';
        let iconName = 'bi-file-earmark';
        if (ext === 'pdf') { typeClass = 'pdf'; iconName = 'bi-file-earmark-pdf-fill'; }
        else if (['doc','docx'].includes(ext)) { typeClass = 'doc'; iconName = 'bi-file-earmark-word-fill'; }

        const item = document.createElement('div');
        item.className = 'file-preview-item';
        item.innerHTML = `
            <div class="fpi-icon ${typeClass}"><i class="bi ${iconName}"></i></div>
            <div class="fpi-info">
                <div class="fpi-name" title="${file.name}">${file.name}</div>
                <div class="fpi-meta">${sizeMB} MB &bull; File ${ext.toUpperCase()}</div>
            </div>
            <i class="bi bi-check-circle-fill text-success ms-2"></i>
        `;
        previewList.appendChild(item);

        if (files.length === 1 && ext === 'pdf') {
            const url = URL.createObjectURL(file);
            const frame = document.createElement('iframe');
            frame.src = url;
            frame.style.cssText = 'width:100%;height:350px;border:1px solid #e8edf4;border-radius:0.75rem;margin-top:0.75rem;background:#fff;';
            previewList.appendChild(frame);
        }
    });
});
</script>
@endpush
@endsection
