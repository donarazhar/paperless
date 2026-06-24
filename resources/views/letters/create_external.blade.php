@extends('layouts.mailbox')
@section('title', 'Buat Surat Eksternal')

@section('content')
<style>
    /* Gmail Style Form */
    .form-panel { background:#fff; border-radius: 0.5rem; padding: 2rem 3rem; margin: 0 auto; max-width: 900px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    
    .gmail-input-group { border-bottom: 1px solid #e2e8f0; margin-bottom: 0.5rem; display: flex; align-items: center; padding: 0.5rem 0; transition: border-color 0.2s; }
    .gmail-input-group:focus-within { border-bottom-color: #0b57d0; }
    
    .gmail-label { width: 140px; font-size: 0.9rem; color: #475569; font-weight: 500; }
    
    .gmail-input { border: none; background: transparent; flex-grow: 1; font-size: 0.95rem; color: #0f172a; padding: 0.5rem; outline: none; }
    .gmail-input::placeholder { color: #94a3b8; font-weight: 400; }
    
    .gmail-textarea { border: none; background: transparent; width: 100%; font-size: 0.95rem; color: #0f172a; padding: 1rem 0; outline: none; min-height: 150px; resize: vertical; }
    
    /* Upload Box */
    .upload-box { background:#f8fafc; border:1px dashed #cbd5e1; border-radius:0.5rem; padding:1.5rem; text-align:center; transition:all .2s; position:relative; cursor: pointer; }
    .upload-box:hover { background:#f1f5f9; border-color:#94a3b8; }
    .upload-box .ub-icon { font-size:1.5rem; color:#64748b; margin-bottom:0.5rem; display:block; }
    .upload-box .ub-title { font-weight:600; color:#334155; font-size:0.9rem; margin-bottom:0.25rem; }
    .upload-input { position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer; }
    
    /* Buttons */
    .btn-submit { background:#0b57d0; color:#fff; border:none; border-radius:1.5rem; font-size:0.9rem; font-weight:600; padding:0.6rem 1.5rem; transition:all .2s; display:inline-flex; align-items:center; gap:0.5rem; }
    .btn-submit:hover { background:#0842a0; box-shadow:0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); color:#fff; }
    
    /* Page Header */
    .page-title { font-size:1.25rem; font-weight:500; color:#1f1f1f; margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem; color:#5f6368; }
    .btn-back { color:#5f6368; font-size:1.25rem; text-decoration:none; transition:color .2s; margin-right: 1rem; }
    .btn-back:hover { color:#1f1f1f; }
    
    /* Error Alert */
    .err-alert { background:#fef2f2; border:1px solid #fecaca; border-radius:0.5rem; padding:1rem; color:#991b1b; display:flex; gap:1rem; align-items:flex-start; margin-bottom:1.5rem; max-width: 900px; margin-left: auto; margin-right: auto; }
    .err-alert i { font-size:1.25rem; color:#dc2626; }
    .err-alert ul { margin:0; padding-left:1.25rem; font-size:0.85rem; margin-top:0.25rem; }

    /* Action Radio */
    .action-group { display: flex; gap: 1rem; margin-top: 1rem; margin-bottom: 2rem; }
    .radio-card { flex: 1; display:flex; gap:0.75rem; padding:1rem; border-radius:0.5rem; border:1px solid #e2e8f0; background:#fff; cursor:pointer; transition:all .2s; align-items: flex-start; }
    .radio-card:hover { border-color:#cbd5e1; background:#f8fafc; }
    .radio-card.active { border-color:#0b57d0; background:#f0f4f9; }
    .radio-card input { margin-top:4px; accent-color:#0b57d0; }
    .radio-info strong { display:block; font-size:0.85rem; color:#1f1f1f; margin-bottom:2px; font-weight: 600; }
    .radio-info span { font-size:0.75rem; color:#5f6368; line-height:1.4; display:block; }

    /* Preview Items */
    .file-preview-item { background:#fff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:0.75rem 1rem; display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .fpi-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
    .fpi-icon.pdf { background:#fef2f2; color:#dc2626; }
    .fpi-icon.doc { background:#eef2ff; color:#0b57d0; }
    .fpi-icon.other { background:#f8fafc; color:#64748b; }
    .fpi-info { flex-grow:1; overflow:hidden; }
    .fpi-name { font-weight:600; font-size:0.85rem; color:#1f1f1f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .fpi-meta { font-size:0.7rem; color:#5f6368; margin-top:2px; }
</style>

<div class="mail-scroll p-4">
    <div class="mb-4 d-flex align-items-center" style="max-width: 900px; margin: 0 auto;">
    <a href="{{ route('letters.inbound') }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h1 class="page-title">Catat Surat Fisik Masuk</h1>
        <p class="page-sub m-0">Rekam surat dari instansi luar untuk didisposisikan.</p>
    </div>
</div>

@if($errors->any())
    <div class="err-alert">
        <i class="bi bi-exclamation-octagon-fill"></i>
        <div>
            <strong style="font-size: 0.95rem; display: block; margin-bottom: 0.25rem;">Gagal menyimpan surat</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('letters.storeExternal') }}" method="POST" enctype="multipart/form-data" class="form-panel">
    @csrf
    
    <div class="gmail-input-group">
        <div class="gmail-label">Pengirim</div>
        <input type="text" name="external_sender_name" class="gmail-input" value="{{ old('external_sender_name') }}" placeholder="Contoh: Kementerian Kesehatan RI" required>
    </div>
    
    <div class="gmail-input-group">
        <div class="gmail-label">Nomor Surat</div>
        <input type="text" name="letter_number" class="gmail-input" value="{{ old('letter_number') }}" placeholder="Nomor resmi dari instansi pengirim" required>
    </div>

    <div class="gmail-input-group" style="margin-bottom: 1.5rem;">
        <div class="gmail-label">Perihal</div>
        <input type="text" name="subject" class="gmail-input" value="{{ old('subject') }}" placeholder="Subjek atau hal surat" required>
    </div>

    <textarea name="body" class="gmail-textarea" placeholder="Isi ringkas atau catatan tambahan..." required>{{ old('body') }}</textarea>

    <div class="mt-4 mb-4">
        <div class="upload-box" id="uploadBox">
            <i class="bi bi-paperclip ub-icon"></i>
            <div class="ub-title">Lampirkan File PDF/DOC</div>
            <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
        </div>
        <div id="previewList" class="mt-2"></div>
    </div>

    <div class="gmail-input-group border-0" style="margin-top: 2rem;">
        <div class="gmail-label" style="width: 100%; color: #1f1f1f; font-weight: 500;">Tindakan Selanjutnya:</div>
    </div>
    
    <div class="action-group">
        <label class="radio-card active">
            <input type="radio" name="action_type" value="archive" checked onclick="toggleActionOptions()">
            <div class="radio-info">
                <strong>Simpan sebagai Arsip Selesai</strong>
                <span>Surat tidak butuh disposisi, langsung diarsipkan.</span>
            </div>
        </label>
        
        <label class="radio-card">
            <input type="radio" name="action_type" value="forward_unit" onclick="toggleActionOptions()">
            <div class="radio-info">
                <strong>Teruskan ke Unit</strong>
                <span>Disposisikan langsung ke unit tertentu.</span>
            </div>
        </label>

        <label class="radio-card">
            <input type="radio" name="action_type" value="forward_personal" onclick="toggleActionOptions()">
            <div class="radio-info">
                <strong>Teruskan Personal</strong>
                <span>Disposisikan langsung ke individu.</span>
            </div>
        </label>
    </div>

    <!-- Pilihan Unit -->
    <div id="targetUnitContainer" class="gmail-input-group" style="display: none;">
        <div class="gmail-label">Pilih Unit</div>
        <select name="to_unit_id" class="gmail-input">
            <option value="">-- Pilih Unit --</option>
            @foreach($units as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Pilihan Personal -->
    <div id="targetPersonalContainer" class="gmail-input-group" style="display: none;">
        <div class="gmail-label">Pilih Pegawai</div>
        <select name="to_user_id" class="gmail-input">
            <option value="">-- Pilih Pegawai --</option>
            @foreach($users as $usr)
                <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->unit->name ?? 'Tanpa Unit' }})</option>
            @endforeach
        </select>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn-submit">
            <i class="bi bi-send-fill" style="font-size: 0.8rem;"></i> Simpan & Kirim
        </button>
    </div>
</form>
</div>

@push('scripts')
<script>
function toggleActionOptions() {
    // Update active class
    document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
    const checkedRadio = document.querySelector('input[name="action_type"]:checked');
    if(checkedRadio) {
        checkedRadio.parentElement.classList.add('active');
    }

    const val = checkedRadio ? checkedRadio.value : '';
    document.getElementById('targetUnitContainer').style.display = (val === 'forward_unit') ? 'flex' : 'none';
    document.getElementById('targetPersonalContainer').style.display = (val === 'forward_personal') ? 'flex' : 'none';
}

document.getElementById('attachmentInput').addEventListener('change', function () {
    const files = this.files;
    const previewList = document.getElementById('previewList');
    previewList.innerHTML = '';

    if (files.length === 0) {
        previewList.innerHTML = `
            <div class="text-center p-4 mt-2" style="border:1px dashed #e2e8f0; border-radius:0.5rem; color:#94a3b8; background: #f8fafc;">
                <i class="bi bi-file-earmark-pdf" style="font-size:2rem; color:#cbd5e1; margin-bottom:0.5rem; display:block;"></i>
                Belum ada dokumen yang diunggah.
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
