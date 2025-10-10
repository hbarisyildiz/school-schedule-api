<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Talebi Hakkında</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .rejection-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #721c24;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #0c5460;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Kayıt Talebi Hakkında</h1>
            <p>Bilgilendirme</p>
        </div>

        <div class="content">
            <h2>Sayın {{ $registrationRequest->school_name }} Yönetimi,</h2>
            
            <p>Ders Programı Sistemi'ne yaptığınız kayıt talebiniz incelenmiştir.</p>

            <div class="rejection-box">
                <h3>❌ Kayıt Talebiniz Onaylanmamıştır</h3>
                <p><strong>Red Sebebi:</strong></p>
                <p>{{ $registrationRequest->rejection_reason }}</p>
            </div>

            <div class="info-box">
                <h3>🔄 Yeniden Başvuru</h3>
                <p>Belirtilen konuları düzelttikten sonra yeniden başvuruda bulunabilirsiniz.</p>
                <p>Başvuru bilgilerinizi gözden geçirip eksiklikleri tamamladıktan sonra tekrar deneyebilirsiniz.</p>
            </div>

            <h3>📋 Başvuru Bilgileriniz:</h3>
            <ul>
                <li><strong>Okul Adı:</strong> {{ $registrationRequest->school_name }}</li>
                <li><strong>Email:</strong> {{ $registrationRequest->email }}</li>
                <li><strong>Telefon:</strong> {{ $registrationRequest->phone }}</li>
                <li><strong>İl/İlçe:</strong> {{ $registrationRequest->city->name }} / {{ $registrationRequest->district->name }}</li>
                <li><strong>Plan:</strong> {{ $registrationRequest->subscriptionPlan->name }}</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ url('/school-registration') }}" class="button">
                    🔄 Yeniden Başvuru Yap
                </a>
            </div>

            <p>Herhangi bir sorunuz varsa destek ekibimizle iletişime geçebilirsiniz.</p>
        </div>

        <div class="footer">
            <p>Bu email otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.</p>
            <p>&copy; {{ date('Y') }} Ders Programı Sistemi - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>