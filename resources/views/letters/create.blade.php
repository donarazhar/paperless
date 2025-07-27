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

      {{-- Pilihan penerima --}}
      <div class="mb-3">
        <label class="form-label">Kirim Ke</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input"
                 type="radio"
                 name="recipient_type"
                 id="recUnit"
                 value="unit"
                 {{ old('recipient_type', 'unit') == 'unit' ? 'checked' : '' }}>
          <label class="form-check-label" for="recUnit">Unit</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input"
                 type="radio"
                 name="recipient_type"
                 id="recUser"
                 value="user"
                 {{ old('recipient_type') == 'user' ? 'checked' : '' }}>
          <label class="form-check-label" for="recUser">Pengguna</label>
        </div>
      </div>

      <div class="mb-3" id="selectUnit">
        <label class="form-label">Pilih Unit</label>
        <select name="to_unit_id" class="form-select">
          <option value="">– Pilih Unit –</option>
          @foreach($units as $unit)
          @if ($unit->name == 'Administrator')
            @continue
          @endif  
            <option value="{{ $unit->id }}"
              {{ old('to_unit_id') == $unit->id ? 'selected' : '' }}>
              {{ $unit->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3" id="selectUser">
        <label class="form-label">Pilih Pengguna</label>
        <select name="to_user_id" class="form-select">
          <option value="">– Pilih Pengguna –</option>
          @foreach(\App\Models\User::all() as $u)
            @if ($u->role == 'admin')
              @continue
            @endif 
            <option value="{{ $u->id }}"
              {{ old('to_user_id') == $u->id ? 'selected' : '' }}>
              {{ $u->name }} ({{ $u->email }})
            </option>
          @endforeach
        </select>
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

    {{-- Script kecil untuk toggle select --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const selUnit = document.getElementById('selectUnit');
      const selUser = document.getElementById('selectUser');
      function toggle() {
        if (document.getElementById('recUnit').checked) {
          selUnit.style.display = 'block';
          selUser.style.display = 'none';
        } else {
          selUnit.style.display = 'none';
          selUser.style.display = 'block';
        }
      }
      document.querySelectorAll('input[name="recipient_type"]')
              .forEach(el => el.addEventListener('change', toggle));
      toggle();
    });
    </script>
@endsection
