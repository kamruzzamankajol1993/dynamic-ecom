<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\SystemInformation;
class PanelExport implements FromView
{
    
     public function view(): View
    {

       // dd(SystemInformation::latest()->get());
        return view('admin.panelSettingInfo._partial.excelSheet', [
            'systemInformation' => SystemInformation::latest()->get(),
        ]);
    }
}
