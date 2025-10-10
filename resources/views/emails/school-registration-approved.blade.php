<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Onayı</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #155724;
        }
        .credentials-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
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
            <h1>🎉 Kayıt Onaylandı!</h1>
            <p>Hoş Geldiniz</p>
        </div>

        <div class="content">
            <h2>Tebrikler!</h2>
            
            <div class="success-box">
                <h3>✅ <strong>{{ $school->name }}</strong> okulu kaydınız başarıyla onaylandı!</h3>
                <p>Artık Ders Programı Sistemi'ni kullanmaya başlayabilirsiniz.</p>
            </div>

            <p>Sizin için bir okul yöneticisi hesabı oluşturuldu. Giriş bilgileriniz aşağıda yer almaktadır:</p>

            <div class="credentials-box">
                <h3>🔑 Giriş Bilgileriniz:</h3>
                <ul>
                    <li><strong>Email:</strong> {{ $adminUser->email }}</li>
                    <li><strong>Geçici Şifre:</strong> <code>{{ $temporaryPassword }}</code></li>
                </ul>
                <p><strong>Önemli:</strong> İlk girişinizde şifrenizi değiştirmeniz önerilir.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/admin-panel') }}" class="button">
                    🚀 Sisteme Giriş Yap
                </a>
            </div>

            <h3>📚 Sistemde Neler Yapabilirsiniz:</h3>
            <ul>
                <li>Öğretmen ve öğrenci kayıtları oluşturabilirsiniz</li>
                <li>Sınıflar ve dersler tanımlayabilirsiniz</li>
                <li>Haftalık ders programları oluşturabilirsiniz</li>
                <li>Otomatik program oluşturma özelliğini kullanabilirsiniz</li>
                <li>Çakışma kontrolü ile hatasız programlar yapabilirsiniz</li>
            </ul>

            <p>Herhangi bir sorunuz olursa, destek ekibimizle iletişime geçebilirsiniz.</p>
        </div>

        <div class="footer">
            <p>Bu email otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.</p>
            <p>&copy; {{ date('Y') }} Ders Programı Sistemi - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>