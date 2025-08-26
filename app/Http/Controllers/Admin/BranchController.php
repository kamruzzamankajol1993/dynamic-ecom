<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemInformation;
use App\Models\Designation;
use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Exports\BranchExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
class BranchController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:branchView|branchAdd|branchUpdate|branchDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:branchAdd', ['only' => ['create','store']]);
         $this->middleware('permission:branchUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:branchDelete', ['only' => ['destroy']]);
    }


    public function downloadBranchExcel()
    {
        return Excel::download(new BranchExport, 'branch.xlsx');
    }

    public function downloadBranchPdf()
{

    $branchList =  Branch::latest()->select('name')->get();

    $html = view('admin.branch._partial.pdfSheet', ['branchList' => $branchList])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'D'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="branchList.pdf"');
}


    public function index(): View
    {

        CommonController::addToLog('branchView');
        $pers = Branch::where('id','!=',1)->latest()->get();
        return view('admin.branch.index',compact('pers'));
    }

    public function data(Request $request)
    {
        $query = Branch::query();

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

        $user = Branch::where('id',$id)->first();
        return response()->json($user);
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);


        CommonController::addToLog('branchStore');

        Branch::create($request->all());
                    
        return redirect()->back()->with('success','Created successfully!');

                
        }



        public function update(Request $request,$id)
        {

          $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);  

          
            $medicine = Branch::findOrFail($id);

            $input = $request->all();

            $medicine->fill($input)->save();

            CommonController::addToLog('branchUpdate');

             return response()->json(['message' => 'Branch updated successfully']);

            //return redirect()->back()->with('info','Updated successfully!');
        }



        public function destroy($id)
        {

            Branch::where('id', $id)->delete();
            CommonController::addToLog('branchDelete');

            //return redirect()->back()->with('error','Deleted successfully!');

            return response()->json(['message' => 'Branch deleted successfully']);

        }
}
