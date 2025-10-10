<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Doğrulama</title>
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
            background: linear-gradient(135deg, #3498db, #2980b9);
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            <h1>🏫 Ders Programı Sistemi</h1>
            <p>Email Doğrulama</p>
        </div>

        <div class="content">
            <h2>Merhaba,</h2>
            
            <p><strong>{{ $registrationRequest->school_name }}</strong> okulu olarak sistemimize kayıt talebinde bulundunuz.</p>
            
            <p>Kayıt işleminizi tamamlamak için aşağıdaki butona tıklayarak email adresinizi doğrulamanız gerekmektedir:</p>

            <div style="text-align: center;">
                <a href="{{ url('/verify-email/' . $registrationRequest->verification_token) }}" class="button">
                    📧 Email Adresimi Doğrula
                </a>
            </div>

            <div class="info-box">
                <h3>📋 Başvuru Bilgileriniz:</h3>
                <ul>
                    <li><strong>Okul Adı:</strong> {{ $registrationRequest->school_name }}</li>
                    <li><strong>Email:</strong> {{ $registrationRequest->email }}</li>
                    <li><strong>Telefon:</strong> {{ $registrationRequest->phone }}</li>
                    <li><strong>İl/İlçe:</strong> {{ $registrationRequest->city->name }} / {{ $registrationRequest->district->name }}</li>
                    <li><strong>Plan:</strong> {{ $registrationRequest->subscriptionPlan->name }}</li>
                </ul>
            </div>

            <p><strong>Önemli:</strong> Email doğrulama işlemini tamamladıktan sonra, kayıt talebiniz sistem yöneticilerimiz tarafından incelenecek ve size bilgi verilecektir.</p>

            <p>Bu email doğrulama linkini <strong>24 saat</strong> içinde kullanmanız gerekmektedir.</p>
        </div>

        <div class="footer">
            <p>Bu email otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.</p>
            <p>&copy; {{ date('Y') }} Ders Programı Sistemi - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>