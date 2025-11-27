<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Diterima</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        
        <h2 style="color: #28a745;">Selamat! Pendaftaran Diterima</h2>
        
        <p>Halo, <strong>{{ $penjual->nama_toko }}</strong>.</p>
        
        <p>Kami telah melakukan verifikasi terhadap data pendaftaran Anda. 
        Dengan senang hati kami sampaikan bahwa pengajuan Anda sebagai penjual di <strong>MartPlace</strong> telah <strong>DISETUJUI</strong>.</p>
        
        <p>Sekarang Anda sudah bisa login dan mulai berjualan produk Anda.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('http://localhost:5173/login') }}" 
               style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
               Login ke Akun Penjual
            </a>
        </div>

        <p>Terima kasih telah bergabung bersama kami!</p>
        
        <hr style="border: 0; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #888;">Ini adalah email otomatis, mohon jangan membalas email ini.</p>
    </div>

</body>
</html>