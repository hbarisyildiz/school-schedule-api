# AWS CLI Kurulum Script'i
# Bu script AWS CLI'yi kurar ve yapılandırır

Write-Host "🚀 AWS CLI kurulumu başlatılıyor..." -ForegroundColor Blue

# AWS CLI indirme URL'si
$awsCliUrl = "https://awscli.amazonaws.com/AWSCLIV2.msi"
$installerPath = "$env:TEMP\AWSCLIV2.msi"

try {
    # AWS CLI'yi indir
    Write-Host "📥 AWS CLI indiriliyor..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri $awsCliUrl -OutFile $installerPath
    
    # AWS CLI'yi kur
    Write-Host "📦 AWS CLI kuruluyor..." -ForegroundColor Yellow
    Start-Process -FilePath "msiexec.exe" -ArgumentList "/i $installerPath /quiet" -Wait
    
    # PATH'i yenile
    $env:PATH = [System.Environment]::GetEnvironmentVariable("PATH", "Machine") + ";" + [System.Environment]::GetEnvironmentVariable("PATH", "User")
    
    Write-Host "✅ AWS CLI başarıyla kuruldu!" -ForegroundColor Green
    Write-Host "🔧 AWS CLI'yi yapılandırmak için 'aws configure' komutunu çalıştırın" -ForegroundColor Cyan
    
} catch {
    Write-Host "❌ AWS CLI kurulumu başarısız: $($_.Exception.Message)" -ForegroundColor Red
}

# Temizlik
if (Test-Path $installerPath) {
    Remove-Item $installerPath
}
