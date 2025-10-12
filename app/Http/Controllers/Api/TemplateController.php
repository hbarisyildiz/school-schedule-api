<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class TemplateController extends Controller
{
    /**
     * Öğretmen toplu yükleme Excel şablonu indir
     */
    public function downloadTeacherTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Başlık satırı
        $headers = ['ad_soyad', 'email', 'brans', 'kisa_ad', 'telefon'];
        $headerLabels = ['Ad Soyad *', 'Email *', 'Branş *', 'Kısa Ad (Opsiyonel)', 'Telefon (Opsiyonel)'];
        
        // Başlıkları yaz
        $col = 'A';
        foreach ($headerLabels as $index => $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Başlık stilini ayarla
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498DB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '2C3E50']
                ]
            ]
        ]);
        
        // Örnek veriler
        $examples = [
            ['Ahmet Yılmaz', 'ahmet.yilmaz@okul.com', 'Matematik', 'AHMYIL', '5551234567'],
            ['Ayşe Demir', 'ayse.demir@okul.com', 'Türkçe', 'AYSDEM', '5551234568'],
            ['Mehmet Kaya', 'mehmet.kaya@okul.com', 'İngilizce', 'MEHKAY', '5551234569'],
            ['Fatma Çelik', 'fatma.celik@okul.com', 'Fen Bilimleri', 'FATCEL', '5551234570'],
            ['Ali Öztürk', 'ali.ozturk@okul.com', 'Sosyal Bilgiler', 'ALIOZ', '5551234571'],
        ];
        
        $row = 2;
        foreach ($examples as $example) {
            $col = 'A';
            foreach ($example as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Örnek verilerin stilini ayarla (açık mavi arka plan)
        $sheet->getStyle('A2:E6')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F4F8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'BDC3C7']
                ]
            ]
        ]);
        
        // Kolon genişliklerini ayarla
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        
        // Satır yüksekliklerini ayarla
        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i <= 6; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
        
        // Bilgilendirme notları ekle (alt satırlara)
        $sheet->setCellValue('A8', '📝 KULLANIM TALİMATLARI:');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(11);
        
        $instructions = [
            '• Örnek verileri silin ve kendi verilerinizi girin',
            '• (*) işaretli alanlar zorunludur',
            '• Email adresleri benzersiz olmalıdır',
            '• Kısa ad boş bırakılırsa otomatik oluşturulur (max 6 karakter)',
            '• Varsayılan şifre: 123456 (Öğretmenler ilk girişte değiştirmelidir)',
            '• Maksimum dosya boyutu: 2MB'
        ];
        
        $row = 9;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction);
            $sheet->getStyle('A' . $row)->getFont()->setSize(10);
            $row++;
        }
        
        // Merge cells for instructions
        foreach (range(9, 14) as $r) {
            $sheet->mergeCells("A{$r}:E{$r}");
        }
        
        // Excel dosyasını response olarak döndür
        $fileName = 'ogretmen_sablonu_' . date('Y-m-d') . '.xlsx';
        
        // Temporary file'a yaz
        $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp_file);
        
        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
