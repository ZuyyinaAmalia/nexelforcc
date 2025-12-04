<!DOCTYPE html>
<html>
<head>
    <title>Laporan Status Penjual</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Daftar Akun Penjual</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Toko</th>
                <th>Pemilik</th>
                <th>Email</th>
                <th>Status</th>
                <th>Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->namaToko }}</td>
                <td>{{ $item->namaPenjual }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>