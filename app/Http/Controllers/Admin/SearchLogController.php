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
use App\Models\SearchHistory;
class SearchLogController extends Controller
{


    public function index()
    {

        CommonController::addToLog('searchLogView');

        $pers = SearchHistory::latest()->get();
        return view('admin.searchLog.index',compact('pers'));
    }
    public function data(Request $request)
    {
         $query = SearchHistory::query();

    // Search by name, phone, or email
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('query', 'like', "%{$request->search}%");
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
            'query' => $user->query,
            'userName' => \App\Models\User::where('id', $user->user_id)->value('name'),

        ];
    });

    return response()->json([
        'data' => $data,
        'total' => $paginated->total(),
        'current_page' => $paginated->currentPage(),
        'last_page' => $paginated->lastPage(),
        'can_edit' => Auth::user()->can('searchLogUpdate'),
        'can_show' => Auth::user()->can('searchLogView'),
        'can_delete' => Auth::user()->can('searchLogDelete'),
    ]);
    }

    public function destroy($id)
        {

            SearchHistory::where('id', $id)->delete();
            CommonController::addToLog('searchLogDelete');
            //return redirect()->back()->with('error','Deleted successfully!');
              return response()->json(['message' => 'search log deleted successfully']);
        }
}
