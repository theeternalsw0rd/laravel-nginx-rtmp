<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;

class UserController extends BaseController
{
    use ValidatesRequests;

    public function __construct()
    {
        if(User::count() > 0)
        {
            $this->middleware('auth');
        }
    }

    public function updatePassword(Request $request, $id)
    {
        if($request->isMethod('post'))
        {
            $this->validate($request, [
                'old_password' => 'required|min:8',
                'password' => 'required|min:8|confirmed'
            ]);
            $old_password = $request->input('old_password');
            $user = User::findOrFail($id);
            if(!Hash::check($old_password, $user->password)) {
                return redirect()->back()->withErrors('Current Password did not match records.');
            }
            $password = Hash::make($request->input('password'));
            $user->password = $password;
            if($user->save()) {
                return redirect('/');
            }
            else {
                return redirect()->back();
            }
        }
        if($request->isMethod('get'))
        {
            return view('user.update')->with('title', 'Update Password')->with('id', $id);
        }
    }

    public function create(Request $request)
    {
        if($request->isMethod('post'))
        {
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:8|confirmed',
            ]);
            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            if($user->save()) {
                return redirect('/');
            }
            else {
                return redirect()->back();
            }
        }
        if($request->isMethod('get'))
        {
            return view('user.create')->with('title', 'Create User');
        }
    }
}
