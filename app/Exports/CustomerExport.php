<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Customer;
class CustomerExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function view(): View
    {
       $customers = Customer::latest()->get();

    return view('admin.customer._partial.excelSheet', ['customers' => $customers ]);
    }
}
