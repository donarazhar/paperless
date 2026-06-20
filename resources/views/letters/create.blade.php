@extends('layouts.app')
@section('title', 'Buat Surat')

@section('content')
    <h1 class="mb-4">Buat Surat Baru</h1>

    {{-- Tampilkan error validasi --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($errors->has('attachments'))
        <div class="alert alert-danger">
            {{$errors->first('attachments')}}
        </div>
    @endif
    
    <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="mb-3">
        <label class="form-label">No Surat (otomatis saat kirim)</label>
        <input type="text"
               name="letter_number"
               class="form-control"
               value="{{ old('letter_number') }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Perihal</label>
        <input type="text"
               name="subject"
               class="form-control"
               value="{{ old('subject') }}"
               required>
      </div>

      <div class="mb-3">
        <label class="form-label">Isi Surat</label>
        <textarea name="body"
                  class="form-control"
                  rows="5"
                  required>{{ old('body') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Lampiran (PDF/DOCX max 5MB)</label>
        <input type="file"
               name="attachments[]"
               class="form-control"
               multiple
               required>
      </div>

      <div class="d-flex gap-2">
        <button type="submit"
                name="action"
                value="draft"
                class="btn btn-secondary">
          <i class="bi bi-save"></i> Simpan Draft
        </button>
        <button type="submit"
                name="action"
                value="send"
                class="btn btn-primary">
          <i class="bi bi-send"></i> Kirim Surat
        </button>
        <a href="{{ route('letters.inbound') }}" class="btn btn-link">Batal</a>
      </div>
    </form>
@endsection
