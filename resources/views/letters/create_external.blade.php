@extends('layouts.mailbox')
@section('title', 'Catat Surat Eksternal')

@section('content')
<style>
    /* Form Styles */
    .form-panel { 
        background: #ffffff; 
        border: 1px solid rgba(0,0,0,0.04); 
        border-radius: 1.5rem; 
        padding: 2.5rem; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); 
    }
    .form-control, .form-select { 
        border-radius: 0.75rem; 
        border: 1.5px solid #e2e8f0; 
        background: #f8fafc; 
        font-size: 0.95rem; 
        font-weight: 500; 
        color: #0f172a; 
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    textarea.form-control { min-height: 120px; }
    .form-control:focus, .form-select:focus { 
        border-color: #4f46e5; 
        background: #ffffff; 
        box-shadow: 0 0 0 4px rgba(79,70,229,0.1); 
        outline: none; 
    }
    
    /* Floating Labels tweak */
    .form-floating > label { color: #64748b; font-weight: 500; padding-left: 1.25rem; }
    .form-floating > .form-control { padding-left: 1.25rem; }
    .form-floating > .form-select { padding-left: 1.25rem; padding-top: 1.625rem; padding-bottom: 0.625rem; }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label { 
        color: #4f46e5; 
        font-weight: 600; 
        transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem); 
    }
    
    /* Radio Cards */
    .radio-card { position: relative; display: block; cursor: pointer; height: 100%; }
    .radio-card input { position: absolute; opacity: 0; }
    .rc-content { 
        border: 2px solid #e2e8f0; 
        border-radius: 1rem; 
        padding: 1.25rem; 
        display: flex; 
        align-items: center; 
        gap: 1.25rem; 
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
        background: #ffffff; 
        height: 100%;
    }
    .rc-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: #f1f5f9; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; flex-shrink: 0;
    }
    .rc-icon i { font-size: 1.5rem; color: #64748b; transition: all 0.2s; }
    .rc-text strong { display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 0.2rem; }
    .rc-text span { font-size: 0.8rem; color: #64748b; line-height: 1.3; display: block; }
    
    .radio-card input:checked + .rc-content { border-color: #4f46e5; background: #eef2ff; box-shadow: 0 4px 15px rgba(79,70,229,0.1); }
    .radio-card input:checked + .rc-content .rc-icon { background: #4f46e5; }
    .radio-card input:checked + .rc-content .rc-icon i { color: #ffffff; }
    
    .radio-card:hover .rc-content { border-color: #cbd5e1; }
    .radio-card input:checked:hover + .rc-content { border-color: #4338ca; }

    /* Page Header */
    .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 0.25rem; }
    .page-sub { font-size: 0.9rem; color: #64748b; }
    .btn-back { 
        display: inline-flex; align-items: center; gap: 0.5rem; 
        background: #ffffff; border: 1.5px solid #e2e8f0; color: #475569; 
        border-radius: 100px; padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 600; 
        text-decoration: none; transition: all 0.2s; 
    }
    .btn-back:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    
    /* Error Alert */
    .err-alert { background: #fef2f2; border: 1px solid #fecaca; border-radius: 1rem; padding: 1.25rem 1.5rem; color: #991b1b; display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(220,38,38,0.05); }
    .err-alert i { font-size: 1.25rem; color: #dc2626; }
    .err-alert ul { margin: 0; padding-left: 1.25rem; font-size: 0.85rem; margin-top: 0.25rem; }

    /* Upload Box */
    .upload-box {
        border: 2px dashed #cbd5e1; border-radius: 1rem; background: #f8fafc;
        text-align: center; padding: 2.5rem 1rem; cursor: pointer; position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;
    }
    .upload-box:hover { border-color: #4f46e5; background: #f1f5f9; }
    .upload-input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .ub-icon { color: #94a3b8; transition: all 0.2s; }
    .upload-box:hover .ub-icon { color: #4f46e5; transform: translateY(-5px); }
    .ub-title { font-weight: 700; color: #334155; margin-top: 1rem; font-size: 1.05rem; }

    /* Preview Items */
    .file-preview-item { background:#fff;border:1px solid #e2e8f0;border-radius:0.75rem;padding:0.75rem 1rem;display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .fpi-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem; }
    .fpi-icon.pdf { background:#fef2f2;color:#dc2626; }
    .fpi-icon.doc { background:#eef2ff;color:#4f46e5; }
    .fpi-icon.other { background:#f8fafc;color:#64748b; }
    .fpi-info { flex-grow:1;overflow:hidden; }
    .fpi-name { font-weight:600;font-size:0.85rem;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .fpi-meta { font-size:0.7rem;color:#64748b;margin-top:2px; }

    /* Buttons */
    .btn-submit { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: #fff; border: none; border-radius: 0.75rem; padding: 0.85rem 1.5rem; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 4px 12px rgba(79,70,229,0.2); }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(79,70,229,0.3); color: #fff; }
    
    .section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .section-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
</style>

<div class="mail-scroll p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3" style="max-width: 900px; margin: 0 auto;">
        <div>
            <h1 class="page-title">Catat Surat Fisik Masuk</h1>
            <p class="page-sub">Rekam surat dari instansi luar untuk didisposisikan atau diarsipkan.</p>
        </div>
        <a href="{{ route('letters.inbound') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">
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

        <form action="{{ route('letters.storeExternal') }}" method="POST" enctype="multipart/form-data" class="form-panel" id="createForm">
            @csrf

            <div class="section-title">Informasi Pengirim</div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="external_sender_name" class="form-control" value="{{ old('external_sender_name') }}" placeholder="Pengirim" required>
                        <label>Nama/Instansi Pengirim <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Nomor Surat" required>
                        <label>Nomor Surat Resmi <span class="text-danger">*</span></label>
                    </div>
                </div>
            </div>

            <div class="section-title mt-4 pt-2">Detail Pesan</div>

            <div class="form-floating mb-4">
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Perihal" required>
                <label>Perihal / Judul Surat <span class="text-danger">*</span></label>
            </div>

            <div class="form-floating mb-4">
                <textarea name="body" class="form-control" placeholder="Keterangan..." required style="height: 150px">{{ old('body') }}</textarea>
                <label>Isi Ringkas / Catatan Tambahan <span class="text-danger">*</span></label>
            </div>

            <div class="section-title mt-4 pt-2">Lampiran Dokumen Fisik</div>

            <div class="mb-4">
                <div class="upload-box" id="uploadBox">
                    <i class="bi bi-cloud-arrow-up-fill ub-icon" style="font-size:2.5rem;"></i>
                    <div class="ub-title">Pilih atau Tarik File Hasil Scan</div>
                    <div class="ub-desc text-muted mt-1" style="font-size:0.8rem;">Format yang didukung: PDF, DOCX, JPG, PNG</div>
                    <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
                </div>
                <div id="previewList" class="mt-3"></div>
            </div>

            <div class="section-title mt-4 pt-3">Tindakan Selanjutnya</div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="radio-card">
                        <input type="radio" name="action_type" value="archive" onchange="toggleActionOptions()" checked>
                        <div class="rc-content">
                            <div class="rc-icon"><i class="bi bi-archive-fill"></i></div>
                            <div class="rc-text">
                                <strong>Arsip Selesai</strong>
                                <span>Simpan langsung.</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="radio-card">
                        <input type="radio" name="action_type" value="forward_unit" onchange="toggleActionOptions()">
                        <div class="rc-content">
                            <div class="rc-icon"><i class="bi bi-diagram-3-fill"></i></div>
                            <div class="rc-text">
                                <strong>Disposisi Unit</strong>
                                <span>Teruskan ke unit kerja.</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="radio-card">
                        <input type="radio" name="action_type" value="forward_personal" onchange="toggleActionOptions()">
                        <div class="rc-content">
                            <div class="rc-icon"><i class="bi bi-person-fill"></i></div>
                            <div class="rc-text">
                                <strong>Disposisi Personal</strong>
                                <span>Teruskan ke pegawai.</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pilihan Unit -->
            <div id="targetUnitContainer" class="mb-4" style="display: none; animation: fadeIn 0.3s;">
                <div class="form-floating">
                    <select name="to_unit_id" class="form-select">
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <label>Pilih Unit Kerja <span class="text-danger">*</span></label>
                </div>
            </div>

            <!-- Pilihan Personal -->
            <div id="targetPersonalContainer" class="mb-4" style="display: none; animation: fadeIn 0.3s;">
                <div class="form-floating">
                    <select name="to_user_id" class="form-select">
                        <option value="">— Pilih Pegawai Tujuan —</option>
                        @foreach($users as $usr)
                            <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->unit->name ?? 'Tanpa Unit' }})</option>
                        @endforeach
                    </select>
                    <label>Pilih Pegawai <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="d-flex justify-content-end pt-4 mt-4" style="border-top: 1px solid #f1f5f9;">
                <button type="submit" class="btn-submit px-5 py-3">
                    <i class="bi bi-send-fill me-2"></i> Simpan & Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

@push('scripts')
<script>
function toggleActionOptions() {
    const val = document.querySelector('input[name="action_type"]:checked').value;
    
    const unitContainer = document.getElementById('targetUnitContainer');
    const personalContainer = document.getElementById('targetPersonalContainer');
    
    const unitSelect = unitContainer.querySelector('select');
    const personalSelect = personalContainer.querySelector('select');

    if(val === 'forward_unit') {
        unitContainer.style.display = 'block';
        personalContainer.style.display = 'none';
        unitSelect.setAttribute('required', 'required');
        personalSelect.removeAttribute('required');
    } else if(val === 'forward_personal') {
        unitContainer.style.display = 'none';
        personalContainer.style.display = 'block';
        personalSelect.setAttribute('required', 'required');
        unitSelect.removeAttribute('required');
    } else {
        unitContainer.style.display = 'none';
        personalContainer.style.display = 'none';
        unitSelect.removeAttribute('required');
        personalSelect.removeAttribute('required');
    }
}

// Trigger on load
document.addEventListener('DOMContentLoaded', () => {
    toggleActionOptions();
    
    // File upload handler
    document.getElementById('attachmentInput').addEventListener('change', function () {
        const files = this.files;
        const previewList = document.getElementById('previewList');
        const uploadBox = document.getElementById('uploadBox');
        
        previewList.innerHTML = '';

        if (files.length === 0) {
            uploadBox.style.borderColor = '#cbd5e1';
            uploadBox.style.background = '#f8fafc';
            uploadBox.querySelector('.ub-title').textContent = 'Pilih atau Tarik File Hasil Scan';
            return;
        }

        uploadBox.style.borderColor = '#4f46e5';
        uploadBox.style.background = '#eef2ff';
        uploadBox.querySelector('.ub-title').textContent = files.length + ' File Dipilih';

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
});
</script>
@endpush
@endsection
