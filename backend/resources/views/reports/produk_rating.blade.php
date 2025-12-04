<!DOCTYPE html>
<html>
<head>
    <title>Laporan Produk dan Rating</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; font-size: 11px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Daftar Produk & Rating</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Nama Toko</th>
                <th>Provinsi</th>
                <th width="10%">Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->namaProduk }}</td>
                <td>{{ $item->kategori ? $item->kategori->namaKategori : '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ $item->penjual->namaToko ?? '-' }}</td>
                <td>{{ $item->penjual->alamat->provinsi ?? '-' }}</td>
                <td class="center">
                    @if($item->reviews_avg_rating)
                        {{ number_format($item->reviews_avg_rating, 1) }}
                    @else
                        <span style="color: #999;">Belum ada rating</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>