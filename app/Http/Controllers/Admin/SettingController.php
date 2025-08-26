<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Designation;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Mail;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;   
class SettingController extends Controller
{
    public function error_500(){

        return view('admin.error_500');
    }


    public function profileView(){

        return view('admin.profile.profileView');


    }

    public function profileSetting(){

        return view('admin.profile.profileSetting');

    }

    public function profileSettingUpdate(Request $request){

        $id=$request->id;
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
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
        
    
        $user = User::find($id);
        $user->update($input);


        CommonController::addToLog('profile update');

        return redirect()->back()
                        ->with('success','User updated successfully');


    }


    public function checkMailForPassword(Request $request){

        $email = $request->mainId;
        $checkMail = User::where('email',$email)->count();
        return $checkMail;
    }


    public function checkMailPost(Request $request){

        Mail::send('emails.passwordChangeEmail', ['id' =>$request->email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Password change Link');
        });


        return redirect()->route('newEmailNotify')->with('success','Email Send successfully!');


    }

    public function newEmailNotify(){

        return view('admin.setting.newEmailNotify');
    }

    public function accountPasswordChange($id){

        \LogActivity::addToLog('accountPasswordChange');


       $email = $id;
       return view('admin.setting.accountPasswordChange',compact('email'));

    }

    public function postPasswordChange(Request $request){

        $request->validate([

             'password' => 'required|min:8|confirmed',
        ],
        [

             'password.required' => 'Password is required',
        ]);

        CommonController::addToLog('password update');

        try{
            DB::beginTransaction();


        $adminId = User::where('email',$request->mainEmail)->value('id');

        DB::table('users')
        ->where('id', $adminId)

        ->update(array('password' =>Hash::make($request->password)));

        DB::commit();
        return redirect()->route('login')->with('success','Successfully Changed!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('error_500');
        }
    }
}
