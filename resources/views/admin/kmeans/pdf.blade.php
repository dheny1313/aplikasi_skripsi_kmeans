<!DOCTYPE html>
<html>
<head>
    <title>Laporan Klastering K-Means</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; margin: 5px 0 0 0; color: #555; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px 0; }
        .cluster-title { background-color: #f3f4f6; padding: 8px; font-weight: bold; margin-top: 20px; border: 1px solid #ddd; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f9fafb; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">LEMBAR HASIL KLASTERING SISWA (K-MEANS)</h1>
        <p class="subtitle">Sistem Alokasi Pengajar Codero</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Tanggal Eksekusi</strong></td>
            <td width="80%">: {{ $log->created_at->format('d F Y, H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Klaster (K)</strong></td>
            <td>: {{ $log->k_value }} Klaster</td>
        </tr>
        <tr>
            <td><strong>Nilai Validasi (DBI)</strong></td>
            <td>: {{ number_format($log->dbi_score, 4) }} (Mendekati 0 = Semakin Valid)</td>
        </tr>
        <tr>
            <td><strong>Keterangan Data</strong></td>
            <td>: {{ $log->description }}</td>
        </tr>
    </table>

    @foreach($clusters as $clusterNumber => $members)
        <div class="cluster-title">
            KLASTER {{ $clusterNumber }} - (Total: {{ $members->count() }} Siswa)
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">NIS</th>
                    <th width="40%">Nama Siswa</th>
                    <th width="35%">Ringkasan Skor Agregasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $member->snapshot_data['nis'] ?? '-' }}</td>
                    <td>{{ $member->snapshot_data['name'] ?? 'Tidak Diketahui' }}</td>
                    <td>
                        @if(isset($member->snapshot_data['scores']))
                            @foreach($member->snapshot_data['scores'] as $code => $val)
                                {{ strtoupper($code) }}: {{ round($val, 2) }}<br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
