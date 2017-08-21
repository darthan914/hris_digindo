<?php

namespace App\Http\Controllers\Backend;

use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		$index = User::all();

    	return view('backend.user.index')->with(compact('index'));
	}

	public function create()
    {
    	if(Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	return view('backend.user.create');
    }

	public function store(Request $request)
    {
    	if(Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	$this->validate($request, [
            'name'          => 'required',
            'username'      => 'required|unique:users,username',
            'email'         => 'required|unique:users,email',
            'password'      => 'required|confirmed',
            'user_password' => 'required|oldpassword',
            'avatar'        => 'image',
        ]);

        // return $request->all();


    	$index = new User;

        $index->name     = $request->name;
        $index->username = $request->username;
        $index->email    = $request->email;
        $index->password = bcrypt($request->password);
        $index->active   = $request->active;
        $index->actor    = Auth::id();
        $index->access   = $request->access;
        if ($request->hasFile('avatar'))
        {
            $pathSource = 'amadeo/source/admin/avatar/';
            $image      = $request->file('avatar');
            $filename   = time() . '-' . $image->getClientOriginalName();
            if($image->move($pathSource, $filename))
            {
                if($index->avatar != '')
                {
                    File::delete($index->avatar);
                }
                $index->avatar = $pathSource . $filename;
            }
        }
        
        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.user');
    }

    public function edit($id)
    {
    	if($id != Auth::id() && Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	$index = User::find($id);
    	return view('backend.user.edit')->with(compact('index'));
    }

    public function update($id, Request $request)
    {
    	if($id != Auth::id() && Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	$this->validate($request, [
            'name'         => 'required',
            'username'     => 'required|unique:users,username,'.$id,
            'email'        => 'required|unique:users,email,'.$id,
            'password'     => 'confirmed',
            'user_password' => 'required|oldpassword',
            'avatar'       => 'image',
        ]);

    	$index = User::find($id);

        $index->name     = $request->name;
        $index->username = $request->username;
        $index->email    = $request->email;
        $index->password = $request->password != '' ? bcrypt($request->password) : $index->password;
        $index->actor    = Auth::id();
        if(isset($request->access))
        {
        	$index->active   = $request->active;
        	$index->access   = $request->access;
        }

        if (isset($request->remove_avatar))
        {
            if($index->avatar != '')
            {
                File::delete($index->avatar);
                $index->avatar = '';
            }
        }
        else
        {
        	if ($request->hasFile('avatar'))
	        {
	            $pathSource = 'amadeo/source/admin/avatar/';
	            $image      = $request->file('avatar');
	            $filename   = time() . '-' . $image->getClientOriginalName();
	            if($image->move($pathSource, $filename))
	            {
	                if($index->avatar != '')
	                {
	                    File::delete($index->avatar);
	                }
	                $index->avatar = $pathSource . $filename;
	            }
	        }
        }
	        
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.user');
    }

    public function delete($id)
    {
    	if(Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	User::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.user');
    }

    public function action(Request $request)
    {
    	if(Auth::user()->access != 0)
    	{
            Session::flash('warning', 'Access Denied');
    		return redirect()->route('admin');
    	}

    	if($request->action == 'delete')
    	{
    		User::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = User::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = User::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.user');
    }

    public function status($id, $action)
    {
        $index = User::find($id);

        $index->active = $action;

        $index->save();

        if($action == 1)
        {
            Session::flash('success', 'Data Has Been Actived');
        }
        else
        {
            Session::flash('success', 'Data Has Been Inactived');
        }

        return redirect()->route('admin.user');
    }
}
