<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\TeachersTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class TemplateController extends Controller
{
    /**
     * Öğretmen toplu yükleme Excel şablonu indir
     */
    public function downloadTeacherTemplate()
    {
        $filePath = storage_path('templates/ogretmen_sablonu.xlsx');
        $fileName = 'ogretmen_sablonu.xlsx';
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Şablon dosyası bulunamadı'], 404);
        }
        
        return response()->download($filePath, $fileName);
    }
}
