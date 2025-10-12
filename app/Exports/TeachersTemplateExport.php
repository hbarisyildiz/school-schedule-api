<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TeachersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Örnek veriler
        return [
            ['Ahmet Yılmaz', 'ahmet.yilmaz@okul.com', 'Matematik', 'AHMYIL', '5551234567'],
            ['Ayşe Demir', 'ayse.demir@okul.com', 'Türkçe', 'AYSDEM', '5551234568'],
            ['Mehmet Kaya', 'mehmet.kaya@okul.com', 'İngilizce', 'MEHKAY', '5551234569'],
            ['Fatma Çelik', 'fatma.celik@okul.com', 'Fen Bilimleri', 'FATCEL', '5551234570'],
            ['Ali Öztürk', 'ali.ozturk@okul.com', 'Sosyal Bilgiler', 'ALIOZ', '5551234571'],
        ];
    }

    public function headings(): array
    {
        return [
            'ad_soyad',
            'email',
            'brans',
            'kisa_ad',
            'telefon'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Başlık satırı stili
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
            ]
        ]);

        // Örnek veriler stili (açık mavi)
        $sheet->getStyle('A2:E6')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F4F8']
            ]
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 20,
            'D' => 18,
            'E' => 18,
        ];
    }
}
