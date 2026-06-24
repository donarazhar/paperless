@extends('layouts.mailbox')
@section('title', 'Buat Surat Baru')

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
        transition: all 0.2s;
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
    .btn-draft { background: #ffffff; color: #475569; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; padding: 0.85rem 1.5rem; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-draft:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    
    .section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .section-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
</style>

<div class="mail-scroll p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3" style="max-width: 800px; margin: 0 auto;">
        <div>
            <h1 class="page-title">Buat Surat Baru</h1>
            <p class="page-sub">Tulis pesan internal atau eksternal dari satu pintu dengan mudah.</p>
        </div>
        <a href="{{ route('letters.outbound') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
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

        <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" class="form-panel" id="createForm">
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

            <div class="section-title">Informasi Dasar</div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="radio-card">
                        <input type="radio" name="letter_type" id="type_internal" value="internal" onchange="toggleType()" {{ $defaultType === 'internal' ? 'checked' : '' }}>
                        <div class="rc-content">
                            <div class="rc-icon"><i class="bi bi-building"></i></div>
                            <div class="rc-text">
                                <strong>Internal</strong>
                                <span>Lingkup YPI Al Azhar</span>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="radio-card">
                        <input type="radio" name="letter_type" id="type_external" value="outbound_external" onchange="toggleType()" {{ $defaultType === 'outbound_external' ? 'checked' : '' }}>
                        <div class="rc-content">
                            <div class="rc-icon"><i class="bi bi-globe"></i></div>
                            <div class="rc-text">
                                <strong>Eksternal</strong>
                                <span>Tujuan Luar Instansi</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Fields for Internal -->
            <div id="fieldInternal" class="mb-4" style="{{ $defaultType === 'outbound_external' ? 'display: none;' : '' }}">
                <div class="form-floating">
                    <select name="to_unit_id" id="to_unit_id" class="form-select">
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $defaultToUnit == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} {{ $unit->branch ? '(Cabang '.$unit->branch->name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <label>Tujuan Unit / Cabang <span class="text-danger">*</span></label>
                </div>
            </div>

            <!-- Fields for External -->
            <div id="fieldExternal" class="mb-4" style="{{ $defaultType === 'internal' ? 'display: none;' : '' }}">
                <div class="form-floating">
                    <input type="text" name="external_recipient_name" id="external_recipient_name" class="form-control" value="{{ $defaultExtRecipient }}" placeholder="Contoh: Kementerian Agama RI">
                    <label>Nama/Instansi Tujuan Eksternal <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="section-title mt-4 pt-2">Detail Pesan</div>

            <div class="form-floating mb-4">
                <input type="text" name="letter_number" id="letter_number" class="form-control" value="{{ old('letter_number') }}" placeholder="Opsional">
                <label>Nomor Surat (Opsional)</label>
                <div class="form-text mt-2 ms-1 text-muted" style="font-size:0.75rem;"><i class="bi bi-info-circle me-1"></i>Akan terisi otomatis saat dikirim jika dikosongkan.</div>
            </div>

            <div class="form-floating mb-4">
                <input type="text" name="subject" id="subject" class="form-control" value="{{ $defaultSubject }}" placeholder="Judul" required>
                <label>Perihal / Judul Surat <span class="text-danger">*</span></label>
            </div>

            <div class="form-floating mb-4">
                <textarea name="body" id="body" class="form-control" placeholder="Keterangan..." required style="height: 150px">{{ $defaultBody }}</textarea>
                <label>Isi Ringkas <span class="text-danger">*</span></label>
            </div>

            <div class="section-title mt-4 pt-2">Lampiran Dokumen</div>

            <div class="mb-4">
                <div class="upload-box" id="uploadBox">
                    <i class="bi bi-cloud-arrow-up-fill ub-icon" style="font-size:2.5rem;"></i>
                    <div class="ub-title">Pilih atau Tarik File Kesini</div>
                    <div class="ub-desc text-muted mt-1" style="font-size:0.8rem;">Format: PDF, DOC, JPG, PNG</div>
                    <input type="file" name="attachments[]" class="upload-input" id="attachmentInput" multiple required>
                </div>
                <!-- Tempat Preview File -->
                <div id="filePreviewContainer" class="mt-3"></div>
            </div>

            <div class="d-flex gap-3 pt-4 mt-4" style="border-top: 1px solid #f1f5f9;">
                <button type="submit" name="action" value="draft" class="btn-draft w-100 justify-content-center">
                    <i class="bi bi-floppy"></i> Simpan Draft
                </button>
                <button type="submit" name="action" value="send" class="btn-submit w-100 justify-content-center">
                    <i class="bi bi-send-fill"></i> Ajukan Surat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleType() {
        const type = document.querySelector('input[name="letter_type"]:checked').value;
        
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
                uploadBox.style.borderColor = '#4f46e5';
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
                uploadBox.style.background = '#f8fafc';
                uploadBox.querySelector('.ub-title').textContent = 'Pilih atau Tarik File Kesini';
            }
        });
    });
</script>
@endpush
@endsection
