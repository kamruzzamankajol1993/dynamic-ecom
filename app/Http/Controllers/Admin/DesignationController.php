<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemInformation;
use App\Models\Designation;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Exports\DesignationExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
class DesignationController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:designationView|designationAdd|designationUpdate|designationDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:designationAdd', ['only' => ['create','store']]);
         $this->middleware('permission:designationUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:designationDelete', ['only' => ['destroy']]);
    }


     public function downloadDesignationExcel()
    {
        return Excel::download(new DesignationExport, 'branch.xlsx');
    }

    public function downloadDesignationPdf()
{

    $designationList =  Designation::latest()->select('name')->get();

    $html = view('admin.designation._partial.pdfSheet', ['designationList' => $designationList])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'D'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="designationList.pdf"');
}


 public function data(Request $request)
    {
        $query = Designation::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like',$request->search . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy('name','asc');

        // Pagination
        $perPage = 10;
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
        ]);
    }

     public function show($id)
    {

        $user = Designation::where('id',$id)->first();
        return response()->json($user);
    }


    public function index(): View
    {

        CommonController::addToLog('designationView');

        $pers = DB::table('designations')->latest()->get();
        return view('admin.designation.index',compact('pers'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);


        
        Designation::create($request->all());
                    
        CommonController::addToLog('designationStore');

        return redirect()->back()->with('success','Created successfully!');

                
        }



        public function update(Request $request,$id)
        {

             $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

          
            $medicine = Designation::findOrFail($id);

            $input = $request->all();

            $medicine->fill($input)->save();
            CommonController::addToLog('designationUpdate');
            //return redirect()->back()->with('info','Updated successfully!');

            return response()->json(['message' => 'Designation updated successfully']);
        }



        public function destroy($id)
        {

            Designation::where('id', $id)->delete();
            CommonController::addToLog('designationDelete');
            //return redirect()->back()->with('error','Deleted successfully!');
              return response()->json(['message' => 'Designation deleted successfully']);
        }
}
