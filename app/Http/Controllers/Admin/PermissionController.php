<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use App\Exports\PermissionExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
class PermissionController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:permissionView|permissionAdd|permissionUpdate|permissionDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:permissionAdd', ['only' => ['create','store']]);
         $this->middleware('permission:permissionUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:permissionDelete', ['only' => ['destroy']]);
    }

     public function downloadPermissionExcel()
    {
        return Excel::download(new PermissionExport, 'permission.xlsx');
    }

    public function downloadPermissionPdf()
{

     $permissionListall = Permission::select('group_name')->groupBy('group_name')->get();

    $html = view('admin.permission._partial.pdfSheet', ['permissionListall' => $permissionListall ])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'D'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="designationList.pdf"');
}

     public function data(Request $request)
    {
        $query = DB::table('permissions')
        ->select('group_name', DB::raw('MIN(id) as first_permission_id'))
        ->groupBy('group_name');

    // Search
    if ($request->filled('search')) {
        $query->having('group_name', 'like', '%' . $request->search . '%');
    }

    // Sorting
    $sort = $request->get('sort', 'group_name');
    $direction = $request->get('direction', 'asc');
    $query->orderBy($sort === 'group_name' ? 'group_name' : 'first_permission_id', $direction);

    // Pagination
    $perPage = $request->get('perPage', 10);
    $paginated = $query->paginate($perPage);

    // Transform each item
    $mapped = $paginated->getCollection()->map(function ($item) {
        $item->permissions = DB::table('permissions')
            ->where('group_name', $item->group_name)
            ->select('id', 'name')
            ->get();
        return $item;
    });

    // Replace the collection
    $paginated->setCollection($mapped);

    return response()->json([
        'data' => $paginated->items(), // Now this is defined
        'total' => $paginated->total(),
        'current_page' => $paginated->currentPage(),
        'last_page' => $paginated->lastPage(),
        'can_edit' => Auth::user()->can('permissionUpdate'),
        'can_delete' => Auth::user()->can('permissionDelete'),
    ]);
    }


    public function index(): View
    {

        CommonController::addToLog('permissionView');

        $pers = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return view('admin.permission.index',compact('pers'));
    }


    public function edit($id): View
    {

        CommonController::addToLog('permissionedit');

        $pers = DB::table('permissions')->where('id',$id)->value('group_name');
        $persEdit = DB::table('permissions')->where('group_name',$pers)->get();
        return view('admin.permission.edit',compact('pers','persEdit'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name.*' => 'required|string',
            'group_name' => 'required|string',
        ]);


        CommonController::addToLog('permissionStore');


                $number=count($request->name);

                if($number >0){
                    for($i=0;$i<$number;$i++){
                        $data=array([
                            'name'=>$request->name[$i],
                            'guard_name'=>'web',
                            'group_name'=>$request->group_name
                        ]);

                      Permission::insert($data);
                    }


                return redirect()->back()->with('success','Created successfully!');

                }
        }



        public function update(Request $request,$id)
        {

          
            Permission::where('group_name', $request->group_name)->delete();

            $number=count($request->name);

            if($number >0){
                for($i=0;$i<$number;$i++){
                    $data=array([
                        'name'=>$request->name[$i],
                        'guard_name'=>'web',
                        'group_name'=>$request->group_name
                    ]);

                  Permission::insert($data);
                }

            }
            CommonController::addToLog('permissionUpdate');
            return redirect()->route('permissions.index')->with('info','Updated successfully!');
        }



        public function destroy($id)
        {

            $getGroupName = DB::table('permissions')
            ->where('id',$id)
            ->value('group_name');

            Permission::where('group_name', $getGroupName)->delete();

            CommonController::addToLog('permissionDelete');

            //return redirect()->back()->with('error','Deleted successfully!');

            return response()->json(['message' => 'permissions deleted successfully']);
        }
}
