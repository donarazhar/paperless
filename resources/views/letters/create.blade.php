@extends('layouts.app')
@section('title', 'Buat Surat Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Formulir Pembuatan Surat</h1>
    <a href="{{ route('letters.outbound') }}" class="btn btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

{{-- Alert Error Validasi --}}
@if($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 0.75rem;">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <strong class="mb-0">Terdapat kesalahan pada input Anda:</strong>
        </div>
        <ul class="mb-0 text-sm">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card p-4">
            <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Field No Surat --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Nomor Surat Internal</label>
                    <input type="text" 
                           name="letter_number" 
                           class="form-control form-control-lg bg-light" 
                           value="{{ old('letter_number') }}" 
                           placeholder="Kosongkan jika ingin sistem mengisi otomatis (opsional)">
                    <div class="form-text mt-2"><i class="bi bi-info-circle"></i> Nomor surat akan terisi otomatis saat surat dikirim jika dibiarkan kosong.</div>
                </div>

                {{-- Field Perihal --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Perihal / Judul Surat</label>
                    <input type="text" 
                           name="subject" 
                           class="form-control form-control-lg bg-light" 
                           value="{{ old('subject') }}" 
                           placeholder="Contoh: Permohonan Pengajuan Dana Operasional..."
                           required>
                </div>

                {{-- Field Isi Surat --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Isi Ringkas / Keterangan Tambahan</label>
                    <textarea name="body" 
                              class="form-control bg-light" 
                              rows="6" 
                              placeholder="Tuliskan keterangan singkat mengenai surat yang dilampirkan..."
                              required>{{ old('body') }}</textarea>
                </div>

                {{-- Field Tujuan Unit --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Tujuan Unit / Cabang</label>
                    <select name="to_unit_id" class="form-select form-select-lg bg-light" required>
                        <option value="">— Pilih Unit Tujuan —</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('to_unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} {{ $unit->branch ? '(Cabang '.$unit->branch->name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Field File Lampiran --}}
                <div class="mb-5 p-4 border rounded bg-light border-dashed" style="border-style: dashed !important;">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-paperclip me-2"></i>Unggah Lampiran Dokumen</label>
                    <p class="text-muted small mb-3">Format yang didukung: PDF, DOC, DOCX (Maksimal 5MB per file). Anda dapat memilih beberapa file sekaligus.</p>
                    <input type="file" 
                           name="attachments[]" 
                           class="form-control" 
                           id="attachmentInput"
                           multiple 
                           required>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex gap-3 justify-content-end pt-3 border-top">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan ke Draft
                    </button>
                    <button type="submit" name="action" value="send" class="btn btn-primary px-5 fw-bold">
                        Kirim Surat <i class="bi bi-send-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Panel Kanan --}}
    <div class="col-lg-4 d-none d-lg-block">

        {{-- Preview Lampiran (tampil saat ada file dipilih) --}}
        <div id="previewCard" class="card p-4 border-0 shadow-sm mb-3" style="display: none !important;">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pratinjau Lampiran</h5>
            <div id="previewList"></div>
        </div>

        {{-- Panduan Pengiriman Surat --}}
        <div id="panduanCard" class="card p-4 bg-primary bg-opacity-10 border-0">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle-fill me-2"></i> Panduan Pengiriman Surat</h5>
            <p class="small text-muted mb-3" style="line-height: 1.6;">
                Anda kini dapat mengirimkan surat secara langsung (Peer-to-Peer) ke unit manapun di lingkungan YPI Al Azhar.
            </p>
            <hr class="border-primary opacity-25">
            <ul class="list-unstyled small text-muted">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Tujuan Langsung:</strong> Jika urusan spesifik antar dua unit, pilih unit yang dituju.</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Tujuan Sekretariat:</strong> Jika membutuhkan keputusan/kebijakan pusat, pilih unit Administrator/Sekretariat.</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Lacak perkembangan surat Anda melalui menu <strong>Surat Keluar</strong>.</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('attachmentInput').addEventListener('change', function () {
    const files = this.files;
    const previewCard = document.getElementById('previewCard');
    const previewList = document.getElementById('previewList');
    
    previewList.innerHTML = '';

    if (files.length === 0) {
        previewCard.style.setProperty('display', 'none', 'important');
        return;
    }

    previewCard.style.setProperty('display', 'block', 'important');

    Array.from(files).forEach((file) => {
        const ext = file.name.split('.').pop().toLowerCase();
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        let iconClass = 'bi-file-earmark text-secondary';
        let bgColor = '#f8f9fa';
        if (ext === 'pdf') { iconClass = 'bi-file-earmark-pdf text-danger'; bgColor = '#fff5f5'; }
        else if (['doc','docx'].includes(ext)) { iconClass = 'bi-file-earmark-word text-primary'; bgColor = '#f0f4ff'; }

        const item = document.createElement('div');
        item.className = 'd-flex align-items-start gap-3 p-2 rounded mb-2';
        item.style.backgroundColor = bgColor;
        item.innerHTML = `
            <i class="bi ${iconClass} fs-3 flex-shrink-0 mt-1"></i>
            <div style="overflow: hidden;">
                <div class="fw-semibold small text-truncate" title="${file.name}">${file.name}</div>
                <div class="text-muted" style="font-size: 0.75rem;">${sizeMB} MB &middot; ${ext.toUpperCase()}</div>
            </div>`;
        previewList.appendChild(item);

        // PDF inline preview jika hanya satu file PDF
        if (files.length === 1 && ext === 'pdf') {
            const url = URL.createObjectURL(file);
            const frame = document.createElement('iframe');
            frame.src = url;
            frame.style.cssText = 'width:100%;height:320px;border:1px solid #dee2e6;border-radius:0.5rem;margin-top:0.5rem;';
            frame.title = 'Pratinjau PDF';
            previewList.appendChild(frame);
        }
    });
});
</script>
@endpush
@endsection
