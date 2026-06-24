@extends('layouts.mailbox')
@section('title', 'Edit Surat')
@section('content')
    <h1>Edit Surat</h1>

    <form action="{{ route('letters.update', $letter) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Jenis Surat</label>
            <select name="type" class="form-select" required>
                <option value="inbound" {{ $letter->type == 'inbound' ? 'selected' : '' }}>Masuk</option>
                <option value="outbound" {{ $letter->type == 'outbound' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor Surat</label>
            <input type="text" name="letter_number" class="form-control" value="{{ $letter->letter_number }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Perihal</label>
            <input type="text" name="subject" class="form-control" value="{{ $letter->subject }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Isi Surat</label>
            <textarea name="body" class="form-control" rows="5" required>{{ $letter->body }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lampiran Baru (opsional)</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button class="btn btn-primary">Update Surat</button>
        <a href="{{ route('letters.show', $letter) }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection