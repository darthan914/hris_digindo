<?php

namespace App\Http\Controllers\Backend;

use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['showLogin', 'login', 'formRegister', 'register', 'logout']]);
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.home');
        }
        return view('backend.auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'active' => 1])) {
            return redirect()->route('admin.home');
        } else {
            $query = User::where('username', $request->username);
            $find  = $query->first();
            $check = $query->count();

            if ($check) {
                if ($find->active != 1) {
                    Session::flash('failed', 'Your username not active please inform to team leader');
                    return redirect::back();
                } else {
                    Session::flash('failed', 'Invalid password');
                    return redirect::back();
                }
            } else {
                Session::flash('failed', 'Invalid Username');
                return redirect::back();
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin');
    }

    public function password()
    {
        return view('backend.auth.password');
    }

    public function sendPassword()
    {
        return view('backend.auth.password');
    }

    public function resetPassword($token)
    {
        return view('backend.auth.reset');
    }

    public function formRegister()
    {
        $check = User::where('username', 'admin')->count();

        if($check == 0)
        {
            $users = new User;

            $users->username = 'admin';
            $users->email    = 'admin@digindo.co.id';
            $users->password = bcrypt('digindo123');
            $users->name     = 'admin';
            $users->active   = 1;

            $users->save();
        }
        return redirect()->route('admin');

        // return view('backend.auth.register');
    }

    public function register(Request $request)
    {
        
        $users = new User;

        $this->validate($request, [
            'username' => 'required|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $users->username = $request->username;
        $users->email    = $request->email;
        $users->password = bcrypt($request->password);
        $users->name     = $request->name;

        $users->save();

        Session::flash('success', 'Data has been added, please info to owner website');
        return redirect()->route('admin');
    }

    public function active($id, Request $request)
    {
        $user = User::find($id);

        if ($request->active == -2) {
            $user->delete();

            Session::flash('success', 'Data has been deleted');
            return redirect::back();
        } else {
            $user->active = $request->active;
            $user->save();

            Session::flash('success', 'Data has been change');
            return redirect::back();
        }
    }
}
