#!/bin/bash

# 🚀 Terraform AWS Deployment Script
# Bu script Terraform ile AWS'de EC2 instance oluşturur ve uygulamayı deploy eder

echo "🚀 Terraform AWS Deployment başlatılıyor..."

# Terraform kurulumu kontrol et
if ! command -v terraform &> /dev/null; then
    echo "❌ Terraform bulunamadı. Lütfen Terraform'u kurun:"
    echo "   https://developer.hashicorp.com/terraform/downloads"
    exit 1
fi

# AWS CLI kurulumu kontrol et
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI bulunamadı. Lütfen AWS CLI'yi kurun:"
    echo "   https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html"
    exit 1
fi

# AWS credentials kontrol et
if ! aws sts get-caller-identity &> /dev/null; then
    echo "❌ AWS credentials yapılandırılmamış. Lütfen 'aws configure' çalıştırın."
    exit 1
fi

# SSH key kontrol et
if [ ! -f ~/.ssh/id_rsa.pub ]; then
    echo "🔑 SSH key oluşturuluyor..."
    ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
fi

# Terraform dizinine git
cd terraform

# Terraform init
echo "📦 Terraform başlatılıyor..."
terraform init

# Terraform plan
echo "📋 Deployment planı oluşturuluyor..."
terraform plan

# Terraform apply
echo "🚀 AWS'de deployment yapılıyor..."
terraform apply -auto-approve

# Output'ları göster
echo "✅ Deployment tamamlandı!"
echo ""
echo "📊 Deployment Bilgileri:"
terraform output

echo ""
echo "🌐 Site URL'leri:"
echo "   Ana Site: $(terraform output -raw url)"
echo "   Admin Panel: $(terraform output -raw admin_url)"
echo ""
echo "📋 Sonraki adımlar:"
echo "1. Site yüklenene kadar 2-3 dakika bekleyin"
echo "2. URL'leri test edin"
echo "3. Performans testi yapın"
echo ""
echo "🔧 SSH ile bağlanmak için:"
echo "   ssh -i ~/.ssh/id_rsa ubuntu@$(terraform output -raw public_ip)"
