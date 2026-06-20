@extends('layouts.app')
@section('title', 'Laporan Surat')

@section('content')
<style>
    .filter-bar { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.1rem 1.35rem;margin-bottom:1.25rem; }
    .filter-bar .f-label { font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;margin-bottom:0.35rem;display:block; }
    .filter-bar .form-control,.filter-bar .form-select { height:40px;font-size:0.865rem;border-radius:0.6rem;border:1.5px solid #e8edf4;background:#fafbfd;padding:0 0.9rem; }
    .filter-bar .form-control:focus,.filter-bar .form-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.09)!important; }

    /* Table */
    .rpt-table { width:100%;border-collapse:separate;border-spacing:0; }
    .rpt-table thead th { font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;padding:0.65rem 0.85rem;border-bottom:1px solid #e8edf4;white-space:nowrap;background:#fafbfd; }
    .rpt-table thead th:first-child { border-radius:0.75rem 0 0 0;padding-left:1.25rem; }
    .rpt-table thead th:last-child  { border-radius:0 0.75rem 0 0;padding-right:1.25rem; }
    .rpt-table tbody tr { transition:background .12s; }
    .rpt-table tbody tr:hover td { background:#f8faff; }
    .rpt-table tbody td { padding:0.85rem 0.85rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:0.855rem;color:#334155; }
    .rpt-table tbody td:first-child { padding-left:1.25rem; }
    .rpt-table tbody td:last-child  { padding-right:1.25rem; }
    .rpt-table tbody tr:last-child td { border-bottom:none; }

    .subject-cell .s-title { font-size:0.875rem;font-weight:700;color:#0f172a; }
    .subject-cell .s-num   { font-size:0.7rem;font-weight:600;color:#94a3b8;margin-top:2px; }
    .person-cell .p-name   { font-weight:600;color:#0f172a;font-size:0.845rem; }
    .person-cell .p-role   { font-size:0.72rem;color:#94a3b8;margin-top:1px; }
    .date-cell .d-date     { font-weight:600;font-size:0.835rem;color:#334155; }

    .agenda-pill { display:inline-flex;align-items:center;gap:4px;background:#dbeafe;color:#1d4ed8;font-size:0.68rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:100px; }

    /* Action buttons */
    .btn-act { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.85rem;border:none;cursor:pointer;transition:background .15s,color .15s;text-decoration:none; }
    .btn-act-view { background:#eff6ff;color:#2563eb; }
    .btn-act-view:hover { background:#dbeafe;color:#1d4ed8; }
    .btn-act-disp { background:#fdf4ff;color:#7e22ce; }
    .btn-act-disp:hover { background:#ede9fe;color:#6b21a8; }

    /* DataTables overrides */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input { height:36px;border-radius:0.5rem;border:1.5px solid #e8edf4;font-size:0.85rem;padding:0 0.75rem; }
    .dataTables_wrapper .dataTables_filter input:focus { outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.09); }
    .dataTables_wrapper .dataTables_info { font-size:0.8rem;color:#94a3b8; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding:0!important;margin:0!important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a:hover { background:#2563eb!important;color:#fff!important;border-color:#2563eb!important;border-radius:0.4rem!important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button a { border-radius:0.4rem!important;font-size:0.82rem; }
    .dataTables_top { display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem; }
    .dataTables_top .dataTables_length { margin:0; }
    .dataTables_top .dataTables_filter { margin:0; }
    .dataTables_bottom { display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-top:1rem; }

    /* Modal */
    .modal-content { border:none;border-radius:1.1rem;box-shadow:0 20px 60px rgba(15,23,42,0.15); }
    .modal-header { border-bottom:1px solid #e8edf4;padding:1.1rem 1.4rem;border-radius:1.1rem 1.1rem 0 0; }
    .modal-footer { border-top:1px solid #e8edf4;padding:0.9rem 1.4rem; }
    .modal-body { padding:1.25rem 1.4rem; }

    .info-card { background:#f8faff;border:1px solid #e8edf4;border-radius:0.75rem;padding:1rem 1.1rem;margin-bottom:1.25rem; }
    .info-card .ic-label { font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:3px; }
    .info-card .ic-val   { font-size:0.875rem;font-weight:700;color:#0f172a; }

    .tl-modal { padding-left:1.5rem;position:relative; }
    .tl-modal::before { content:'';position:absolute;left:7px;top:4px;bottom:4px;width:2px;background:#e8edf4; }
    .tl-item { position:relative;margin-bottom:1rem; }
    .tl-item::before { content:'';position:absolute;left:-1.5rem;top:5px;width:14px;height:14px;border-radius:50%;background:#fff;border:2px solid #2563eb; }
    .tl-body { background:#fff;border:1px solid #e8edf4;border-radius:0.65rem;padding:0.75rem 0.9rem; }
    .tl-action { font-size:0.8rem;font-weight:700;color:#0f172a; }
    .tl-date   { font-size:0.7rem;color:#94a3b8; }
    .tl-note   { font-size:0.78rem;color:#64748b;margin-top:4px;font-style:italic; }
    .tl-by     { font-size:0.72rem;color:#94a3b8;margin-top:4px;display:flex;align-items:center;gap:4px; }

    /* Mobile cards */
    .rpt-card { background:#fff;border:1px solid #e8edf4;border-radius:0.9rem;padding:1rem 1.1rem;margin-bottom:0.65rem; }
    .rpt-card .rc-no { font-size:0.68rem;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#94a3b8;margin-bottom:3px; }
    .rpt-card .rc-sub { font-size:0.875rem;font-weight:700;color:#0f172a;margin-bottom:0.35rem; }
    .rpt-card .rc-meta { font-size:0.75rem;color:#64748b;display:flex;flex-wrap:wrap;gap:0.6rem; }

    .empty-state { text-align:center;padding:4rem 1rem;color:#94a3b8; }
    .empty-state i { font-size:3rem;display:block;margin-bottom:0.75rem;color:#cbd5e1; }

    .table-wrap { display:block; }
    .cards-wrap { display:none; }
    @media(max-width:900px) { .table-wrap{display:none;} .cards-wrap{display:block;} }
    @media(max-width:600px) { .filter-bar{padding:0.9rem 1rem;} }
</style>

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">Laporan Surat</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Rekap seluruh surat beserta riwayat perjalanannya</p>
    </div>
    <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.78rem;padding:0.45rem 0.9rem;border-radius:100px;">
        <i class="bi bi-bar-chart-line-fill me-1"></i>{{ count($letters) }} data
    </span>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-sm-4 col-md-4">
            <label class="f-label">Pencarian</label>
            <input type="text" name="search" class="form-control" placeholder="Cari perihal/nomor..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3">
            <label class="f-label">Cabang</label>
            <select name="branch_id" class="form-select">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-sm-4 col-md-3">
            <label class="f-label">Unit Kerja</label>
            <select name="unit_id" class="form-select">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" @selected(request('unit_id')==$u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-sm-auto d-flex gap-2 align-items-end">
            <button class="btn btn-primary" style="height:40px;border-radius:0.6rem;font-size:0.85rem;padding:0 1rem;">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <a href="{{ request()->url() }}" class="btn btn-light border" style="height:40px;border-radius:0.6rem;font-size:0.85rem;padding:0 0.9rem;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;padding:1rem 0.25rem 0.75rem;">
    <div class="dataTables_top px-3">
        <div id="dtLength"></div>
        <div id="dtSearch"></div>
    </div>
    <table id="laporanTable" class="rpt-table w-100">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">No.</th>
                <th style="width:100px;">Tanggal</th>
                <th style="width:90px;">No Agenda</th>
                <th>Perihal Surat</th>
                <th style="width:150px;">Pengirim</th>
                <th style="width:150px;">Tujuan</th>
                <th style="width:90px;text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
                @php
                    $pengirimText = $letter->type === 'external' ? $letter->external_sender_name : ($letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem'));
                    $tujuanText   = $letter->type === 'outbound_external' ? $letter->external_recipient_name : ($letter->recipientUser ? $letter->recipientUser->name : ($letter->recipientUnit->name ?? '--'));
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>
                        <div class="date-cell">
                            <div class="d-date">{{ $letter->created_at->format('d/m/Y') }}</div>
                        </div>
                        <span class="d-none">{{ $letter->created_at->format('Y-m-d') }}</span>
                    </td>
                    <td>
                        @if($letter->agenda_number)
                            <span class="agenda-pill"><i class="bi bi-hash"></i>{{ $letter->agenda_number }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title" title="{{ $letter->subject }}">{{ $letter->subject }}</div>
                            <div class="s-num">{{ $letter->letter_number ?: '—' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="person-cell">
                            <div class="p-name" title="{{ $pengirimText }}">{{ $pengirimText }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="person-cell">
                            <div class="p-name" title="{{ $tujuanText }}">{{ $tujuanText }}</div>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('letters.printDisposition', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" target="_blank"
                               class="btn-act btn-act-disp"
                               title="Lacak Perjalanan (Print)">
                                <i class="bi bi-sign-turn-right-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="dataTables_bottom px-3">
        <div id="dtInfo"></div>
        <div id="dtPaginate"></div>
    </div>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($letters as $letter)
        @php
            $pengirimText = $letter->type === 'external' ? $letter->external_sender_name : ($letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem'));
        @endphp
        <div class="rpt-card">
            <div class="rc-no">{{ $letter->letter_number ?: '— Belum bernomor' }}</div>
            <div class="rc-sub">{{ $letter->subject }}</div>
            <div class="rc-meta mb-2">
                <span><i class="bi bi-person-fill"></i> {{ $pengirimText }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
                @if($letter->agenda_number)
                    <span><i class="bi bi-hash"></i> {{ $letter->agenda_number }}</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('letters.printDisposition', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;background:#fdf4ff;color:#7e22ce;border:none;border-radius:0.5rem;font-size:0.78rem;font-weight:700;padding:0.4rem 0.85rem;text-decoration:none;cursor:pointer;">
                    <i class="bi bi-printer-fill"></i> Cetak Lacak
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-bar-chart-line"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada data laporan</p>
        </div>
    @endforelse
</div>

@push('scripts')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var dt = $('#laporanTable').DataTable({
        language: {
            sLengthMenu:'Tampilkan _MENU_ entri', sSearch:'',
            sZeroRecords:'Tidak ditemukan data', sInfo:'_START_–_END_ dari _TOTAL_',
            sInfoEmpty:'0 entri', sInfoFiltered:'(dari _MAX_)',
            oPaginate:{ sPrevious:'‹', sNext:'›' }
        },
        order:[], pageLength:25,
        dom: 'lrtip',
        initComplete: function() {
            var api = this.api();
            $('#dtLength').html($('.dataTables_length').detach());
            $('#dtSearch').html('<div class="dataTables_filter"><input type="search" placeholder="Cari surat…" id="dtSearchInput" style="height:36px;border-radius:0.5rem;border:1.5px solid #e8edf4;font-size:0.85rem;padding:0 0.75rem;outline:none;"></div>');
            $('#dtInfo').html($('.dataTables_info').detach());
            $('#dtPaginate').html($('.dataTables_paginate').detach());
            $('#dtSearchInput').on('keyup', function(){ api.search(this.value).draw(); });
        }
    });
});
</script>
@endpush

@endsection