<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Temel Plan',
                'slug' => 'basic',
                'description' => 'Küçük okullar için temel özellikler',
                'monthly_price' => 99.00,
                'yearly_price' => 990.00,
                'max_teachers' => 20,
                'max_students' => 500,
                'max_classes' => 20,
                'features' => [
                    'Ders programı oluşturma',
                    'Öğretmen yönetimi',
                    'Sınıf yönetimi',
                    'Temel raporlama'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Standart Plan',
                'slug' => 'standard',
                'description' => 'Orta büyüklükteki okullar için gelişmiş özellikler',
                'monthly_price' => 199.00,
                'yearly_price' => 1990.00,
                'max_teachers' => 50,
                'max_students' => 1500,
                'max_classes' => 50,
                'features' => [
                    'Tüm temel özellikler',
                    'Gelişmiş raporlama',
                    'SMS bildirim',
                    'Veli paneli',
                    'Mobil uygulama'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'description' => 'Büyük okullar için sınırsız erişim',
                'monthly_price' => 399.00,
                'yearly_price' => 3990.00,
                'max_teachers' => null, // Sınırsız
                'max_students' => null, // Sınırsız
                'max_classes' => null,  // Sınırsız
                'features' => [
                    'Tüm özellikler',
                    'Sınırsız kullanıcı',
                    'API erişimi',
                    'Özel entegrasyonlar',
                    'Öncelikli destek',
                    'Özel eğitim'
                ],
                'is_active' => true
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
