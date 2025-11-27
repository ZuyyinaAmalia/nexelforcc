<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Ditolak</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        
        <h2 style="color: #dc3545;">Pemberitahuan: Pendaftaran Ditolak</h2>
        
        <p>Halo, <strong>{{ $penjual->nama_toko }}</strong>.</p>
        
        <p>Kami telah melakukan verifikasi terhadap data pendaftaran Anda. 
        Mohon maaf, pengajuan Anda sebagai penjual di <strong>MartPlace</strong> telah <strong>DITOLAK</strong>.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0;"><strong>Alasan Penolakan:</strong></p>
            <p style="margin: 10px 0 0 0; color: #555;">{{ $penjual->alasan_ditolak ?? 'Data yang dikirimkan tidak memenuhi kriteria minimum kami.' }}</p>
        </div>
        
        <p><strong>Hal yang bisa Anda lakukan:</strong></p>
        <ul style="color: #555; line-height: 1.8;">
            <li>Periksa kembali data yang telah dikirimkan</li>
            <li>Pastikan semua dokumen valid dan sesuai persyaratan</li>
            <li>Hubungi tim support kami untuk bantuan lebih lanjut</li>
            <li>Anda bisa mendaftar ulang dengan data yang telah diperbaiki</li>
        </ul>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('http://localhost:5173/login') }}" 
               style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
               Kembali ke Halaman Login
            </a>
        </div>

        <p style="color: #888; margin-top: 30px;">Jika Anda merasa ada kesalahan, silakan hubungi tim support kami:</p>
        <p style="color: #007bff; font-weight: bold;">Email: support@marketplace.com</p>
        
        <hr style="border: 0; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #888;">Ini adalah email otomatis, mohon jangan membalas email ini.</p>
    </div>

</body>
</html>