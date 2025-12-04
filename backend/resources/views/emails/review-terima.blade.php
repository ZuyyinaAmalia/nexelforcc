<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih Atas Review Anda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .review-box {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .review-item {
            margin-bottom: 15px;
        }
        .review-label {
            font-weight: 600;
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .review-value {
            color: #555;
            font-size: 16px;
            margin-top: 5px;
            line-height: 1.6;
            word-break: break-word;
        }
        .stars {
            font-size: 24px;
            color: #ffc107;
            letter-spacing: 2px;
        }
        .message {
            font-size: 16px;
            color: #333;
            line-height: 1.8;
            margin: 20px 0;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .footer p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #764ba2;
        }
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }
        .highlight {
            color: #667eea;
            font-weight: 600;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }
        .info-item {
            font-size: 14px;
            color: #666;
        }
        .info-item strong {
            display: block;
            color: #333;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>⭐ Terima Kasih!</h1>
            <p>Ulasan Anda sangat berarti bagi kami</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo <span class="highlight">{{ $namaPengunjung }}</span>,
            </div>

            <p class="message">
                Kami sangat berterima kasih telah meluangkan waktu untuk memberikan ulasan pada produk kami. 
                Feedback dari pelanggan seperti Anda membantu kami terus meningkatkan kualitas layanan dan produk.
            </p>

            <!-- Review Details -->
            <div class="review-box">
                <div class="review-item">
                    <div class="review-label">Produk yang Diulas</div>
                    <div class="review-value">{{ $produk_nama }}</div>
                </div>

                <div class="review-item">
                    <div class="review-label">Rating Anda</div>
                    <div class="review-value">
                        <span class="stars">
                            @for ($i = 0; $i < $rating; $i++)
                                ★
                            @endfor
                            @for ($i = $rating; $i < 5; $i++)
                                ☆
                            @endfor
                        </span>
                        <span style="margin-left: 10px; color: #667eea; font-weight: 600;">{{ $rating }}/5</span>
                    </div>
                </div>

                <div class="review-item">
                    <div class="review-label">Ulasan Anda</div>
                    <div class="review-value">
                        "{{ $ulasan }}"
                    </div>
                </div>

                <!-- Info Pengunjung -->
                @if($noHpPengunjung || $provinsiPengunjung)
                <div class="divider"></div>
                <div class="info-grid">
                    @if($noHpPengunjung)
                    <div class="info-item">
                        <strong>📱 No. HP:</strong>
                        {{ $noHpPengunjung }}
                    </div>
                    @endif
                    
                    @if($provinsiPengunjung)
                    <div class="info-item">
                        <strong>📍 Provinsi:</strong>
                        {{ $provinsiPengunjung }}
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <p class="message">
                Ulasan Anda telah diterima dan akan membantu calon pembeli lain membuat keputusan yang lebih baik. 
                Kami berkomitmen untuk terus memberikan produk dan layanan terbaik untuk Anda.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Jika Anda memiliki pertanyaan atau masalah, jangan ragu untuk menghubungi kami.</p>
            <p style="margin-bottom: 20px;">
                📧 support@nexelmart.com | 📱 +62 812-3456-7890
            </p>
            <div class="divider"></div>
            <p>
                <strong>NEXEL MartPlace</strong><br>
                Platform jual-beli online terpercaya
            </p>
        </div>
    </div>
</body>
</html>