<?php
    
    namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Designation;
use App\Models\Branch;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use App\Http\Controllers\Admin\CommonController;
class UserController extends Controller
{

      public function downloadUserExcel()
    {
        return Excel::download(new UserExport, 'userList.xlsx');
    }

    public function downloadUserPdf()
{

     $userList = User::where('id', '!=', 1)->where('user_type', 2)->latest()->get();

    $html = view('admin.users._partial.pdfSheet', ['userList' => $userList ])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'D'))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="userList.pdf"');
}


    public function data(Request $request)
{
    $query = User::query()->where('id', '!=', 1)->where('user_type', 2)->where('status',1);

    // Search by name, phone, or email
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('phone', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%");
        });
    }

    // Sorting
    $sort = $request->get('sort', 'id');
    $direction = $request->get('direction', 'desc');
     $query->orderBy('id','desc');

    // Pagination
    $perPage = $request->get('perPage', 10);
    $paginated = $query->paginate($perPage);

    // Transform each user record
    $data = $paginated->getCollection()->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'status' => $user->status,
            'viewpassword' => $user->viewpassword,
            'image' => $user->image,
            'branch_name' => \App\Models\Branch::where('id', $user->branch_id)->value('name'),
            'designation_name' => \App\Models\Designation::where('id', $user->designation_id)->value('name'),
            'roles' => $user->getRoleNames(), // from Spatie
        ];
    });

    return response()->json([
        'data' => $data,
        'total' => $paginated->total(),
        'current_page' => $paginated->currentPage(),
        'last_page' => $paginated->lastPage(),
        'can_edit' => Auth::user()->can('userUpdate'),
         'can_show' => Auth::user()->can('userView'),
        'can_delete' => Auth::user()->can('userDelete'),
    ]);
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {

        CommonController::addToLog('user page view');


                $data = User::where('id','!=',1)->where('user_type', 2)->latest()->get();

        return view('admin.users.index',compact('data'));
    }

    

    public function activeOrInActiveUser($status,$id): RedirectResponse
    {

        //dd($status);

        CommonController::addToLog('user active or inactive');

        $user = User::find($id);
        $user->status = $status;
        $user->save();
    
        return redirect()->route('users.index')
                        ->with('success','User Updated successfully');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        CommonController::addToLog('user create');

        $roles = Role::pluck('name','name')->all();
        $designationList = Designation::latest()->get();
       

            $branchList = Branch::latest()->get();

       

        
        
        return view('admin.users.create',compact('roles','designationList','branchList'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {

        //dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        CommonController::addToLog('user store');

        $time_dy = time().date("Ymd");
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['viewpassword'] = $input['password'];

        if ($request->hasfile('image')) {


            $productImage = $request->file('image');
            $imageName = 'profileImage'.$time_dy.$productImage->getClientOriginalName();
            $directory = 'public/uploads/';
            $imageUrl = $directory.$imageName;

            $img=Image::read($productImage)->resize(100,100);
            $img->save($imageUrl);

            $userImage =  'public/uploads/'.$imageName;

        }else{


            $userImage = null;


        }
        $input['image'] = $userImage;
        $input['status'] = 1;
        $input['user_type'] = 2;
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('admin.users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {

        CommonController::addToLog('user edit');

        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $designationList = Designation::latest()->get();
    

            $branchList = Branch::latest()->get();

       
        return view('admin.users.edit',compact('user','roles','userRole','designationList','branchList'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        CommonController::addToLog('user update');
    
        $input = $request->all();

        //dd($input);
$time_dy = time().date("Ymd");


        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
             $input['viewpassword'] = $request->password;
        }else{
            $input = Arr::except($input,array('password'));    
             $input['viewpassword'] = null;
        }


        if ($request->hasfile('image')) {


            $productImage = $request->file('image');
            $imageName = 'profileImage'.$time_dy.$productImage->getClientOriginalName();
            $directory = 'public/uploads/';
            $imageUrl = $directory.$imageName;

            $img=Image::read($productImage)->resize(100,100);
            $img->save($imageUrl);

            $userImage =  'public/uploads/'.$imageName;

        }else{


            $userImage = User::where('id',$id)->value('image');


        }
        $input['image'] = $userImage;
        $input['status'] = 1;
         $input['user_type'] = 2;
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {

        CommonController::addToLog('user delete');

        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}
