<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubjectTemplate;

class SubjectTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📚 MEB Ders Şablonları oluşturuluyor...');
        
        // İlkokul Dersleri
        $ilkokulDersleri = [
            ['name' => 'Türkçe', 'description' => 'Türkçe dersi'],
            ['name' => 'Matematik', 'description' => 'Matematik dersi'],
            ['name' => 'Hayat Bilgisi', 'description' => 'Hayat Bilgisi dersi'],
            ['name' => 'Fen Bilimleri', 'description' => 'Fen Bilimleri dersi'],
            ['name' => 'Sosyal Bilgiler', 'description' => 'Sosyal Bilgiler dersi'],
            ['name' => 'İngilizce', 'description' => 'İngilizce dersi'],
            ['name' => 'Görsel Sanatlar', 'description' => 'Görsel Sanatlar dersi'],
            ['name' => 'Müzik', 'description' => 'Müzik dersi'],
            ['name' => 'Beden Eğitimi ve Oyun', 'description' => 'Beden Eğitimi ve Oyun dersi'],
            ['name' => 'Trafik Güvenliği', 'description' => 'Trafik Güvenliği dersi'],
            ['name' => 'İnsan Hakları, Yurttaşlık ve Demokrasi', 'description' => 'İnsan Hakları, Yurttaşlık ve Demokrasi dersi'],
        ];
        
        foreach ($ilkokulDersleri as $ders) {
            SubjectTemplate::create([
                'school_type' => 'ilkokul',
                'name' => $ders['name'],
                'description' => $ders['description'],
                'is_active' => true
            ]);
        }
        $this->command->info('✅ İlkokul dersleri eklendi: ' . count($ilkokulDersleri) . ' ders');
        
        // Ortaokul Dersleri
        $ortaokulDersleri = [
            ['name' => 'Türkçe', 'description' => 'Türkçe dersi'],
            ['name' => 'Matematik', 'description' => 'Matematik dersi'],
            ['name' => 'Fen Bilimleri', 'description' => 'Fen Bilimleri dersi'],
            ['name' => 'Sosyal Bilgiler', 'description' => 'Sosyal Bilgiler dersi'],
            ['name' => 'Din Kültürü ve Ahlak Bilgisi', 'description' => 'Din Kültürü ve Ahlak Bilgisi dersi'],
            ['name' => 'İngilizce', 'description' => 'İngilizce dersi'],
            ['name' => 'Görsel Sanatlar', 'description' => 'Görsel Sanatlar dersi'],
            ['name' => 'Müzik', 'description' => 'Müzik dersi'],
            ['name' => 'Beden Eğitimi ve Spor', 'description' => 'Beden Eğitimi ve Spor dersi'],
            ['name' => 'Teknoloji ve Tasarım', 'description' => 'Teknoloji ve Tasarım dersi'],
            ['name' => 'Bilişim Teknolojileri ve Yazılım', 'description' => 'Bilişim Teknolojileri ve Yazılım dersi'],
            ['name' => 'İnkılap Tarihi ve Atatürkçülük', 'description' => 'İnkılap Tarihi ve Atatürkçülük dersi'],
        ];
        
        foreach ($ortaokulDersleri as $ders) {
            SubjectTemplate::create([
                'school_type' => 'ortaokul',
                'name' => $ders['name'],
                'description' => $ders['description'],
                'is_active' => true
            ]);
        }
        $this->command->info('✅ Ortaokul dersleri eklendi: ' . count($ortaokulDersleri) . ' ders');
        
        // Lise Dersleri
        $liseDersleri = [
            ['name' => 'Türk Dili ve Edebiyatı', 'description' => 'Türk Dili ve Edebiyatı dersi'],
            ['name' => 'Matematik', 'description' => 'Matematik dersi'],
            ['name' => 'Fizik', 'description' => 'Fizik dersi'],
            ['name' => 'Kimya', 'description' => 'Kimya dersi'],
            ['name' => 'Biyoloji', 'description' => 'Biyoloji dersi'],
            ['name' => 'Tarih', 'description' => 'Tarih dersi'],
            ['name' => 'Coğrafya', 'description' => 'Coğrafya dersi'],
            ['name' => 'Felsefe', 'description' => 'Felsefe dersi'],
            ['name' => 'İngilizce', 'description' => 'İngilizce dersi'],
            ['name' => 'Almanca', 'description' => 'Almanca dersi'],
            ['name' => 'Fransızca', 'description' => 'Fransızca dersi'],
            ['name' => 'Beden Eğitimi', 'description' => 'Beden Eğitimi dersi'],
            ['name' => 'Görsel Sanatlar', 'description' => 'Görsel Sanatlar dersi'],
            ['name' => 'Müzik', 'description' => 'Müzik dersi'],
            ['name' => 'Din Kültürü ve Ahlak Bilgisi', 'description' => 'Din Kültürü ve Ahlak Bilgisi dersi'],
            ['name' => 'Bilgisayar Bilimi', 'description' => 'Bilgisayar Bilimi dersi'],
            ['name' => 'Proje Hazırlama', 'description' => 'Proje Hazırlama dersi'],
        ];
        
        foreach ($liseDersleri as $ders) {
            SubjectTemplate::create([
                'school_type' => 'lise',
                'name' => $ders['name'],
                'description' => $ders['description'],
                'is_active' => true
            ]);
        }
        $this->command->info('✅ Lise dersleri eklendi: ' . count($liseDersleri) . ' ders');
        
        $this->command->info('🎉 Toplam ' . (count($ilkokulDersleri) + count($ortaokulDersleri) + count($liseDersleri)) . ' ders şablonu oluşturuldu!');
    }
}

