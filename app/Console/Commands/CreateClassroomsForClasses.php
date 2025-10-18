<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassRoom;

class CreateClassroomsForClasses extends Command
{
    protected $signature = 'classrooms:create-for-classes';
    protected $description = 'Mevcut tüm sınıflar için derslik oluştur';

    public function handle()
    {
        $this->info('🏢 Mevcut sınıflar için derslik oluşturuluyor...');
        
        $classes = ClassRoom::with('school')->get();
        
        if ($classes->count() === 0) {
            $this->warn('⚠️ Hiç sınıf bulunamadı!');
            return 0;
        }
        
        $this->info("📊 Toplam {$classes->count()} sınıf bulundu.");
        $this->newLine();
        
        $createdCount = 0;
        $skippedCount = 0;
        
        $progressBar = $this->output->createProgressBar($classes->count());
        $progressBar->start();
        
        foreach ($classes as $class) {
            // Bu sınıf için zaten derslik var mı kontrol et
            $existingClassroom = \App\Models\Classroom::where('school_id', $class->school_id)
                ->where('name', $class->name . ' Dersliği')
                ->first();
            
            if ($existingClassroom) {
                $skippedCount++;
            } else {
                try {
                    \App\Models\Classroom::create([
                        'school_id' => $class->school_id,
                        'name' => $class->name . ' Dersliği',
                        'code' => $class->name,
                        'type' => 'classroom',
                        'capacity' => $class->capacity ?? 30,
                        'current_occupancy' => 0,
                        'is_active' => true
                    ]);
                    $createdCount++;
                } catch (\Exception $e) {
                    $this->error("❌ Hata ({$class->name}): " . $e->getMessage());
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Özet
        $this->info('📈 Özet:');
        $this->line("✅ Oluşturulan derslikler: <fg=green>{$createdCount}</>");
        $this->line("⏭️ Atlanan (zaten mevcut): <fg=yellow>{$skippedCount}</>");
        $this->line("📊 Toplam sınıf: <fg=cyan>{$classes->count()}</>");
        
        if ($createdCount > 0) {
            $this->newLine();
            $this->info("✅ Başarılı! {$createdCount} yeni derslik oluşturuldu.");
        }
        
        return 0;
    }
}
