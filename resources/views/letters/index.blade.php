@extends('layouts.mailbox')
@section('title', 'Laporan Surat')

@section('content')
<style>
    .agenda-pill { display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#1a73e8;font-size:0.68rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:100px; }

    /* Action buttons */
    .btn-act { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.85rem;border:none;cursor:pointer;transition:background .15s,color .15s;text-decoration:none; }
    .btn-act-view { background:#e8f0fe;color:#1a73e8; }
    .btn-act-view:hover { background:#bfdbfe;color:#1557b0; }
    .btn-act-disp { background:#f3e8fd;color:#7e22ce; }
    .btn-act-disp:hover { background:#ede9fe;color:#6b21a8; }

    /* DataTables overrides */
    .dataTables_wrapper .dataTables_length label { display:flex; flex-direction:column; align-items:flex-start; margin:0; width:100%; font-weight:normal; }
    .dataTables_wrapper .dataTables_length select { width:100%; height:40px;border-radius:0.65rem;border:1.5px solid #e8edf4;background:#fafbfd;font-size:0.855rem;padding:0 0.9rem;outline:none;cursor:pointer;color:#334155; }
    .dataTables_wrapper .dataTables_length select:focus { border-color:#1a73e8;background:#fff;box-shadow:0 0 0 3px rgba(26,115,232,0.09)!important; }
    .dataTables_wrapper .dataTables_info { font-size:0.8rem;color:#94a3b8; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding:0!important;margin:0!important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a:hover { background:#1a73e8!important;color:#fff!important;border-color:#1a73e8!important;border-radius:0.4rem!important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button a { border-radius:0.4rem!important;font-size:0.82rem; }

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
    .tl-item::before { content:'';position:absolute;left:-1.5rem;top:5px;width:14px;height:14px;border-radius:50%;background:#fff;border:2px solid #6366f1; }
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
    @media(max-width:900px) { .table-wrap{display:none;} .cards-wrap{display:block;} }
</style>

{{-- Page Header --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="hero-title mb-0">Laporan Surat</div>
            </div>
            <div class="hero-sub">Rekap seluruh surat beserta riwayat perjalanannya</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-bar-chart-line-fill"></i> {{ count($letters) }} data
            </div>
        </div>
    </div>
</div>

<div class="filter-card">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-md flex-grow-1">
            <label class="f-label">Cari Laporan</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik nomor surat atau perihal..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-auto d-flex gap-2 align-items-end">
            <div id="dtLength" style="min-width:110px;"></div>
            <button type="submit" class="btn-filter" style="min-width:100px; justify-content:center;">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ request()->url() }}" class="btn-reset text-center" style="min-width:100px; justify-content:center;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap table-container" style="padding:1rem 0.25rem 0.75rem;">
    <table id="laporanTable" class="inbox-table w-100">
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
                            @if(request()->routeIs('letters.index'))
                            <a href="{{ route('letters.printDisposition', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" target="_blank"
                               class="btn-act btn-act-disp"
                               title="Lacak Disposisi">
                                <i class="bi bi-eye"></i>
                            </a>
                            @else
                            <a href="{{ route('letters.show', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
                               class="btn-act btn-act-disp"
                               style="background:#fdf4ff;color:#7e22ce;"
                               title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
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
                @if(request()->routeIs('letters.index'))
                <a href="{{ route('letters.printDisposition', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;background:#fdf4ff;color:#7e22ce;border:none;border-radius:0.5rem;font-size:0.78rem;font-weight:700;padding:0.4rem 0.85rem;text-decoration:none;cursor:pointer;">
                    <i class="bi bi-eye"></i> Lacak Disposisi
                </a>
                @else
                <a href="{{ route('letters.show', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
                   style="display:inline-flex;align-items:center;gap:5px;background:#fdf4ff;color:#7e22ce;border:none;border-radius:0.5rem;font-size:0.78rem;font-weight:700;padding:0.4rem 0.85rem;text-decoration:none;cursor:pointer;">
                    <i class="bi bi-eye"></i> Detail
                </a>
                @endif
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
            sLengthMenu:'<span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;margin-bottom:0.35rem;display:block;">Tampilkan</span> _MENU_', sSearch:'',
            sZeroRecords:'Tidak ditemukan data', sInfo:'_START_–_END_ dari _TOTAL_',
            sInfoEmpty:'0 entri', sInfoFiltered:'(dari _MAX_)',
            oPaginate:{ sPrevious:'‹', sNext:'›' }
        },
        order:[], pageLength:25,
        dom: 'lrtip',
        initComplete: function() {
            var api = this.api();
            $('#dtLength').html($('.dataTables_length').detach());
            $('#dtInfo').html($('.dataTables_info').detach());
            $('#dtPaginate').html($('.dataTables_paginate').detach());
        }
    });
});
</script>
@endpush

@endsection