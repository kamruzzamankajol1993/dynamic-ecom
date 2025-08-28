<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemInformation;
use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Exports\PanelExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf; 
class SystemInformationController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:panelSettingView|panelSettingAdd|panelSettingUpdate|panelSettingDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:panelSettingAdd', ['only' => ['create','store']]);
         $this->middleware('permission:panelSettingUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:panelSettingDelete', ['only' => ['destroy']]);
    }


       public function downloadSystemInformationExcel()
    {
        return Excel::download(new PanelExport, 'panelList.xlsx');
    }

    public function downloadSystemInformationPdf()
{

     $systemInformation = SystemInformation::latest()->get();

    $html = view('admin.panelSettingInfo._partial.pdfSheet', ['systemInformation' => $systemInformation ])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'D'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="designationList.pdf"');
}



    public function data(Request $request)
{

    if(Auth::user()->id == 1){
    $query = SystemInformation::query();
    }else{
       $query = SystemInformation::query()->where('branch_id',Auth::user()->branch_id); 
    }

    // Search by name, phone, or email
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('ins_name', 'like', "%{$request->search}%")
              ->orWhere('phone', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%");
        });
    }

    $sort = $request->get('sort', 'id');
    $direction = $request->get('direction', 'desc');
    $query->orderBy('id', 'desc');

    $perPage = $request->get('perPage', 10);
    $paginated = $query->paginate($perPage);

    // Optional: eager load branch name
    $data = $paginated->getCollection()->map(function ($item) {
        $item->branch_name = \App\Models\Branch::where('id', $item->branch_id)->value('name');
        return $item;
    });

    return response()->json([
        'data' => $data,
        'total' => $paginated->total(),
        'current_page' => $paginated->currentPage(),
        'last_page' => $paginated->lastPage(),
        'can_edit' => Auth::user()->can('panelSettingUpdate'),
        'can_delete' => Auth::user()->can('panelSettingDelete'),
    ]);
}

    public function index()
    {
        try{

            if(Auth::user()->id == 1){
                $panelSettingInfo = SystemInformation::latest()->get();

            }else{
$panelSettingInfo = SystemInformation::where('branch_id',Auth::user()->branch_id)->latest()->get();
            }

            

            CommonController::addToLog('panelSettingView');

            return view('admin.panelSettingInfo.panelList',compact('panelSettingInfo'));

        } catch (\Exception $e) {
    
            return redirect()->route('error_500');
           
        }
    }


    public function create()
    {

        CommonController::addToLog('panelSettingAdd');
        try{

        $branchInfo = Branch::latest()->get();

        return view('admin.panelSettingInfo.create',compact('branchInfo'));

        } catch (\Exception $e) {
        // DB::rollBack();
            return redirect()->route('error_500');
        }
    }


    public function edit($id)
    {

        CommonController::addToLog('panelSettingUpdate');
        try{

       

        $branchInfo = Branch::latest()->get();

           if(Auth::user()->id == 1){
 $panelSettingInfo = SystemInformation::where('id',$id)->first();
           }else{
        $panelSettingInfo = SystemInformation::where('branch_id',Auth::user()->branch_id)->where('id',$id)->first();
           }
        return view('admin.panelSettingInfo.edit',compact('branchInfo','panelSettingInfo'));

        } catch (\Exception $e) {
        // DB::rollBack();
            return redirect()->route('error_500');
        }
    }

    public function store(Request $request)
    {


       // dd($request->all());


                $request->validate([
           
                    'ins_name' => 'required|string',
                    'phone' => 'required|string',
                    'address' => 'required|string',
                    'email' => 'required|string',
                    'logo' => 'required|file',
                    'icon' => 'required|file',
                ]);


                $time_dy = time().date("Ymd");

                CommonController::addToLog('panelSettingStore');

                $systemInformation =  new SystemInformation();
                $systemInformation->branch_id = $request->branch_id;
                $systemInformation->ins_name = $request->ins_name;
                $systemInformation->email = $request->email;
                $systemInformation->phone = $request->phone;
                $systemInformation->main_url = $request->main_url;
                $systemInformation->address = $request->address;
                $systemInformation->keyword = $request->keyword;
                $systemInformation->description	= $request->description;
                $systemInformation->develop_by = $request->develop_by;
                $systemInformation->tax	= $request->tax;
                $systemInformation->charge = $request->charge;
                
                if ($request->hasfile('logo')) {


                    $productImage = $request->file('logo');
                    $imageName = 'logo'.$time_dy.$productImage->getClientOriginalName();
                    $directory = 'public/uploads/';
                    $imageUrl = $directory.$imageName;

                    $img=Image::read($productImage);
                        // ->resize(140,50);
                    $img->save($imageUrl);

                    $systemInformation->logo =  'public/uploads/'.$imageName;

                }

                 if ($request->hasfile('black_logo')) {


                    $productImage = $request->file('black_logo');
                    $imageName = 'black_logo'.$time_dy.$productImage->getClientOriginalName();
                    $directory = 'public/uploads/';
                    $imageUrl = $directory.$imageName;

                    $img=Image::read($productImage);
                        // ->resize(140,50);
                    $img->save($imageUrl);

                    $systemInformation->black_logo =  'public/uploads/'.$imageName;

                }
                if ($request->hasfile('icon')) {


                    $productImage = $request->file('icon');
                    $imageName = 'icon'.$time_dy.$productImage->getClientOriginalName();
                    $directory = 'public/uploads/';
                    $imageUrl = $directory.$imageName;

                    $img=Image::read($productImage)->resize(50,50);
                    $img->save($imageUrl);

                    $systemInformation->icon =  'public/uploads/'.$imageName;

                }
                $systemInformation->save();


                return redirect()->route('systemInformation.index')->with('success','Added Succesfully');




    }


    public function update(Request $request,$id)
    {



               try{
                DB::beginTransaction();


        $time_dy = time().date("Ymd");

        $request->validate([
            'ins_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string',
        ]);

        CommonController::addToLog('panelSettingUpdate');

        $systemInformation = SystemInformation::find($id);
        $systemInformation->branch_id = $request->branch_id;
        $systemInformation->ins_name = $request->ins_name;
        $systemInformation->email = $request->email;
        $systemInformation->phone = $request->phone;
        $systemInformation->main_url = $request->main_url;
        $systemInformation->address = $request->address;
        $systemInformation->keyword = $request->keyword;
        $systemInformation->description	= $request->description;
        $systemInformation->develop_by = $request->develop_by;
        $systemInformation->tax	= $request->tax;
        
                $systemInformation->charge = $request->charge;
        if ($request->hasfile('logo')) {


            $productImage = $request->file('logo');
            $imageName = 'logo'.$time_dy.$productImage->getClientOriginalName();
            $directory = 'public/uploads/';
            $imageUrl = $directory.$imageName;

            $img=Image::read($productImage);
                        // ->resize(140,50);
            $img->save($imageUrl);

            $systemInformation->logo =  'public/uploads/'.$imageName;

        }
        if ($request->hasfile('black_logo')) {


                    $productImage = $request->file('black_logo');
                    $imageName = 'black_logo'.$time_dy.$productImage->getClientOriginalName();
                    $directory = 'public/uploads/';
                    $imageUrl = $directory.$imageName;

                    $img=Image::read($productImage);
                        // ->resize(140,50);
                    $img->save($imageUrl);

                    $systemInformation->black_logo =  'public/uploads/'.$imageName;

                }
        if ($request->hasfile('icon')) {


            $productImage = $request->file('icon');
            $imageName = 'icon'.$time_dy.$productImage->getClientOriginalName();
            $directory = 'public/uploads/';
            $imageUrl = $directory.$imageName;

            $img=Image::read($productImage)->resize(50,50);
            $img->save($imageUrl);

            $systemInformation->icon =  'public/uploads/'.$imageName;

        }
        $systemInformation->save();
        DB::commit();
    return redirect()->route('systemInformation.index')->with('success','Updated Succesfully');

} catch (\Exception $e) {
    DB::rollBack();
    return $e->getMessage();
}


    }

    public function destroy($id)
    {

        SystemInformation::where('id', $id)->delete();
        CommonController::addToLog('SystemInformation Delete');
       // return redirect()->back()->with('error','Deleted successfully!');

       return response()->json(['message' => 'panel deleted successfully']);
    }
}
