<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjual per Provinsi</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .provinsi-title { font-size: 14px; font-weight: bold; margin-top: 15px; color: #444; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjual per Lokasi (Provinsi)</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    @foreach($data as $provinsi => $listPenjual)
        <div class="provinsi-title">Provinsi: {{ $provinsi }}</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Toko</th>
                    <th>Pemilik</th>
                    <th>Kota/Kab</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listPenjual as $idx => $toko)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $toko->namaToko }}</td>
                    <td>{{ $toko->namaPenjual }}</td>
                    <td>{{ $toko->kota }}</td>
                    <td>{{ $toko->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>