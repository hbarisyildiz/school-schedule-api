# PowerShell Server Başlatma Script'i
# Bu dosyayı: start-server.ps1 olarak kaydedin

Write-Host "🚀 Laravel School Schedule API Server Başlatılıyor..." -ForegroundColor Green

# Proje dizinine git
Set-Location "C:\MAMP\htdocs\dersProg\school-schedule-api"

# Port kontrolü
$port = 8000
$portInUse = $true

while ($portInUse) {
    try {
        $connection = Test-NetConnection -ComputerName localhost -Port $port -WarningAction SilentlyContinue
        if ($connection.TcpTestSucceeded) {
            Write-Host "⚠️  Port $port kullanımda. Sonraki port deneniyor..." -ForegroundColor Yellow
            $port++
        } else {
            $portInUse = $false
        }
    } catch {
        $portInUse = $false
    }
    
    if ($port -gt 8010) {
        Write-Host "❌ Uygun port bulunamadı!" -ForegroundColor Red
        exit 1
    }
}

Write-Host "✅ Port $port kullanılacak" -ForegroundColor Green

# Server başlat
Write-Host "🌐 Server başlatılıyor: http://localhost:$port" -ForegroundColor Cyan

# PHP server'ı başlat (public dizininden)
Set-Location "public"
php -d display_errors=0 -d log_errors=0 -S localhost:$port

Write-Host "⏹️  Server durduruldu." -ForegroundColor Red