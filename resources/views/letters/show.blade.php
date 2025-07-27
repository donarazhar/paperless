@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
        @php
    // Mapping role ke label
    $roleLabels = [
        'admin' => 'Administrator',
        'staff' => 'Staff',
        'manager' => 'Manajer',
    ];
    $user = Auth::user();
    // Disposisi yang diterima oleh current user/unit
    $dispRecv = $letter->dispositions
        ->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    // Semua disposisi yang dikirim oleh current user (manager)
    $dispSent = $letter->dispositions
        ->filter(fn($d) => $d->from_user_id === $user->id);
    // Untuk admin
    $allDisp = $letter->dispositions;
    $isAdmin = Auth::user()->role === 'admin';
          @endphp

        <h1 class="mb-4">Detail Surat</h1>

        {{-- 1) CARD DISPOSISI PENERIMA --}}
        @if($dispRecv)
            <div class="card border-warning mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-arrow-repeat"></i> Disposisi
                </div>
                <div class="card-body">
                    <p>
                        <strong>Dari:</strong>
                        {{ $dispRecv->fromUser->name }}
                        ({{ $roleLabels[$dispRecv->fromUser->role] ?? ucfirst($dispRecv->fromUser->role) }}
                        – {{ $dispRecv->fromUser->unit->name }})
                    </p>
                    <p><strong>Catatan:</strong> {{ $dispRecv->note }}</p>

                    @if($dispRecv->status === 'pending')
                        <div class="mt-3">
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#acceptModal">
                                <i class="bi bi-check-circle"></i> Terima
                            </button>
                            <button class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#followupModal">
                                <i class="bi bi-arrow-clockwise"></i> Follow‐up
                            </button>
                        </div>
                    @else
                        <div class="mt-2">
                            <span
                                class="badge bg-{{ $dispRecv->status === 'accepted' ? 'success' : ($dispRecv->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($dispRecv->status) }}
                            </span>
                            @if($dispRecv->response_note)
                                <div
                                    class="mt-2 p-3 border rounded 
                                                                                                                                {{ $dispRecv->status === 'accepted' ? 'border-success' : ($dispRecv->status === 'rejected' ? 'border-danger' : 'border-warning') }}">
                                    <small class="text-muted">Catatan Respon:</small>
                                    <blockquote class="mb-0">{{ $dispRecv->response_note }}</blockquote>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 2) RESPON DISPOSISI ANDA (untuk pengirim) --}}
        @if($dispSent->isNotEmpty())
            <div class="card mb-4">
                @php
        $first = $dispSent->first();
        $hdrClass = $first->status === 'accepted'
            ? 'bg-success text-white'
            : ($first->status === 'rejected'
                ? 'bg-danger text-white'
                : 'bg-warning text-dark');
                @endphp
                <div class="card-header {{ $hdrClass }}">
                    <i class="bi bi-receipt"></i> Respon Disposisi Anda
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">

                        @foreach($dispSent as $d)
                            <li class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>Ke:
                                            @if($d->to_unit_id)
                                                Unit {{ $d->unit->name }}
                                            @else
                                                {{ $d->toUser->name }}
                                                ({{ $roleLabels[$d->toUser->role] }} – {{ $d->toUser->unit->name }})
                                            @endif
                                        </strong>
                                    </div>
                                    <div>
                                        <span class="badge 
                                                                                              {{ $d->status === 'accepted' ? 'bg-success'
                : ($d->status === 'rejected' ? 'bg-danger'
                    : 'bg-warning text-dark') }}">
                                            {{ ucfirst($d->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Tanggal Respon --}}
                                <small class="text-muted mb-2 d-block">
                                    {{ $d->updated_at->locale('id')->isoFormat('D MMMM YYYY HH:mm') }}
                                </small>

                                {{-- Catatan Disposisi Asli --}}
                                <p class="mb-1">
                                    <strong>Catatan Disposisi:</strong> {{ $d->note }}
                                </p>

                                {{-- Catatan Respon dengan Nama Responden --}}
                                @if($d->status !== 'pending')
                                    <div class="mt-2 p-3 border rounded 
                                                                                                                  {{ $d->status === 'accepted' ? 'border-success'
                    : ($d->status === 'rejected' ? 'border-danger'
                        : 'border-warning') }}">
                                        <small class="text-muted">Catatan Respon:</small>
                                        <blockquote class="mb-1">{{ $d->response_note }}</blockquote>

                                        {{-- Siapa yang merespon --}}
                                        <small class="text-muted">
                                            <i class="bi bi-person-circle me-1"></i>
                                            @if($d->to_user_id)
                                                {{ $d->toUser->name }}
                                                ({{ $roleLabels[$d->toUser->role] }} – {{ $d->toUser->unit->name }})
                                            @else
                                                Oleh Manajer/Atasan Unit {{ $d->unit->name }}
                                            @endif
                                        </small>
                                    </div>
                                @endif

                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        @endif

        {{-- 3) DAFTAR DISPOSISI (khusus Admin) --}}
        @if($isAdmin && $allDisp->isNotEmpty())
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-list-ul"></i> Daftar Disposisi (Admin)
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($allDisp as $d)
                            <li class="mb-3">
                                <p class="mb-1">
                                    <strong>Dari:</strong>
                                    {{ $d->fromUser->name }}
                                    ({{ $roleLabels[$d->fromUser->role] }} – {{ $d->fromUser->unit->name }})
                                </p>
                                <p class="mb-1">
                                    <strong>Ke:</strong>
                                    @if($d->to_unit_id)
                                        Unit {{ $d->unit->name }}
                                    @else
                                        {{ $d->toUser->name }}
                                        ({{ $roleLabels[$d->toUser->role] }} – {{ $d->toUser->unit->name }})
                                    @endif
                                </p>
                                <p class="mb-1"><strong>Catatan Disposisi:</strong> {{ $d->note }}</p>
                                <p class="mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge 
                                                                    {{ $d->status === 'accepted' ? 'bg-success'
                : ($d->status === 'rejected' ? 'bg-danger'
                    : 'bg-warning text-dark') }}">
                                        {{ ucfirst($d->status) }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        {{ $d->updated_at->locale('id')->isoFormat('D MMM YYYY HH:mm') }}
                                    </small>
                                </p>
                                @if($d->response_note)
                                    <div class="ps-3 border-start border-3 
                                                                                  {{ $d->status === 'accepted' ? 'border-success'
                    : ($d->status === 'rejected' ? 'border-danger'
                        : 'border-warning') }}">
                                        <p class="mb-1"><strong>Catatan Respon:</strong></p>
                                        <blockquote class="mb-1">{{ $d->response_note }}</blockquote>
                                        <small class="text-muted">
                                            Oleh:
                                            @if($d->to_user_id)
                                                {{ $d->toUser->name }}
                                                ({{ $roleLabels[$d->toUser->role] }} – {{ $d->toUser->unit->name }})
                                            @else
                                                Anggota Unit {{ $d->unit->name }}
                                            @endif
                                        </small>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- 4) DETAIL SURAT --}}
        <p class="text-muted mb-2">
            <i class="bi bi-calendar-event"></i>
            {{ $letter->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}
        </p>
        <div class="card mb-4">
            <div class="card-body">
                <p>
                    <strong>No Surat:</strong> {{ $letter->letter_number }}
                </p>
                <p>
                    <strong>Jenis:</strong> {{ ucfirst($letter->type) }}
                </p>
                <p>
                    <strong>Perihal:</strong> {{ $letter->subject }}
                </p>
                <p>
                    <strong>Isi:</strong><br>{!! nl2br(e($letter->body)) !!}
                </p>
                <p>
                    <strong>Dari:</strong>
                    {{ $letter->sender->name }}
                    ({{ $roleLabels[$letter->sender->role] ?? ucfirst($letter->sender->role) }}
                    – {{ $letter->sender->unit->name }})
                </p>
                <p>
                    <strong>Tujuan:</strong>
                    @if($letter->recipientUser)
                        {{ $letter->recipientUser->name }}
                        ({{ $roleLabels[$letter->recipientUser->role] ?? ucfirst($letter->recipientUser->role) }}
                        – {{ $letter->recipientUser->unit->name }})
                    @else
                        Unit {{ $letter->recipientUnit->name }}
                    @endif
                </p>
                <p>
                    <strong>Status:</strong>
                    <span class="text-capitalize">{{ $letter->status }}</span>
                </p>
            </div>
        </div>

        {{-- 5) LAMPIRAN --}}
        @if($letter->attachments->count())
            <div class="mb-4">
                <h5><i class="bi bi-paperclip"></i> Lampiran</h5>
                <ul class="list-unstyled">
                    @foreach($letter->attachments as $att)
                        @php
            $url = Storage::url($att->file_path);
            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
            $name = basename($att->file_path);
                          @endphp
                        <li class="mb-1">
                            @if($ext === 'pdf')
                                <button class="btn btn-link p-0 view-pdf" data-src="{{ $url }}">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i> {{ $name }}
                                </button>
                            @else
                                <a href="{{ $url }}" download class="btn btn-link p-0">
                                    <i class="bi bi-file-earmark-fill text-secondary"></i> {{ $name }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 6) AKSI & NAV --}}
        {{-- Aksi: Tandai Dibaca, Disposisi, Kembali --}}
        <div class="d-flex gap-2 mb-4">
            @if($letter->status === 'sent' && in_array($user->role, ['staff', 'manager']))
                <form action="{{ route('letters.markRead', $letter) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success">
                        <i class="bi bi-eye-fill"></i> Tandai Dibaca
                    </button>
                </form>
            @endif

            @if($user->role === 'manager' && !$dispRecv)
                <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#newDispoForm">
                    <i class="bi bi-arrow-repeat"></i> Disposisi
                </button>
            @endif

            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Form Disposisi (hanya manager & belum dispo) --}}
        @if($user->role === 'manager' && !$dispRecv)
            <div class="collapse mb-4" id="newDispoForm">
                <div class="card card-body">
                    <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                        @csrf

                        {{-- Pilih Target Type --}}
                        <div class="mb-3">
                            <label class="form-label">Kirim Ke</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit"
                                    checked>
                                <label class="form-check-label" for="typeUnit">Unit</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                                <label class="form-check-label" for="typeUser">Pengguna</label>
                            </div>
                        </div>

                        {{-- Select Unit --}}
                        <div class="mb-3" id="selectUnit">
                            <label class="form-label">Pilih Unit</label>
                            <select name="to_unit_id" class="form-select">
                                <option value="">— Pilih Unit —</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    @if ($unit->name == 'Administrator')
                                        @continue
                                    @endif 
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select User --}}
                        <div class="mb-3" id="selectUser" style="display:none;">
                            <label class="form-label">Pilih Pengguna</label>
                            <select name="to_user_id" class="form-select">
                                <option value="">— Pilih Pengguna —</option>
                                @foreach(\App\Models\User::where('unit_id', $user->unit_id)->get() as $u)
                                    @if ($u->role == 'admin')
                                        @continue
                                    @endif
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill"></i> Kirim Disposisi
                        </button>
                    </form>
                </div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const selUnit = document.getElementById('selectUnit');
                        const selUser = document.getElementById('selectUser');

                        document.getElementsByName('recipient_type').forEach(radio => {
                            radio.addEventListener('change', () => {
                                if (document.getElementById('typeUser').checked) {
                                    selUnit.style.display = 'none';
                                    selUser.style.display = 'block';
                                } else {
                                    selUnit.style.display = 'block';
                                    selUser.style.display = 'none';
                                }
                            });
                        });
                    });
                </script>
            @endpush
        @endif

        {{-- 7) HISTORY --}}
        <h3 class="mb-3"><i class="bi bi-clock-history"></i> History Surat</h3>
        @if($letter->histories->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada riwayat aksi untuk surat ini.
            </div>
        @else
            <ul class="list-unstyled">
                @foreach($letter->histories as $h)
                    @php
            $icons = [
                'sent' => 'bi-send-fill text-success',
                'draft' => 'bi-save-fill text-secondary',
                'read' => 'bi-eye-fill text-primary',
                'disposed' => 'bi-arrow-repeat text-warning',
                'disposition_accepted' => 'bi-check-circle-fill text-success',
                'disposition_rejected' => 'bi-x-circle-fill text-danger',
                'disposition_followup' => 'bi-arrow-clockwise text-warning',
            ];
            $icon = $icons[$h->action] ?? 'bi-info-circle';
                    @endphp
                    <li class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-light border rounded-circle p-3">
                                <i class="bi {{ $icon }} fs-5"></i>
                            </span>
                        </div>
                        <div>
                            <p class="mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $h->action)) }}</strong>
                                @if($h->note)
                                    – “{{ $h->note }}”
                                @endif
                            </p>
                            <small class="text-muted">
                                oleh
                                @if($h->user)
                                    {{ $h->user->name }}
                                    ({{ $roleLabels[$h->user->role] ?? ucfirst($h->user->role) }}
                                    – {{ $h->user->unit->name }})
                                @else
                                    System
                                @endif
                                , {{ $h->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}
                            </small>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- 8) MODAL PDF --}}
        <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="pdfFrame" style="width:100%;height:90vh;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>

        {{-- 9) MODAL RESPOND --}}
        @if($dispRecv)
            {{-- Accept --}}
            <div class="modal fade" id="acceptModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="accepted">
                            <div class="modal-header">
                                <h5 class="modal-title">Terima Disposisi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda yakin ingin menerima disposisi ini?</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Terima</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Reject --}}
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="rejected">
                            <div class="modal-header">
                                <h5 class="modal-title">Tolak Disposisi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Alasan Penolakan</label>
                                    <textarea name="response_note" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Follow-up --}}
            <div class="modal fade" id="followupModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="followup">
                            <div class="modal-header">
                                <h5 class="modal-title">Tindak Lanjut Disposisi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Catatan Tindak Lanjut</label>
                                    <textarea name="response_note" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">Kirim Follow-up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // PDF Preview
            const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            document.querySelectorAll('.view-pdf').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('pdfFrame').src = btn.dataset.src;
                    pdfModal.show();
                });
            });
            document.getElementById('pdfModal')
                .addEventListener('hidden.bs.modal', () => document.getElementById('pdfFrame').src = '');
        });
    </script>
@endpush