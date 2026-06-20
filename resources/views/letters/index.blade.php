@extends('layouts.app')
@section('title', 'Laporan Surat')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 !important;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Laporan Surat</h1>
    </div>

    {{-- Form Filter Tanggal & Status (Backend Filter) --}}
    <div class="card p-4 mb-4 border-0 shadow-sm">
        <form class="row gy-3 gx-3 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Jenis</label>
                <select name="type" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="internal" @selected(request('type') == 'internal')>Internal</option>
                    <option value="external" @selected(request('type') == 'external')>Masuk Eksternal</option>
                    <option value="outbound_external" @selected(request('type') == 'outbound_external')>Keluar Eksternal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary fw-bold w-100"><i class="bi bi-funnel me-1"></i> Filter Laporan</button>
                <a href="{{ request()->url() }}" class="btn btn-light border w-100 mt-2 fw-bold"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card p-4 border-0 shadow-sm">
        <div class="table-responsive">
            <table id="laporanTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Tgl Dibuat</th>
                        <th>Jenis</th>
                        <th>No Surat</th>
                        <th>Perihal</th>
                        <th>Pengirim</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($letters as $letter)
                        <tr>
                            <td>
                                <span class="d-none">{{ $letter->created_at->format('Y-m-d H:i:s') }}</span>
                                {{ $letter->created_at->locale('id')->isoFormat('D MMM YYYY') }}
                            </td>
                            <td>
                                @if($letter->type == 'internal')
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Internal</span>
                                @elseif($letter->type == 'external')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">M. Eksternal</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">K. Eksternal</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $letter->letter_number }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $letter->subject }}">
                                    {{ $letter->subject }}
                                </div>
                            </td>
                            <td>
                                @if($letter->type === 'external')
                                    {{ $letter->external_sender_name }}
                                @else
                                    {{ $letter->sender->name ?? 'Sistem' }}
                                @endif
                            </td>
                            <td>
                                @if($letter->type === 'outbound_external')
                                    {{ $letter->external_recipient_name }}
                                @elseif($letter->recipientUser)
                                    {{ $letter->recipientUser->name }}
                                @else
                                    Unit {{ $letter->recipientUnit->name ?? '--' }}
                                @endif
                            </td>
                            <td class="text-capitalize">
                                <span class="badge bg-success">{{ str_replace('_', ' ', $letter->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Detail Surat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @php
                                        // Siapkan data riwayat perjalanan surat (histories) untuk JSON
                                        $historiesList = $letter->histories->map(function($h) {
                                            $actor = $h->user ? $h->user->name . ' (Unit ' . ($h->user->unit->name ?? '-') . ')' : 'Sistem';
                                            return [
                                                'tanggal' => $h->created_at->locale('id')->isoFormat('D MMM YYYY HH:mm'),
                                                'aksi' => ucfirst(str_replace('_', ' ', $h->action)),
                                                'aktor' => $actor,
                                                'catatan' => $h->note,
                                            ];
                                        })->toJson();
                                    @endphp
                                    
                                    <button type="button" class="btn btn-sm btn-outline-info btn-lihat-disposisi" 
                                            data-disposisi="{{ $historiesList }}"
                                            data-nosurat="{{ $letter->letter_number }}"
                                            title="Lihat Disposisi">
                                        <i class="bi bi-sign-turn-right-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Disposisi --}}
    <div class="modal fade" id="modalDisposisi" tabindex="-1" aria-labelledby="modalDisposisiLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold" id="modalDisposisiLabel">Lacak Perjalanan Surat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="alert alert-primary py-2 mb-4">
                  <strong>No Surat:</strong> <span id="modalNoSurat"></span>
              </div>
              
              <div id="disposisiContainer">
                  <!-- Konten disposisi akan dirender via JS -->
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#laporanTable').DataTable({
            language: {
                "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                "sProcessing":   "Sedang memproses...",
                "sLengthMenu":   "Tampilkan _MENU_ entri",
                "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sInfoPostFix":  "",
                "sSearch":       "Cari:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext":     "Selanjutnya",
                    "sLast":     "Terakhir"
                }
            },
            order: [[0, 'desc']], // Urutkan berdasarkan kolom pertama (Tgl Dibuat) menurun
            pageLength: 25
        });

        // Event handler untuk tombol Lihat Disposisi (Gunakan event delegation karena DataTables memodifikasi DOM)
        $('#laporanTable').on('click', '.btn-lihat-disposisi', function() {
            var rawData = $(this).attr('data-disposisi');
            var noSurat = $(this).attr('data-nosurat');
            var disposisi = JSON.parse(rawData);
            
            $('#modalNoSurat').text(noSurat);
            
            var html = '';
            if(disposisi.length === 0) {
                html = '<div class="text-center text-muted my-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada riwayat disposisi untuk surat ini.</div>';
            } else {
                html += '<div class="timeline-container">';
                disposisi.forEach(function(item, index) {
                    html += `
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                    <span class="badge bg-primary">${item.aksi}</span>
                                    <span class="small text-muted"><i class="bi bi-clock me-1"></i> ${item.tanggal}</span>
                                </div>
                                <div class="mt-2">
                                    <div class="small text-muted mb-1"><i class="bi bi-person-fill"></i> Oleh:</div>
                                    <div class="fw-bold text-dark">${item.aktor}</div>
                                </div>
                                ${item.catatan ? `
                                <div class="mt-3 p-3 bg-white rounded border">
                                    <div class="small text-muted mb-1 fw-bold">Catatan:</div>
                                    <div class="fst-italic">"${item.catatan.replace(/\n/g, '<br>')}"</div>
                                </div>` : ''}
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            $('#disposisiContainer').html(html);
            
            // Tampilkan Modal
            var myModal = new bootstrap.Modal(document.getElementById('modalDisposisi'));
            myModal.show();
        });
    });
</script>
@endpush