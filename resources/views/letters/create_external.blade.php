@extends('layouts.mailbox')
@section('title', 'Catat Surat Fisik Masuk')

@section('content')
<style>
    /* ══ CATAT SURAT EKSTERNAL — Gmail Compose Style ══ */
    .ce-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #f8fafc;
    }

    /* ── Top bar (seperti header compose Gmail) ── */
    .ce-topbar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .65rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .ce-back-btn {
        width: 34px; height: 34px; border: none; background: none;
        color: #64748b; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; transition: all .15s; text-decoration: none;
        flex-shrink: 0;
    }
    .ce-back-btn:hover { background: #f1f5f9; color: #0f172a; }
    .ce-topbar-title {
        font-size: .9rem; font-weight: 700; color: #0f172a;
    }
    .ce-topbar-sub {
        font-size: .75rem; color: #94a3b8; font-weight: 400;
    }

    /* ── Scrollable compose area ── */
    .ce-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    /* ── Compose card ── */
    .ce-card {
        width: 100%;
        max-width: 720px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(15,23,42,.07);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Card header */
    .ce-card-header {
        padding: .85rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .ce-card-icon {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .ce-card-icon i { color: #fff; font-size: .9rem; }
    .ce-card-title  { font-size: .85rem; font-weight: 700; color: #0f172a; }
    .ce-card-sub    { font-size: .72rem; color: #94a3b8; }

    /* Error alert */
    .ce-error {
        margin: 1rem 1.25rem 0;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: .85rem 1.1rem;
        display: flex; gap: .75rem; align-items: flex-start;
        color: #991b1b; font-size: .82rem;
    }
    .ce-error i { color: #dc2626; flex-shrink: 0; margin-top: .1rem; }
    .ce-error ul { margin: 0; padding-left: 1.1rem; }

    /* ── Inline field rows (Gmail style) ── */
    .ce-field {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        min-height: 44px;
        padding: 0 1.25rem;
        transition: background .12s;
    }
    .ce-field:focus-within { background: #fafbff; }
    .ce-field-label {
        font-size: .78rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .05em;
        width: 110px; flex-shrink: 0;
    }
    .ce-field-label.required::after {
        content: ' *'; color: #dc2626;
    }
    .ce-field input,
    .ce-field select {
        flex: 1; border: none; background: none;
        font-family: inherit; font-size: .875rem;
        color: #0f172a; font-weight: 500;
        padding: .6rem 0;
        outline: none;
        min-width: 0;
    }
    .ce-field input::placeholder { color: #cbd5e1; font-weight: 400; }
    .ce-field select { cursor: pointer; }
    .ce-field select option[value=""] { color: #94a3b8; }

    /* ── Body textarea ── */
    .ce-body {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .ce-body textarea {
        width: 100%; border: none; background: none;
        font-family: inherit; font-size: .875rem;
        color: #0f172a; font-weight: 400;
        resize: none; outline: none;
        min-height: 80px; line-height: 1.6;
    }
    .ce-body textarea::placeholder { color: #cbd5e1; }

    /* ── Divider label ── */
    .ce-section-label {
        display: flex; align-items: center; gap: .6rem;
        padding: .75rem 1.25rem .4rem;
        font-size: .7rem; font-weight: 700;
        color: #94a3b8; text-transform: uppercase; letter-spacing: .06em;
    }
    .ce-section-label::after {
        content: ''; flex: 1; height: 1px; background: #f1f5f9;
    }

    /* ── Tindakan (Action cards — compact) ── */
    .action-group {
        display: flex;
        gap: .5rem;
        padding: .6rem 1.25rem .75rem;
        flex-wrap: wrap;
    }
    .action-card {
        flex: 1; min-width: 120px;
        position: relative; cursor: pointer;
    }
    .action-card input { position: absolute; opacity: 0; width: 0; height: 0; }
    .action-card-body {
        display: flex; align-items: center; gap: .6rem;
        padding: .6rem .85rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        transition: all .15s;
        font-size: .8rem; font-weight: 600; color: #475569;
    }
    .action-card-body i { font-size: .9rem; color: #94a3b8; flex-shrink: 0; }
    .action-card input:checked + .action-card-body {
        border-color: #4f46e5;
        background: #eef2ff;
        color: #3730a3;
    }
    .action-card input:checked + .action-card-body i { color: #4f46e5; }
    .action-card:hover .action-card-body { border-color: #c7d2fe; }

    /* ── Target selectors (unit/personal) ── */
    .ce-target {
        padding: .5rem 1.25rem .75rem;
        animation: slideIn .2s ease;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ce-target select {
        width: 100%;
        padding: .6rem .9rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        font-size: .875rem; font-family: inherit; color: #0f172a;
        outline: none; transition: all .15s; cursor: pointer;
    }
    .ce-target select:focus {
        border-color: #4f46e5;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(79,70,229,.1);
    }

    .ce-attach-zone {
        margin: 0 1.25rem .75rem;
        border: 1.5px dashed #e2e8f0;
        border-radius: .5rem;
        padding: .5rem 1rem;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        cursor: pointer;
        transition: all .15s;
        position: relative;
    }
    .ce-attach-zone:hover { border-color: #4f46e5; background: #eef2ff; }
    .ce-attach-zone.has-files { border-style: solid; border-color: #10b981; background: #f0fdf4; }
    .ce-attach-zone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2; }
    .ce-attach-label {
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        font-size: .8rem; font-weight: 600; color: #64748b;
    }
    .ce-attach-label i { font-size: 1rem; }

    /* File chips */
    .ce-chips {
        display: flex; flex-wrap: wrap; gap: .4rem;
        padding: 0 1.25rem .75rem;
    }
    .ce-chip {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        border-radius: 100px; padding: .25rem .7rem;
        font-size: .75rem; font-weight: 600; color: #374151;
        max-width: 200px; overflow: hidden;
    }
    .ce-chip i { font-size: .75rem; color: #10b981; flex-shrink: 0; }
    .ce-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* PDF preview */
    .ce-pdf-preview {
        margin: 0 1.25rem .75rem;
        border-radius: 10px; overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .ce-pdf-preview iframe {
        width: 100%; height: 500px; border: none; display: block;
    }

    /* ── Bottom toolbar (like Gmail send bar) ── */
    .ce-footer {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .75rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .btn-save {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #4f46e5; color: #fff;
        border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .85rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 8px rgba(79,70,229,.25);
    }
    .btn-save:hover { background: #4338ca; transform: translateY(-1px); }
    .ce-footer-hint {
        font-size: .75rem; color: #94a3b8;
        display: flex; align-items: center; gap: .3rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .ce-field-label { width: 80px; font-size: .7rem; }
        .action-group { gap: .35rem; }
        .action-card { min-width: 90px; }
        .action-card-body { padding: .5rem .65rem; font-size: .75rem; }
        .ce-card { border-radius: 12px; }
        .ce-scroll { padding: .75rem .5rem; }
    }
</style>

<div class="ce-wrap">

    {{-- ── Top bar ── --}}
    <div class="ce-topbar">
        <a href="{{ route('letters.inbound') }}" class="ce-back-btn" title="Kembali ke Inbox">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div class="ce-topbar-title">Catat Surat Fisik Masuk</div>
            <div class="ce-topbar-sub">Rekam surat dari instansi luar untuk diarsipkan atau didisposisikan</div>
        </div>
    </div>

    {{-- ── Scrollable area ── --}}
    <div class="ce-scroll">
        <div class="ce-card">

            {{-- Card header --}}
            <div class="ce-card-header">
                <div class="ce-card-icon"><i class="bi bi-envelope-arrow-down-fill"></i></div>
                <div>
                    <div class="ce-card-title">Surat Masuk Eksternal</div>
                    <div class="ce-card-sub">Surat fisik dari pihak di luar organisasi</div>
                </div>
            </div>

            {{-- Error alert --}}
            @if($errors->any())
            <div class="ce-error">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div>
                    <strong style="font-size:.83rem;">Terdapat kesalahan pada form:</strong>
                    <ul class="mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- ── FORM ── --}}
            <form action="{{ route('letters.storeExternal') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="ceForm">
                @csrf

                {{-- Pengirim --}}
                <div class="ce-section-label"><i class="bi bi-person-vcard"></i> Pengirim</div>

                <div class="ce-field">
                    <span class="ce-field-label required">Dari</span>
                    <input type="text"
                           name="external_sender_name"
                           value="{{ old('external_sender_name') }}"
                           placeholder="Nama instansi atau pihak pengirim..."
                           required>
                </div>
                <div class="ce-field">
                    <span class="ce-field-label required">No. Surat</span>
                    <input type="text"
                           name="letter_number"
                           value="{{ old('letter_number') }}"
                           placeholder="Nomor surat resmi..."
                           required>
                </div>

                {{-- Detail --}}
                <div class="ce-section-label mt-1"><i class="bi bi-file-earmark-text"></i> Isi Surat</div>

                <div class="ce-field">
                    <span class="ce-field-label required">Perihal</span>
                    <input type="text"
                           name="subject"
                           value="{{ old('subject') }}"
                           placeholder="Perihal / judul surat..."
                           required>
                </div>

                <div class="ce-body">
                    <textarea name="body"
                              id="ceBody"
                              placeholder="Isi ringkas atau catatan tambahan mengenai surat ini..."
                              required>{{ old('body') }}</textarea>
                </div>

                {{-- Lampiran --}}
                <div class="ce-section-label"><i class="bi bi-paperclip"></i> Lampiran</div>

                <div class="ce-attach-zone" id="attachZone">
                    <input type="file"
                           name="attachments[]"
                           id="attachInput"
                           multiple required
                           accept=".pdf">
                    <div class="ce-attach-label" id="attachLabel">
                        <i class="bi bi-cloud-upload"></i>
                        <span>Pilih / Tarik File Hasil Scan (Khusus PDF)</span>
                    </div>
                </div>
                <div id="ceChips" class="ce-chips"></div>

                {{-- Tindakan --}}
                <div class="ce-section-label"><i class="bi bi-arrow-right-circle"></i> Tindakan Selanjutnya</div>

                <div class="action-group">
                    <label class="action-card">
                        <input type="radio" name="action_type" value="archive"
                               onchange="toggleTarget()" {{ old('action_type','archive')==='archive' ? 'checked' : '' }}>
                        <div class="action-card-body">
                            <i class="bi bi-archive-fill"></i> Arsip Selesai
                        </div>
                    </label>
                    <label class="action-card">
                        <input type="radio" name="action_type" value="forward_unit"
                               onchange="toggleTarget()" {{ old('action_type')==='forward_unit' ? 'checked' : '' }}>
                        <div class="action-card-body">
                            <i class="bi bi-diagram-3-fill"></i> Disposisi Unit
                        </div>
                    </label>
                    <label class="action-card">
                        <input type="radio" name="action_type" value="forward_personal"
                               onchange="toggleTarget()" {{ old('action_type')==='forward_personal' ? 'checked' : '' }}>
                        <div class="action-card-body">
                            <i class="bi bi-person-fill"></i> Disposisi Personal
                        </div>
                    </label>
                </div>

                {{-- Target Unit --}}
                <div id="targetUnit" class="ce-target" style="display:none;">
                    <select name="to_unit_id" id="toUnitSelect">
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('to_unit_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Target Personal --}}
                <div id="targetPersonal" class="ce-target" style="display:none;">
                    <select name="to_user_id" id="toUserSelect">
                        <option value="">— Pilih Pegawai Tujuan —</option>
                        @foreach($users as $usr)
                            <option value="{{ $usr->id }}" {{ old('to_user_id') == $usr->id ? 'selected' : '' }}>
                                {{ $usr->organ->name ?? 'Tanpa Jabatan' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Footer actions --}}
                <div class="ce-footer">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-send-fill"></i> Simpan & Lanjutkan
                    </button>
                    <span class="ce-footer-hint">
                        <i class="bi bi-shield-check"></i>
                        Data tersimpan sesuai tindakan yang dipilih
                    </span>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ── Toggle target selector ── */
function toggleTarget() {
    const val = document.querySelector('input[name="action_type"]:checked')?.value;
    const unitDiv     = document.getElementById('targetUnit');
    const personalDiv = document.getElementById('targetPersonal');
    const unitSel     = document.getElementById('toUnitSelect');
    const userSel     = document.getElementById('toUserSelect');

    if (val === 'forward_unit') {
        unitDiv.style.display     = 'block';
        personalDiv.style.display = 'none';
        unitSel.setAttribute('required', 'required');
        userSel.removeAttribute('required');
    } else if (val === 'forward_personal') {
        unitDiv.style.display     = 'none';
        personalDiv.style.display = 'block';
        userSel.setAttribute('required', 'required');
        unitSel.removeAttribute('required');
    } else {
        unitDiv.style.display     = 'none';
        personalDiv.style.display = 'none';
        unitSel.removeAttribute('required');
        userSel.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    /* Init toggle */
    toggleTarget();

    /* ── Auto-resize textarea ── */
    const ta = document.getElementById('ceBody');
    if (ta) {
        ta.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    /* ── Attachment handler ── */
    const input      = document.getElementById('attachInput');
    const zone       = document.getElementById('attachZone');
    const chipsEl    = document.getElementById('ceChips');
    const labelEl    = document.getElementById('attachLabel');

    input.addEventListener('change', function () {
        const files = this.files;
        chipsEl.innerHTML    = '';

        if (!files.length) {
            zone.classList.remove('has-files');
            labelEl.innerHTML = '<i class="bi bi-cloud-upload"></i><span>Klik atau seret file hasil scan</span>';
            return;
        }

        zone.classList.add('has-files');
        labelEl.innerHTML = `<i class="bi bi-check-circle-fill" style="color:#10b981;"></i><span>${files.length} file dipilih</span>`;

        Array.from(files).forEach(file => {
            const ext  = file.name.split('.').pop().toLowerCase();
            const size = (file.size / 1024 / 1024).toFixed(2);

            let icon = 'bi-file-earmark';
            if (ext === 'pdf')                   icon = 'bi-file-earmark-pdf-fill';
            else if (['doc','docx'].includes(ext)) icon = 'bi-file-earmark-word-fill';
            else if (['jpg','jpeg','png'].includes(ext)) icon = 'bi-file-earmark-image-fill';

            const chip = document.createElement('div');
            chip.className = 'ce-chip';
            chip.innerHTML = `<i class="bi ${icon}"></i><span title="${file.name}">${file.name}</span><span style="color:#94a3b8;margin-left:.2rem;">${size}MB</span>`;
            chipsEl.appendChild(chip);


        });
    });

    /* ── Drag & drop support ── */
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('has-files'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('has-files'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    });
});
</script>
@endpush
@endsection
