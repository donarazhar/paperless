@extends('layouts.mailbox')
@section('title', 'Tulis Surat Baru')

@section('content')
<style>
    /* ══ GMAIL-STYLE COMPOSE ══ */
    .compose-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #f6f8fc;
        overflow: hidden;
    }

    /* Top bar */
    .compose-topbar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .compose-topbar h1 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        flex: 1;
    }
    .btn-back-compose {
        display: inline-flex; align-items: center; gap: .4rem;
        background: none; border: 1.5px solid #e2e8f0; color: #475569;
        border-radius: 100px; padding: .4rem 1rem; font-size: .82rem;
        font-weight: 600; text-decoration: none; transition: all .2s;
        cursor: pointer;
    }
    .btn-back-compose:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    /* Scrollable body */
    .compose-body {
        flex: 1;
        overflow-y: auto;
        display: flex;
        justify-content: center;
        padding: 1.5rem 1rem 2rem;
    }

    /* Compose card — Gmail-like */
    .compose-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(15,23,42,.08), 0 1px 4px rgba(15,23,42,.04);
        width: 100%;
        max-width: 680px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    /* Type switcher pills */
    .type-bar {
        display: flex;
        gap: .5rem;
        padding: 1rem 1.25rem .75rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .type-pill {
        display: flex; align-items: center; gap: .45rem;
        padding: .4rem .95rem; border-radius: 100px;
        font-size: .8rem; font-weight: 600;
        border: 1.5px solid #e2e8f0; background: #fff;
        cursor: pointer; transition: all .2s; color: #64748b;
        user-select: none;
    }
    .type-pill input { display: none; }
    .type-pill i { font-size: .85rem; }
    .type-pill.selected {
        background: #eef2ff;
        border-color: #6366f1;
        color: #4f46e5;
        box-shadow: 0 2px 8px rgba(99,102,241,.15);
    }
    .type-pill:hover:not(.selected) { border-color: #cbd5e1; background: #f8fafc; color: #374151; }

    /* Field rows — inline like Gmail */
    .compose-field {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding: 0 1.25rem;
        min-height: 52px;
        position: relative;
        gap: .75rem;
    }
    .compose-field:last-of-type { border-bottom: none; }
    .compose-field-label {
        font-size: .8rem;
        font-weight: 700;
        color: #94a3b8;
        width: 90px;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .compose-field input,
    .compose-field select {
        flex: 1;
        border: none;
        outline: none;
        font-size: .9rem;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        color: #0f172a;
        background: transparent;
        padding: .75rem 0;
        min-width: 0;
    }
    .compose-field select { cursor: pointer; }
    .compose-field input::placeholder { color: #cbd5e1; font-weight: 400; }

    /* Subject row — slightly bigger */
    .compose-field.is-subject input {
        font-size: .95rem;
        font-weight: 600;
        padding: .85rem 0;
    }

    /* Body area */
    .compose-body-field {
        padding: .75rem 1.25rem;
        flex: 1;
    }
    .compose-body-field textarea {
        width: 100%;
        border: none;
        outline: none;
        font-size: .9rem;
        font-family: 'Inter', sans-serif;
        color: #0f172a;
        line-height: 1.65;
        resize: none;
        background: transparent;
        min-height: 80px;
    }
    .compose-body-field textarea::placeholder { color: #cbd5e1; }

    /* Attachment drop zone */
    .compose-attach {
        margin: 0 1.25rem .75rem;
    }
    .attach-drop {
        border: 1.5px dashed #e2e8f0;
        border-radius: .5rem;
        background: #fafbfc;
        padding: .5rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        cursor: pointer;
        position: relative;
        transition: all .25s;
    }
    .attach-drop:hover { border-color: #6366f1; background: #eef2ff; }
    .attach-drop input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .attach-drop i { font-size: 1.2rem; color: #94a3b8; transition: color .2s; }
    .attach-drop:hover i { color: #6366f1; }
    .attach-drop p { margin: 0; font-size: .78rem; color: #94a3b8; font-weight: 500; }
    .attach-drop.has-files { border-color: #6366f1; background: #eef2ff; }
    .attach-drop.has-files i { color: #4f46e5; }

    /* File chips */
    .file-chips { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
    .file-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 100px; padding: .25rem .65rem .25rem .5rem;
        font-size: .75rem; font-weight: 600; color: #374151;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        max-width: 200px;
    }
    .file-chip i { font-size: .85rem; }
    .file-chip .chip-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-chip.pdf i { color: #dc2626; }
    .file-chip.doc i { color: #2563eb; }
    .file-chip.img i { color: #16a34a; }

    /* Action toolbar — bottom */
    .compose-toolbar {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .85rem 1.25rem;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
        flex-wrap: wrap;
    }
    .btn-send {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #4f46e5; color: #fff;
        border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(79,70,229,.25);
    }
    .btn-send:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(79,70,229,.35); }
    .btn-save-draft {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #fff; color: #475569;
        border: 1.5px solid #e2e8f0; border-radius: 100px;
        padding: .5rem 1.2rem; font-size: .875rem; font-weight: 600;
        cursor: pointer; transition: all .2s;
    }
    .btn-save-draft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
    .toolbar-spacer { flex: 1; }

    /* Error alert */
    .err-alert {
        margin: .75rem 1.25rem 0;
        background: #fef2f2; border: 1px solid #fecaca;
        border-radius: .75rem; padding: .9rem 1rem;
        color: #991b1b; font-size: .82rem;
        display: flex; gap: .75rem; align-items: flex-start;
    }
    .err-alert i { color: #dc2626; font-size: 1rem; flex-shrink: 0; margin-top: .05rem; }
    .err-alert ul { margin: 0; padding-left: 1.1rem; }

    /* Info note on letter number */
    .field-note {
        font-size: .7rem; color: #94a3b8;
        padding: .25rem 1.25rem .5rem;
        display: flex; align-items: center; gap: .3rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .compose-topbar { padding: .65rem .9rem; }
        .compose-body { padding: .75rem .5rem 1.5rem; }
        .compose-field-label { width: 72px; font-size: .7rem; }
        .compose-field input, .compose-field select { font-size: .85rem; }
        .btn-send, .btn-save-draft { font-size: .8rem; padding: .45rem 1rem; }
    }
</style>

<div class="compose-wrapper">

    <!-- Top bar -->
    <div class="compose-topbar">
        <a href="{{ url()->previous() }}" class="btn-back-compose">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-pencil-square me-2" style="color:#6366f1;"></i>Tulis Surat Baru</h1>
    </div>

    <!-- Body -->
    <div class="compose-body">
        @php
            $defaultSubject      = old('subject');
            $defaultBody         = old('body');
            $defaultType         = old('letter_type', 'internal');
            $defaultToUnit       = old('to_unit_id');
            $defaultExtRecipient = old('external_recipient_name');

            if (isset($replyTo) && $replyTo) {
                $defaultSubject = $defaultSubject ?: 'Re: ' . $replyTo->subject;
                $defaultBody    = $defaultBody    ?: "\n\n--- Membalas Pesan ---\n" . $replyTo->body;
                if ($replyTo->type === 'internal' || $replyTo->type === 'outbound_external') {
                    $defaultType   = 'internal';
                    $defaultToUnit = $defaultToUnit ?: ($replyTo->sender->unit_id ?? '');
                } else {
                    $defaultType         = 'outbound_external';
                    $defaultExtRecipient = $defaultExtRecipient ?: $replyTo->external_sender_name;
                }
            } elseif (isset($forward) && $forward) {
                $defaultSubject = $defaultSubject ?: 'Fwd: ' . $forward->subject;
                $defaultBody    = $defaultBody    ?: "\n\n--- Diteruskan ---\n" . $forward->body;
            }
        @endphp

        <form action="{{ route('letters.store') }}" method="POST"
              enctype="multipart/form-data" id="createForm" style="width:100%;max-width:680px;">
            @csrf

            <div class="compose-card">

                <!-- Error -->
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

                <!-- Type switcher -->
                <div class="type-bar">
                    <label class="type-pill {{ $defaultType !== 'outbound_external' ? 'selected' : '' }}" id="pillInternal">
                        <input type="radio" name="letter_type" value="internal"
                               onchange="toggleType()"
                               {{ $defaultType !== 'outbound_external' ? 'checked' : '' }}>
                        <i class="bi bi-building"></i> Internal
                    </label>
                    <label class="type-pill {{ $defaultType === 'outbound_external' ? 'selected' : '' }}" id="pillExternal">
                        <input type="radio" name="letter_type" value="outbound_external"
                               onchange="toggleType()"
                               {{ $defaultType === 'outbound_external' ? 'checked' : '' }}>
                        <i class="bi bi-globe"></i> Eksternal
                    </label>
                    <span style="font-size:.75rem;color:#94a3b8;margin-left:auto;align-self:center;">
                        <i class="bi bi-shield-check me-1"></i>Surat Resmi YPI Al Azhar
                    </span>
                </div>

                <!-- Kepada (Internal) -->
                <div class="compose-field" id="fieldInternal"
                     style="{{ $defaultType === 'outbound_external' ? 'display:none;' : '' }}">
                    <span class="compose-field-label">Kepada</span>
                    <select name="to_unit_id" id="to_unit_id">
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}"
                                {{ $defaultToUnit == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kepada (Eksternal) -->
                <div class="compose-field" id="fieldExternal"
                     style="{{ $defaultType !== 'outbound_external' ? 'display:none;' : '' }}">
                    <span class="compose-field-label">Kepada</span>
                    <input type="text" name="external_recipient_name" id="external_recipient_name"
                           value="{{ $defaultExtRecipient }}"
                           placeholder="Nama instansi / pihak eksternal...">
                </div>

                <!-- Nomor Surat -->
                <div class="compose-field">
                    <span class="compose-field-label">No. Surat</span>
                    <input type="text" name="letter_number" id="letter_number"
                           value="{{ old('letter_number') }}"
                           placeholder="Kosongkan jika otomatis...">
                </div>
                <div class="field-note">
                    <i class="bi bi-info-circle"></i>
                    Nomor surat akan terisi otomatis saat dikirim jika dikosongkan.
                </div>

                <!-- Perihal -->
                <div class="compose-field is-subject">
                    <span class="compose-field-label">Perihal</span>
                    <input type="text" name="subject" id="subject"
                           value="{{ $defaultSubject }}"
                           placeholder="Judul / perihal surat..." required>
                </div>

                <!-- Body -->
                <div class="compose-body-field">
                    <textarea name="body" id="body"
                              placeholder="Tulis isi ringkas surat di sini..."
                              required
                              oninput="autoResize(this)">{{ $defaultBody }}</textarea>
                </div>

                <!-- Attachment -->
                <div class="compose-attach">
                    <div class="attach-drop" id="attachDrop">
                        <i class="bi bi-paperclip"></i>
                        <p id="attachLabel">Pilih / Tarik Dokumen (Khusus PDF)</p>
                        <input type="file" name="attachments[]" id="attachmentInput"
                               multiple required accept=".pdf">
                    </div>
                    <div class="file-chips" id="fileChips"></div>
                </div>

                <!-- Toolbar -->
                <div class="compose-toolbar">
                    <button type="submit" name="action" value="send" class="btn-send">
                        <i class="bi bi-send-fill"></i> Ajukan Surat
                    </button>
                    <button type="submit" name="action" value="draft" class="btn-save-draft">
                        <i class="bi bi-floppy"></i> Simpan Draft
                    </button>
                    <div class="toolbar-spacer"></div>
                    <span style="font-size:.72rem;color:#94a3b8;">
                        <i class="bi bi-lock-fill me-1"></i>Aman & Terenkripsi
                    </span>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    /* Toggle internal/eksternal */
    function toggleType() {
        const val = document.querySelector('input[name="letter_type"]:checked').value;
        const isExt = (val === 'outbound_external');

        document.getElementById('fieldInternal').style.display = isExt ? 'none' : 'flex';
        document.getElementById('fieldExternal').style.display = isExt ? 'flex' : 'none';
        document.getElementById('to_unit_id').required = !isExt;
        document.getElementById('external_recipient_name').required = isExt;

        // Update pill styling
        document.getElementById('pillInternal').classList.toggle('selected', !isExt);
        document.getElementById('pillExternal').classList.toggle('selected', isExt);
    }

    /* Auto-resize textarea */
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleType();

        // Auto-resize on load (for old() values)
        const body = document.getElementById('body');
        if (body) autoResize(body);

        /* File attachment preview & accumulation */
        const input     = document.getElementById('attachmentInput');
        const drop      = document.getElementById('attachDrop');
        const chips     = document.getElementById('fileChips');
        const label     = document.getElementById('attachLabel');
        const defaultLabel = label.textContent;
        let allFiles = [];

        input.addEventListener('change', function () {
            const newFiles = Array.from(this.files);
            // Append newly selected files
            allFiles = allFiles.concat(newFiles);
            updateFileInputAndUI();
        });

        /* Drag-and-drop highlight */
        drop.addEventListener('dragover',  function (e) { e.preventDefault(); drop.classList.add('has-files'); });
        drop.addEventListener('dragleave', function ()  { if (!allFiles.length) drop.classList.remove('has-files'); });
        drop.addEventListener('drop', function (e) { 
            e.preventDefault(); 
            const newFiles = Array.from(e.dataTransfer.files);
            allFiles = allFiles.concat(newFiles);
            updateFileInputAndUI();
        });

        function updateFileInputAndUI() {
            const dt = new DataTransfer();
            allFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;

            chips.innerHTML = '';

            if (allFiles.length > 0) {
                drop.classList.add('has-files');
                label.textContent = allFiles.length + ' file PDF dipilih (Bisa tambah file lagi)';

                allFiles.forEach(function (file, index) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    let iconClass = '', iconBi = 'file-earmark';
                    if (ext === 'pdf')                    { iconClass = 'pdf'; iconBi = 'file-earmark-pdf-fill'; }
                    else if (['doc','docx'].includes(ext)){ iconClass = 'doc'; iconBi = 'file-earmark-word-fill'; }
                    else if (['jpg','jpeg','png'].includes(ext)){ iconClass = 'img'; iconBi = 'file-earmark-image-fill'; }

                    const chip = document.createElement('div');
                    chip.className = 'file-chip ' + iconClass;
                    chip.innerHTML = `
                        <i class="bi bi-${iconBi}"></i>
                        <span class="chip-name">${file.name}</span>
                        <i class="bi bi-x-circle-fill remove-file" data-index="${index}" style="cursor:pointer; color:#ef4444; margin-left:.25rem; font-size:.85rem;"></i>
                    `;
                    chips.appendChild(chip);
                });

                // Attach remove event
                document.querySelectorAll('.remove-file').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent triggering the file input click
                        e.preventDefault();
                        const idx = parseInt(this.getAttribute('data-index'));
                        allFiles.splice(idx, 1);
                        updateFileInputAndUI();
                    });
                });
            } else {
                drop.classList.remove('has-files');
                label.textContent = defaultLabel;
            }
        }
    });
</script>
@endpush
@endsection
