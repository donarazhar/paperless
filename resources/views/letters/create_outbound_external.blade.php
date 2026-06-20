@extends('layouts.app')
@section('title', 'Buat Surat Keluar Eksternal')

@section('content')
<style>
    /* Form Styles */
    .form-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:2rem; }
    .form-label { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:0.5rem; }
    .form-control { height:48px;border-radius:0.75rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.95rem;font-weight:500;color:#0f172a;transition:all .2s; }
    textarea.form-control { height:auto;padding-top:0.75rem; }
    .form-control:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,0.08);outline:none; }
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
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="page-title">Catat Surat Keluar Eksternal</h1>
        <p class="page-sub">Arsipkan data surat dari YPI Al Azhar yang dikirim ke Instansi Luar.</p>
    </div>
    <a href="{{ route('letters.outboundExternal') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat</a>
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
        <form action="{{ route('letters.storeOutboundExternal') }}" method="POST" enctype="multipart/form-data" class="form-panel shadow-sm">
            @csrf
            
            <div class="mb-4">
                <label class="form-label" style="color:#7e22ce;"><i class="bi bi-building me-1"></i> Instansi/Pihak Luar Tujuan <span class="text-danger">*</span></label>
                <input type="text" name="external_recipient_name" class="form-control" style="border-color:#e9d5ff;background:#faf5ff;" value="{{ old('external_recipient_name') }}" placeholder="Contoh: Kementerian Pendidikan, Dinas Sosial..." required>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Surat <span class="text-muted text-lowercase fw-normal">(Opsional)</span></label>
                    <input type="text" name="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Bisa diisi nanti...">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Masukkan perihal / judul surat..." required>
            </div>

            <div class="mb-4">
                <label class="form-label">Ringkasan Isi Surat <span class="text-muted text-lowercase fw-normal">(Opsional)</span></label>
                <textarea name="body" class="form-control" rows="4" placeholder="Tuliskan intisari atau ringkasan isi surat di sini...">{{ old('body') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Keterangan Tambahan / Hasil <span class="text-muted text-lowercase fw-normal">(Opsional)</span></label>
                <textarea name="external_notes" class="form-control" rows="2" placeholder="Contoh: Dikirim via Kurir, Menunggu balasan...">{{ old('external_notes') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Lampiran / Scan Surat <span class="text-muted text-lowercase fw-normal">(Opsional)</span></label>
                <div class="upload-box" id="uploadBox">
                    <i class="bi bi-cloud-arrow-up-fill ub-icon"></i>
                    <div class="ub-title">Unggah File Lampiran</div>
                    <div class="ub-desc">Format: PDF, DOC, JPG (Maks. 5MB/file)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary" style="font-weight:600;border-radius:0.5rem;pointer-events:none;">Pilih Dokumen</button>
                    <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple>
                </div>
                
                {{-- Preview Area --}}
                <div id="previewList" class="mt-3"></div>
            </div>

            <div class="d-flex justify-content-end pt-3" style="border-top:1px solid #e8edf4;">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-save2-fill"></i> Simpan Data Surat
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="guide-panel shadow-sm" style="position:sticky;top:2rem;">
            <div class="guide-title"><i class="bi bi-send-check-fill" style="color:#9333ea;"></i> Panduan Pencatatan</div>
            
            <p class="text-muted" style="font-size:0.85rem;line-height:1.6;margin-bottom:1.5rem;">
                Halaman ini khusus digunakan untuk mencatat arsip surat yang <strong>dikirim ke instansi luar</strong> dari lingkungan YPI Al Azhar.
            </p>
            
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="guide-text"><strong>Langsung Selesai</strong><br>Data surat akan otomatis berstatus Selesai dan tersimpan di riwayat.</div>
            </div>
            
            <div class="guide-item">
                <div class="guide-icon"><i class="bi bi-pencil-square"></i></div>
                <div class="guide-text"><strong>Pembaruan Keterangan</strong><br>Anda dapat memperbarui form "Keterangan Tambahan" nanti apabila ada update balasan.</div>
            </div>
            
            <hr style="border-color:#e9d5ff;margin:1.25rem 0;">
            
            <div style="background:#fff;border-radius:0.75rem;padding:1rem;border:1px solid #e8edf4;font-size:0.8rem;color:#475569;">
                <i class="bi bi-info-circle-fill text-purple mb-2 d-block fs-6" style="color:#9333ea;"></i>
                Tidak perlu melampirkan file jika fisik surat belum memiliki versi digital, namun sangat disarankan untuk scan dan upload salinannya demi kelengkapan arsip.
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
