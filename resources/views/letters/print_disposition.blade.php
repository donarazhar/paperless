<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi Surat - {{ $letter->letter_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 150px;
            font-weight: bold;
        }
        .info-table td.colon {
            width: 10px;
        }
        .dispo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .dispo-table th, .dispo-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .dispo-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
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

    <div class="header">
        <h3>DISPOSISI SURAT</h3>
        <p>Aplikasi Persuratan (Paperless)</p>
    </div>

    @php
        $pengirimText = $letter->type === 'external' ? $letter->external_sender_name : ($letter->sender->name ?? 'Sistem');
        $tujuanText   = $letter->type === 'outbound_external' ? $letter->external_recipient_name : ($letter->recipientUser ? $letter->recipientUser->name : 'Unit '.($letter->recipientUnit->name ?? '--'));
        
        $dispoHistory = collect();
        foreach($letter->dispositions as $d) {
            $target = $d->toUser ? $d->toUser->name : ($d->unit ? 'Unit '.$d->unit->name : '--');
            $actor  = $d->fromUser ? $d->fromUser->name : 'Sistem';
            $dispoHistory->push(['sort_date'=>$d->created_at->timestamp,'tanggal'=>$d->created_at->format('d/m/y'),'aksi'=>'Disposisi','aktor'=>$target,'catatan'=>$d->note ?? '-','by'=>$actor]);
            if($d->status !== 'pending') {
                $sInd = $d->status==='accepted' ? 'Selesai' : ($d->status==='pertimbangan' ? 'Pertimbangan' : ucfirst($d->status));
                $dispoHistory->push(['sort_date'=>$d->updated_at->timestamp,'tanggal'=>$d->updated_at->format('d/m/y'),'aksi'=>$sInd,'aktor'=>$actor,'catatan'=>$d->response_note ?? '-','by'=>$target]);
            }
        }
        $historiesList = $dispoHistory->sortBy('sort_date')->values();
    @endphp

    <table class="info-table">
        <tr>
            <td class="label">Nomor Surat</td>
            <td class="colon">:</td>
            <td>{{ $letter->letter_number ?: '—' }}</td>
            <td class="label">No. Agenda</td>
            <td class="colon">:</td>
            <td>{{ $letter->agenda_number ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Surat</td>
            <td class="colon">:</td>
            <td>{{ $letter->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</td>
            <td class="label">Status</td>
            <td class="colon">:</td>
            <td>
                @if($letter->status === 'completed') Selesai
                @elseif($letter->status === 'disposed') Disposisi
                @else {{ ucfirst($letter->status) }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Pengirim</td>
            <td class="colon">:</td>
            <td colspan="4">{{ $pengirimText }}</td>
        </tr>
        <tr>
            <td class="label">Tujuan</td>
            <td class="colon">:</td>
            <td colspan="4">{{ $tujuanText }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td colspan="4">{{ $letter->subject }}</td>
        </tr>
    </table>

    <table class="dispo-table">
        <thead>
            <tr>
                <th style="width:20%;">Tanggal</th>
                <th style="width:25%;">Ditujukan Ke</th>
                <th style="width:55%;">Isi Disposisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historiesList as $item)
                @php
                    $catatan = $item['catatan'] && $item['catatan'] !== '-' ? nl2br(e($item['catatan'])) : '—';
                @endphp
                <tr>
                    <td>{{ $item['tanggal'] }}</td>
                    <td><strong>{{ $item['aktor'] }}</strong></td>
                    <td>{!! $catatan !!}</td>
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
