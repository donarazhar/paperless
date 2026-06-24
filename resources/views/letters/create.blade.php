@extends('layouts.mailbox')
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
    
    /* Dropdown Selection */
    .type-dropdown { border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.6rem; font-size: 0.95rem; width: 100%; max-width: 300px; outline: none; background: #fff; cursor: pointer; transition: all .2s; }
    .type-dropdown:focus { border-color: #0b57d0; box-shadow: 0 0 0 3px rgba(11,87,208,0.1); }

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

<div class="mail-scroll p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="page-title">Buat Surat Baru</h1>
            <p class="page-sub">Buat pesan internal atau eksternal baru dari satu pintu.</p>
        </div>
        <a href="{{ route('letters.outbound') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
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

    <div style="max-width: 900px; margin: 0 auto;">
        {{-- INPUTAN FORM --}}
        <div>
            <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" class="form-panel shadow-sm" id="createForm" style="padding:2rem;">
                @csrf
                
                @php
                    $defaultSubject = old('subject');
                    $defaultBody = old('body');
                    $defaultType = old('letter_type', 'internal');
                    $defaultToUnit = old('to_unit_id');
                    $defaultExtRecipient = old('external_recipient_name');

                    if (isset($replyTo) && $replyTo) {
                        $defaultSubject = $defaultSubject ?: 'Re: ' . $replyTo->subject;
                        $defaultBody = $defaultBody ?: "\n\n--- Membalas Pesan ---\n" . $replyTo->body;
                        if ($replyTo->type === 'internal' || $replyTo->type === 'outbound_external') {
                            $defaultType = 'internal';
                            $defaultToUnit = $defaultToUnit ?: ($replyTo->sender->unit_id ?? '');
                        } else {
                            $defaultType = 'outbound_external';
                            $defaultExtRecipient = $defaultExtRecipient ?: $replyTo->external_sender_name;
                        }
                    } elseif (isset($forward) && $forward) {
                        $defaultSubject = $defaultSubject ?: 'Fwd: ' . $forward->subject;
                        $defaultBody = $defaultBody ?: "\n\n--- Diteruskan ---\n" . $forward->body;
                    }
                @endphp

                <div class="mb-4">
                    <label class="form-label d-block">Jenis Surat <span class="text-danger">*</span></label>
                    <select name="letter_type" id="letter_type" class="type-dropdown" onchange="toggleType()">
                        <option value="internal" {{ $defaultType === 'internal' ? 'selected' : '' }}>Internal (Dalam YPIA)</option>
                        <option value="outbound_external" {{ $defaultType === 'outbound_external' ? 'selected' : '' }}>Eksternal (Luar Instansi)</option>
                    </select>
                </div>

                <!-- Fields for Internal -->
                <div id="fieldInternal" class="mb-3" style="{{ $defaultType === 'outbound_external' ? 'display: none;' : '' }}">
                    <label class="form-label">Tujuan Unit / Cabang <span class="text-danger">*</span></label>
                    <select name="to_unit_id" id="to_unit_id" class="form-select">
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $defaultToUnit == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} {{ $unit->branch ? '(Cabang '.$unit->branch->name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fields for External -->
                <div id="fieldExternal" class="mb-3" style="{{ $defaultType === 'internal' ? 'display: none;' : '' }}">
                    <label class="form-label">Nama/Instansi Tujuan Eksternal <span class="text-danger">*</span></label>
                    <input type="text" name="external_recipient_name" id="external_recipient_name" class="form-control" value="{{ $defaultExtRecipient }}" placeholder="Contoh: Kementerian Agama RI">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor Surat</label>
                    <input type="text" name="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Opsional (Kosongkan = otomatis)">
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:6px;display:flex;gap:4px;">
                        <i class="bi bi-info-circle text-primary"></i> Otomatis terisi saat dikirim jika kosong.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Perihal / Judul Surat <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" value="{{ $defaultSubject }}" placeholder="Contoh: Permohonan Pengajuan..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi Ringkas <span class="text-danger">*</span></label>
                    <textarea name="body" class="form-control" rows="8" placeholder="Keterangan singkat..." required>{{ $defaultBody }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Lampiran Dokumen <span class="text-danger">*</span></label>
                    <div class="upload-box" id="uploadBox" style="padding:1.5rem 1rem;">
                        <i class="bi bi-cloud-arrow-up-fill ub-icon" style="font-size:2rem;"></i>
                        <div class="ub-title" style="font-size:0.9rem;">Pilih / Tarik File</div>
                        <div class="ub-desc" style="font-size:0.75rem;margin-bottom:0;">Format: PDF, DOC, JPG, PNG</div>
                        <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
                    </div>
                    <!-- Tempat Preview File -->
                    <div id="filePreviewContainer" class="mt-3"></div>
                </div>

                <div class="d-flex flex-column gap-2 pt-3" style="border-top:1px solid #e8edf4;">
                    <button type="submit" name="action" value="send" class="btn-submit w-100 justify-content-center">
                        <i class="bi bi-send-fill"></i> Ajukan Surat
                    </button>
                    <button type="submit" name="action" value="draft" class="btn-draft w-100 justify-content-center">
                        <i class="bi bi-floppy"></i> Simpan Draft
                    </button>
                </div>
            </form>
        </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleType() {
        const type = document.getElementById('letter_type').value;
        
        const fieldInternal = document.getElementById('fieldInternal');
        const fieldExternal = document.getElementById('fieldExternal');
        const inputToUnit = document.getElementById('to_unit_id');
        const inputExternalRecipient = document.getElementById('external_recipient_name');

        if (type === 'internal') {
            fieldInternal.style.display = 'block';
            fieldExternal.style.display = 'none';
            
            inputToUnit.setAttribute('required', 'required');
            inputExternalRecipient.removeAttribute('required');
        } else {
            fieldExternal.style.display = 'block';
            fieldInternal.style.display = 'none';
            
            inputExternalRecipient.setAttribute('required', 'required');
            inputToUnit.removeAttribute('required');
        }
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', () => {
        toggleType();
        
        // Handling file previews
        const input = document.getElementById('attachmentInput');
        const previewContainer = document.getElementById('filePreviewContainer');
        const uploadBox = document.getElementById('uploadBox');

        input.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            
            if(this.files && this.files.length > 0) {
                // Change box style slightly to indicate files are selected
                uploadBox.style.borderColor = '#6366f1';
                uploadBox.style.background = '#eef2ff';
                uploadBox.querySelector('.ub-title').textContent = this.files.length + ' File Dipilih';
                
                Array.from(this.files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    
                    let iconClass = 'other';
                    let iconBi = 'file-earmark';
                    if(ext === 'pdf'){ iconClass = 'pdf'; iconBi = 'file-earmark-pdf-fill'; }
                    else if(['doc','docx'].includes(ext)){ iconClass = 'doc'; iconBi = 'file-earmark-word-fill'; }
                    
                    const mbSize = (file.size / (1024*1024)).toFixed(2);
                    
                    const item = document.createElement('div');
                    item.className = 'file-preview-item';
                    item.innerHTML = `
                        <div class="fpi-icon ${iconClass}"><i class="bi bi-${iconBi}"></i></div>
                        <div class="fpi-info">
                            <div class="fpi-name" title="${file.name}">${file.name}</div>
                            <div class="fpi-meta">${mbSize} MB</div>
                        </div>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    `;
                    previewContainer.appendChild(item);
                });
            } else {
                uploadBox.style.borderColor = '#cbd5e1';
                uploadBox.style.background = '#f8faff';
                uploadBox.querySelector('.ub-title').textContent = 'Pilih / Tarik File';
            }
        });
    });
</script>
@endpush
@endsection
