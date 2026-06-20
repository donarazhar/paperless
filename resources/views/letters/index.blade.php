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
              
              <div class="table-responsive">
                  <table id="historyTable" class="table table-hover table-bordered align-middle w-100" style="font-size: 0.9rem;">
                      <thead class="table-light">
                          <tr>
                              <th>Waktu</th>
                              <th>Status / Aksi</th>
                              <th>Dilakukan Oleh</th>
                              <th>Catatan</th>
                          </tr>
                      </thead>
                      <tbody id="historyTableBody">
                          <!-- Konten riwayat akan dirender via JS -->
                      </tbody>
                  </table>
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

        // Variabel global untuk menyimpan instance DataTables pada modal
        var historyDataTable = null;

        // Event handler untuk tombol Lihat Disposisi (Gunakan event delegation karena DataTables memodifikasi DOM)
        $('#laporanTable').on('click', '.btn-lihat-disposisi', function() {
            var rawData = $(this).attr('data-disposisi');
            var noSurat = $(this).attr('data-nosurat');
            var disposisi = JSON.parse(rawData);
            
            $('#modalNoSurat').text(noSurat);
            
            // Hancurkan DataTable lama jika ada, sebelum memodifikasi isi tbody
            if (historyDataTable !== null) {
                historyDataTable.destroy();
                historyDataTable = null;
            }
            
            var html = '';
            if(disposisi.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat perjalanan untuk surat ini.</td></tr>';
            } else {
                disposisi.forEach(function(item) {
                    var catatan = item.catatan ? item.catatan.replace(/\n/g, '<br>') : '-';
                    html += `
                        <tr>
                            <td class="text-nowrap"><i class="bi bi-clock me-1 text-muted"></i> ${item.tanggal}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">${item.aksi}</span></td>
                            <td><i class="bi bi-person-fill text-muted me-1"></i> ${item.aktor}</td>
                            <td><div class="fst-italic text-wrap" style="max-width: 300px;">${catatan}</div></td>
                        </tr>
                    `;
                });
            }
            
            $('#historyTableBody').html(html);
            
            // Inisialisasi DataTables jika ada data
            if(disposisi.length > 0) {
                historyDataTable = $('#historyTable').DataTable({
                    language: {
                        "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                        "sProcessing":   "Sedang memproses...",
                        "sSearch":       "Cari Riwayat:"
                    },
                    order: [], // Tetap pertahankan urutan dari backend (Terbaru di atas)
                    paging: false, // Tampilkan semua baris tanpa paginasi
                    info: false // Sembunyikan teks "Menampilkan X entri" karena tidak ada paginasi
                });
            }
            
            // Tampilkan Modal
            var myModal = new bootstrap.Modal(document.getElementById('modalDisposisi'));
            myModal.show();
        });
    });
</script>
@endpush