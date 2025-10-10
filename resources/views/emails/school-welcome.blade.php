<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoş Geldiniz!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .welcome-title {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .login-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .login-info h3 {
            color: #495057;
            margin-top: 0;
        }
        .credential {
            margin: 10px 0;
            font-family: monospace;
            background-color: #f1f3f4;
            padding: 8px;
            border-radius: 3px;
            font-size: 16px;
        }
        .next-steps {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #856404;
            margin-top: 0;
        }
        .next-steps ul {
            color: #856404;
        }
        .support-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">📅 Okul Ders Programı Sistemi</div>
            <p>Akıllı Ders Programı Yönetimi</p>
        </div>

        <h2 class="welcome-title">🎉 Hoş Geldiniz {{ $school->school_name }}!</h2>

        <p>Merhaba,</p>
        
        <p>Okul Ders Programı Sistemi'ne başarıyla kayıt oldunuz! Artık okulunuzun ders programlarını kolayca oluşturabilir ve yönetebilirsiniz.</p>

        <div class="info-box">
            <strong>✅ Hesabınız hazır!</strong><br>
            Hemen giriş yaparak sistemi kullanmaya başlayabilirsiniz.
        </div>

        <div class="login-info">
            <h3>🔐 Giriş Bilgileriniz</h3>
            <div>
                <strong>Email:</strong>
                <div class="credential">{{ $school->email }}</div>
            </div>
            <div>
                <strong>Şifre:</strong>
                <div class="credential">{{ $password }}</div>
            </div>
            <div>
                <strong>Okul Kodu:</strong>
                <div class="credential">{{ $school->school_code }}</div>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.frontend_url', 'http://localhost:8000') }}/login" class="btn">
                🚀 Sisteme Giriş Yap
            </a>
        </div>

        <div class="next-steps">
            <h3>📋 Sonraki Adımlar</h3>
            <ul>
                <li><strong>Profil Tamamlama:</strong> Okul bilgilerinizi tamamlayın</li>
                <li><strong>Öğretmen Ekleme:</strong> Öğretmenlerinizi sisteme ekleyin</li>
                <li><strong>Ders Tanımlama:</strong> Okul derslerinizi tanımlayın</li>
                <li><strong>Program Oluşturma:</strong> İlk ders programınızı oluşturun</li>
            </ul>
        </div>

        <div class="info-box">
            <strong>💡 İpucu:</strong> Güvenliğiniz için giriş yaptıktan sonra şifrenizi değiştirmenizi öneririz.
        </div>

        <div class="support-info">
            <p><strong>Yardıma mı ihtiyacınız var?</strong></p>
            <p>
                📧 destek@okuldersprogrami.com<br>
                📞 0850 123 45 67<br>
                🕒 Pazartesi-Cuma 09:00-18:00
            </p>
            <p style="margin-top: 20px; font-size: 12px;">
                Bu email otomatik olarak gönderilmiştir. Lütfen yanıtlamayın.
            </p>
        </div>
    </div>
</body>
</html>