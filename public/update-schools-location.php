<?php
header('Content-Type: text/html; charset=utf-8');

// Laravel bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

echo "<h2>📍 Mevcut Okulları İl/İlçe ile Güncelleme</h2>";

try {
    $cities = \App\Models\City::with('districts')->get();
    $schools = \App\Models\School::whereNull('city_id')->get();
    
    echo "<p>Güncellenecek okul sayısı: <strong>{$schools->count()}</strong></p>";
    echo "<p>Mevcut şehir sayısı: <strong>{$cities->count()}</strong></p>";
    
    $updated = 0;
    
    foreach ($schools as $school) {
        // Rastgele bir şehir seç
        $randomCity = $cities->random();
        
        // O şehirden rastgele bir ilçe seç
        $randomDistrict = $randomCity->districts->random();
        
        // Okulu güncelle
        $school->update([
            'city_id' => $randomCity->id,
            'district_id' => $randomDistrict->id
        ]);
        
        echo "<div style='background: #e8f5e8; padding: 5px; margin: 2px; border-radius: 3px;'>";
        echo "✅ <strong>{$school->name}</strong> → {$randomCity->name} / {$randomDistrict->name}";
        echo "</div>";
        
        $updated++;
    }
    
    echo "<hr>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🎉 İşlem Tamamlandı!</h3>";
    echo "<p><strong>{$updated}</strong> okul başarıyla güncellendi.</p>";
    echo "</div>";
    
    // İstatistikler
    echo "<h3>📊 İl Bazında Okul Dağılımı:</h3>";
    $cityStats = \App\Models\City::withCount('schools')->orderBy('schools_count', 'desc')->get();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>İl</th><th>Okul Sayısı</th></tr>";
    
    foreach ($cityStats as $city) {
        echo "<tr>";
        echo "<td>{$city->name}</td>";
        echo "<td style='text-align: center;'><strong>{$city->schools_count}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;'>";
    echo "<strong>Hata:</strong> " . $e->getMessage();
    echo "</div>";
}
?>