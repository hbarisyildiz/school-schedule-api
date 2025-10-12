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
        $fileName = 'ogretmen_sablonu_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new TeachersTemplateExport, $fileName);
    }
}
