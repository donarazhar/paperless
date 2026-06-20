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
                        <th>No Surat</th>
                        <th>No Agenda</th>
                        <th>Perihal</th>
                        <th>Pengirim</th>
                        <th>Tujuan</th>
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
                            <td class="fw-bold">{{ $letter->letter_number }}</td>
                            <td class="text-nowrap">
                                @if($letter->agenda_number)
                                    <span class="badge bg-secondary">{{ $letter->agenda_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Detail Surat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @php
                                        // Siapkan data khusus disposisi untuk JSON agar menampilkan Ditujukan Ke
                                        $dispoHistory = collect();
                                        foreach($letter->dispositions as $d) {
                                            $target = $d->toUser ? $d->toUser->name : ($d->unit ? 'Unit ' . $d->unit->name : '--');
                                            $actor = $d->fromUser ? $d->fromUser->name : 'Sistem';
                                            
                                            $dispoHistory->push([
                                                'sort_date' => $d->created_at->timestamp,
                                                'tanggal' => $d->created_at->format('d/m/y'),
                                                'aksi' => 'Disposisi <br><small class="text-muted fw-normal"><i class="bi bi-person-fill"></i> Oleh: ' . $actor . '</small>',
                                                'aktor' => $target,
                                                'catatan' => $d->note ?? '-',
                                            ]);

                                            if ($d->status !== 'pending') {
                                                $statusIndo = $d->status === 'accepted' ? 'Selesai' : ($d->status === 'pertimbangan' ? 'Memberi Pertimbangan' : ucfirst($d->status));
                                                $dispoHistory->push([
                                                    'sort_date' => $d->updated_at->timestamp,
                                                    'tanggal' => $d->updated_at->format('d/m/y'),
                                                    'aksi' => $statusIndo . ' <br><small class="text-muted fw-normal"><i class="bi bi-person-fill"></i> Oleh: ' . $target . '</small>',
                                                    'aktor' => $actor,
                                                    'catatan' => $d->response_note ?? '-',
                                                ]);
                                            }
                                        }
                                        $historiesList = $dispoHistory->sortBy('sort_date')->values()->toJson();
                                    @endphp
                                    
                                    @php
                                        $pengirimText = $letter->type === 'external' ? $letter->external_sender_name : ($letter->sender->name ?? 'Sistem');
                                    @endphp
                                    
                                    <button type="button" class="btn btn-sm btn-outline-info btn-lihat-disposisi" 
                                            data-disposisi="{{ $historiesList }}"
                                            data-nosurat="{{ $letter->letter_number }}"
                                            data-agenda="{{ $letter->agenda_number ?? '-' }}"
                                            data-perihal="{{ $letter->subject }}"
                                            data-pengirim="{{ $pengirimText }}"
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
          <div class="modal-body p-4">
              <!-- Info Surat Section -->
              <div class="alert alert-light border border-secondary-subtle mb-4">
                  <div class="row gx-3 gy-2">
                      <div class="col-12">
                          <span class="text-muted small text-uppercase fw-semibold">Perihal Surat</span>
                          <div class="fw-bold text-dark" id="modalPerihal"></div>
                      </div>
                      <div class="col-md-6 mt-3">
                          <span class="text-muted small text-uppercase fw-semibold">Pengirim</span>
                          <div class="fw-medium text-dark" id="modalPengirim"></div>
                      </div>
                      <div class="col-md-6 mt-3">
                          <span class="text-muted small text-uppercase fw-semibold">No. Agenda</span>
                          <div><span class="badge bg-secondary" id="modalAgenda"></span></div>
                      </div>
                  </div>
              </div>
              
              <div class="table-responsive">
                  <table id="historyTable" class="table table-hover table-bordered align-middle w-100" style="font-size: 0.9rem;">
                      <thead class="table-light">
                          <tr>
                              <th style="width: 15%;">Waktu</th>
                              <th style="width: 20%;">Status / Aksi</th>
                              <th style="width: 20%;">Ditujukan Ke</th>
                              <th style="width: 45%;">Catatan</th>
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
            var agenda = $(this).attr('data-agenda');
            var perihal = $(this).attr('data-perihal');
            var pengirim = $(this).attr('data-pengirim');
            var disposisi = JSON.parse(rawData);
            
            $('#modalDisposisiLabel').text('Lacak Perjalanan: ' + noSurat);
            $('#modalPerihal').text(perihal);
            $('#modalPengirim').text(pengirim);
            if (agenda && agenda !== '-') {
                $('#modalAgenda').text(agenda).removeClass('bg-secondary text-muted').addClass('bg-primary');
            } else {
                $('#modalAgenda').text('Belum diagendakan').removeClass('bg-primary').addClass('bg-secondary text-muted');
            }
            
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
                            <td class="text-nowrap"><i class="bi bi-calendar3 me-1 text-muted"></i> ${item.tanggal}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle text-start lh-base">${item.aksi}</span></td>
                            <td>${item.aktor}</td>
                            <td><div class="fst-italic text-wrap text-break">${catatan}</div></td>
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
                    info: false, // Sembunyikan teks "Menampilkan X entri" karena tidak ada paginasi
                    searching: false, // Sembunyikan kotak pencarian
                    ordering: false // Nonaktifkan fitur klik sorting pada judul kolom
                });
            }
            
            // Tampilkan Modal
            var myModal = new bootstrap.Modal(document.getElementById('modalDisposisi'));
            myModal.show();
        });
    });
</script>
@endpush