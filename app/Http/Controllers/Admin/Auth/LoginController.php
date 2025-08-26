<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\CommonController;
class LoginController extends Controller
{
    public function viewLoginPage(){

        CommonController::addToLog('LoginPage View');
        return view('admin.auth.viewLoginPage');


    }

    public function showLinkRequestForm(){
        CommonController::addToLog('forgate password page View');
        return view('admin.auth.showLinkRequestForm');

    }
}
