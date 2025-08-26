<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;
class TextController extends Controller
{
    public function textMessage(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'msg' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Here you can add your logic to store the data, send an email, etc.
            // For example:
             Message::create($request->all());

            return response()->json(['success' => 'Your message has been sent successfully!']);
        }

        // This part will handle non-AJAX requests if any.
        return abort(404);
    }
}
