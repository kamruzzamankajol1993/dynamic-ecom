<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SupplierExport implements FromView
{
    public function view(): View
    {
       $suppliers = Supplier::latest()->get();
       return view('admin.supplier._partial.excelSheet', ['suppliers' => $suppliers]);
    }
}