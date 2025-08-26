<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class PermissionExport implements FromView

{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function view(): View
    {
        return view('admin.permission._partial.excelSheet', [
            'permissionListall' => Permission::select('group_name')->groupBy('group_name')->get(),
        ]);
    }
}
