<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi Surat - {{ $letter->letter_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 70px;
        }
        .info-table td.colon {
            width: 10px;
            text-align: center;
        }
        .dispo-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .dispo-table th, .dispo-table td {
            border: 1px solid #f0f0f0;
            padding: 8px 12px;
            vertical-align: top;
        }
        .dispo-table th {
            background-color: #e2f0ee;
            color: #2b7a6b;
            font-weight: bold;
            text-align: center;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer; font-weight: bold;">Cetak PDF / Print</button>
    </div>

    @php
        $pengirimText = $letter->type === 'external' ? $letter->external_sender_name : ($letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem'));
        $tujuanText   = $letter->type === 'outbound_external' ? $letter->external_recipient_name : ($letter->recipientUser ? $letter->recipientUser->name : ($letter->recipientUnit->name ?? '--'));
        
        $dispoHistory = collect();
        
        // Proses awal: Semua surat masuk ke Subag Persuratan terlebih dahulu
        $dispoHistory->push([
            'sort_date' => $letter->created_at->timestamp - 1,
            'tanggal'   => $letter->created_at->format('d-m-Y'),
            'aksi'      => 'Disposisi',
            'aktor'     => 'Subag Persuratan',
            'catatan'   => '-',
            'by'        => 'Sistem'
        ]);

        foreach($letter->dispositions as $d) {
            if (\Illuminate\Support\Str::contains($d->note, 'Diteruskan kepada personal terkait di unit')) continue;
            
            $target = $d->toUser ? $d->toUser->name : ($d->unit ? $d->unit->name : '--');
            $actor  = $d->fromUser ? $d->fromUser->name : 'Sistem';
            $dispoHistory->push(['sort_date'=>$d->created_at->timestamp,'tanggal'=>$d->created_at->format('d-m-Y'),'aksi'=>'Disposisi','aktor'=>$target,'catatan'=>$d->note ?? '-','by'=>$actor]);
        }

        foreach($letter->histories->where('action', 'replied') as $h) {
            $dispoHistory->push([
                'sort_date' => $h->created_at->timestamp,
                'tanggal' => $h->created_at->format('d-m-Y'),
                'aksi' => 'Catatan',
                'aktor' => 'Catatan oleh: ' . ($h->user->name ?? 'User'),
                'catatan' => $h->note,
                'by' => $h->user->name ?? 'User'
            ]);
        }

        $historiesList = $dispoHistory->sortBy('sort_date')->values();
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 120px; text-align: center;">
                <img src="{{ asset('img/logo.png') }}" style="width: 100px; height: auto;" alt="Logo">
            </td>
            <td style="text-align: right;">
                <div style="font-family: 'Times New Roman', Times, serif;">
                    <div style="font-size: 22px; color: #1a5ea3; margin-bottom: 4px;">YAYASAN PESANTREN ISLAM AL AZHAR</div>
                    <div style="font-size: 14px; margin-bottom: 2px;">Kompleks Masjid Agung Al Azhar</div>
                    <div style="font-size: 12px; margin-bottom: 2px;">Jl. Sisingamangaraja, Kebayoran Baru, Jakarta Selatan 12100, Indonesia</div>
                    <div style="font-size: 11px; color: #1a5ea3;">Telp. 021-7261233, 021-7245682 Fax. 021-7393646 - www.al-azhar.or.id</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">No Agenda</td>
            <td class="colon">:</td>
            <td style="width: 250px;">{{ $letter->agenda_number ?: '—' }}</td>
            <td style="text-align: center; vertical-align: top;" rowspan="3">
                <span style="text-decoration: underline;">Diterima Hari : {{ \Carbon\Carbon::parse($letter->created_at)->locale('id')->isoFormat('dddd') }}</span>
            </td>
            <td style="text-align: right; vertical-align: top;" rowspan="3">
                Tanggal : {{ \Carbon\Carbon::parse($letter->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}
            </td>
        </tr>
        <tr>
            <td class="label">Asal Surat</td>
            <td class="colon">:</td>
            <td>{{ $pengirimText }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td>{{ $letter->subject }}</td>
        </tr>
    </table>

    <table class="dispo-table">
        <thead>
            <tr>
                <th style="width:30%;">DITERUSKAN YTH</th>
                <th style="width:50%;">ISI DISPOSISI</th>
                <th style="width:20%;">TANGGAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historiesList as $item)
                @php
                    $catatan = $item['catatan'] && $item['catatan'] !== '-' ? nl2br(e($item['catatan'])) : '—';
                @endphp
                <tr>
                    <td>{{ $item['aktor'] }}</td>
                    <td>{!! $catatan !!}</td>
                    <td style="text-align: center;">{{ $item['tanggal'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:20px;">Belum ada riwayat disposisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
