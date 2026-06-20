@extends('layouts.app')
@section('title', 'Buat Surat Eksternal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Input Surat Eksternal</h1>
    <a href="{{ route('letters.inboundExternal') }}" class="btn btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
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
            <form action="{{ route('letters.storeExternal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Field Pengirim Eksternal --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Nama Instansi Pengirim Eksternal</label>
                    <input type="text" 
                           name="external_sender_name" 
                           class="form-control form-control-lg bg-light" 
                           value="{{ old('external_sender_name') }}" 
                           placeholder="Contoh: Kementerian Pendidikan, PT ABC, dll."
                           required>
                </div>

                {{-- Field No Surat --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Nomor Surat dari Instansi</label>
                    <input type="text" 
                           name="letter_number" 
                           class="form-control form-control-lg bg-light" 
                           value="{{ old('letter_number') }}" 
                           placeholder="Ketik persis seperti nomor yang tertera pada fisik surat"
                           required>
                </div>

                {{-- Field Perihal --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Perihal / Judul Surat</label>
                    <input type="text" 
                           name="subject" 
                           class="form-control form-control-lg bg-light" 
                           value="{{ old('subject') }}" 
                           placeholder="Contoh: Undangan Sosialisasi Program Baru"
                           required>
                </div>

                {{-- Field Isi Surat --}}
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Keterangan / Ringkasan Isi</label>
                    <textarea name="body" 
                              class="form-control bg-light" 
                              rows="5" 
                              placeholder="Tuliskan keterangan singkat mengenai surat fisik yang dilampirkan..."
                              required>{{ old('body') }}</textarea>
                </div>

                {{-- Field File Lampiran --}}
                <div class="mb-4 p-4 border rounded bg-light border-dashed" style="border-style: dashed !important;">
                    <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-file-earmark-pdf me-2"></i>Unggah Scan Dokumen Surat</label>
                    <p class="text-muted small mb-3">Mohon pindai (scan) surat fisik asli dan unggah format PDF/DOC di sini.</p>
                    <input type="file" 
                           name="attachments[]" 
                           class="form-control" 
                           multiple 
                           required>
                </div>

                {{-- Pilihan Tindakan Eksekusi --}}
                <div class="mb-5 p-4 border rounded" style="background-color: #f8fafc;">
                    <label class="form-label fw-bold text-primary mb-3"><i class="bi bi-signpost-split-fill me-2"></i>Tindakan Lanjutan Surat Ini</label>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="action_type" id="actionArchive" value="archive" checked>
                        <label class="form-check-label fw-bold text-dark" for="actionArchive">
                            Simpan sebagai Arsip Unit (Langsung Selesai)
                        </label>
                        <div class="small text-muted mt-1">Pilih opsi ini jika surat dari luar sudah dieksekusi langsung oleh unit Anda dan hanya butuh diarsipkan.</div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="action_type" id="actionForward" value="forward">
                        <label class="form-check-label fw-bold text-dark" for="actionForward">
                            Teruskan ke Sekretariat YPIA untuk Disposisi
                        </label>
                        <div class="small text-muted mt-1">Pilih opsi ini jika surat dari luar membutuhkan pertimbangan, arahan, atau disposisi lintas unit melalui Sekretariat Yayasan.</div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                        Simpan Surat Eksternal <i class="bi bi-save2-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Info Bantuan Panel Kanan --}}
    <div class="col-lg-4 d-none d-lg-block">
        <div class="card p-4 bg-primary bg-opacity-10 border-0">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle-fill me-2"></i> Panduan Surat Eksternal</h5>
            <p class="small text-muted mb-3" style="line-height: 1.6;">
                Fitur ini dirancang bagi unit/cabang untuk mencatat masuknya surat yang berasal dari <strong>luar YPI Al Azhar</strong> (misal: Kementerian Agama, vendor pihak ketiga, instansi pemerintah lainnya).
            </p>
            <hr class="border-primary opacity-25">
            <h6 class="fw-bold text-dark small mb-2">Penjelasan Tindakan:</h6>
            <ul class="list-unstyled small text-muted">
                <li class="mb-3">
                    <strong class="text-dark d-block"><i class="bi bi-check-circle-fill text-success me-1"></i> Arsip Unit</strong>
                    Jika surat undangan/edaran dari luar hanya diperuntukkan bagi unit Anda dan tidak perlu sepengetahuan Sekretariat Pusat. Status langsung <em>Selesai</em>.
                </li>
                <li>
                    <strong class="text-dark d-block"><i class="bi bi-arrow-up-right-circle-fill text-primary me-1"></i> Teruskan ke YPIA</strong>
                    Jika surat tersebut membutuhkan arahan Kasubag/Pimpinan Sekretariat, atau meminta Sekretariat untuk mendisposisikannya ke unit lain yang lebih relevan.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
