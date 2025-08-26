<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\Message;
use App\Models\Blog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;

class MessageController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::latest()->paginate(15);
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = Message::find($id);
        $message->delete();
        return redirect()->route('message.index')
                         ->with('success', 'Message deleted successfully.');
    }
}
