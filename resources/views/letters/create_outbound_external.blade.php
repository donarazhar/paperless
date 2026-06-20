@extends('layouts.app')
@section('title', 'Buat Surat Keluar Eksternal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('letters.outboundExternal') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="h3 fw-bold mb-0 text-dark"><i class="bi bi-send-dash-fill text-primary me-2"></i>Buat Surat Keluar Eksternal</h1>
        <p class="text-muted small mt-1 mb-0">Catat dan kirim surat dari YPI Al Azhar ke Instansi Luar.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card p-4 border-0 shadow-sm mb-4">
            <form action="{{ route('letters.storeOutboundExternal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Field Tujuan Instansi --}}
                <div class="mb-4 p-3 bg-primary bg-opacity-10 rounded border border-primary border-opacity-25">
                    <label class="form-label text-primary fw-bold small text-uppercase"><i class="bi bi-building me-1"></i> Instansi/Pihak Luar Tujuan</label>
                    <input type="text" name="external_recipient_name" class="form-control form-control-lg border-primary border-opacity-50" 
                           placeholder="Contoh: Kementerian Pendidikan, Dinas Sosial, PT ABC..." 
                           value="{{ old('external_recipient_name') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">Nomor Surat <span class="text-secondary fw-normal">(Opsional)</span></label>
                        <input type="text" name="letter_number" class="form-control bg-light" 
                               placeholder="Bisa diisi nanti..." value="{{ old('letter_number') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Perihal Surat</label>
                    <input type="text" name="subject" class="form-control bg-light" 
                           placeholder="Masukkan perihal / judul surat..." value="{{ old('subject') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Ringkasan Isi Surat <span class="text-secondary fw-normal">(Opsional)</span></label>
                    <textarea name="body" class="form-control bg-light" rows="4" 
                              placeholder="Tuliskan intisari atau ringkasan isi surat di sini...">{{ old('body') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Keterangan / Hasil (Opsional)</label>
                    <textarea name="external_notes" class="form-control bg-light" rows="2" 
                              placeholder="Contoh: Surat sudah dikirim via Kurir, Menunggu balasan...">{{ old('external_notes') }}</textarea>
                </div>

                <div class="mb-5 p-4 border rounded bg-light border-dashed" style="border-style: dashed !important;">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-paperclip me-2"></i>Unggah Lampiran (Opsional)</label>
                    <p class="text-muted small mb-3">Format yang didukung: PDF, DOC, DOCX, JPG, PNG (Maksimal 5MB). File pindaian/scan surat fisik.</p>
                    <input type="file" name="attachments[]" class="form-control" multiple>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        Simpan Data Surat <i class="bi bi-save-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4 d-none d-lg-block">
        <div class="card p-4 bg-light border-0">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i> Panduan Pengisian</h5>
            <p class="small text-muted mb-3" style="line-height: 1.6;">
                Halaman ini khusus digunakan untuk mencatat dan melacak surat yang <strong>dikirim ke luar lingkungan YPI Al Azhar</strong>.
            </p>
            <ul class="list-unstyled small text-muted">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Data surat akan langsung berstatus <strong>Selesai</strong>.</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Anda bisa memperbarui form <strong>Keterangan</strong> nanti apabila ada *update* atau tanggapan dari instansi tujuan.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
