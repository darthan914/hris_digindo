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
use Validator;

use Datatables;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
    	return view('backend.user.index');
	}

    public function datatables(Request $request)
    {
        $index = User::select('*');

        $index = $index->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-user') && $this->usergrant($index->id, 'all-user'))
            {
                $html .= '
                    <a href="' . route('admin.user.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if (Auth::user()->can('active-user') && $this->usergrant($index->id, 'all-user') && $index->id != Auth::id())
            {
                if($index->active)
                {
                    $html .= '
                        <button class="btn btn-xs btn-dark inactive-user" data-toggle="modal" data-target="#inactive-user" data-id="'.$index->id.'"><i class="fa fa-eye-slash"></i></button>
                    ';
                }
                else
                {
                    $html .= '
                        <button class="btn btn-xs btn-info active-user" data-toggle="modal" data-target="#active-user" data-id="'.$index->id.'"><i class="fa fa-eye"></i></button>
                    ';
                }
            }

            if(Auth::user()->can('access-user') && $this->usergrant($index->id, 'all-user'))
            {
                $html .= '
                    <a href="' . route('admin.user.access', ['id' => $index->id]) . '" class="btn btn-xs btn-default"><i class="fa fa-key"></i></a>
                ';
            }


            if(Auth::user()->can('delete-user') && $this->usergrant($index->id, 'all-user') && $index->id != Auth::id())
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-user" data-toggle="modal" data-target="#delete-user" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            if(Auth::user()->can('impersonate-user') && $this->usergrant($index->id, 'all-user') && $index->id != Auth::id())
            {
                $html .= '
                    <button class="btn btn-xs btn-info impersonate-user" data-toggle="modal" data-target="#impersonate-user" data-id="'.$index->id.'"><i class="fa fa-sign-in"></i></button>
                ';
            }
                
            return $html;
        });

        $datatables->editColumn('active', function ($index) {
            $html = '';
            if($index->active)
            {
                $html .= '
                    <span class="label label-info">Aktif</span>
                ';
            }
            else
            {
                $html .= '
                    <span class="label label-default">Tidak Aktif</span>
                ';
            }
            return $html;
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            if($index->id != Auth::id())
            {
                $html .= '
                    <input type="checkbox" class="check" value="' . $index->id . '" name="id[]" form="action">
                ';
            }

            return $html;
        });

        $datatables->editColumn('id_role', function ($index) {
            return $index->getRole->name;
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }

	public function create()
    {
        $key = User::keypermission();
    	return view('backend.user.create', compact('key'));
    }

	public function store(Request $request)
    {
        $permission = $request->permission ? implode($request->permission, ', ') : '';

        $message = [
            'name.required' => 'Data harus diisi.',
            'username.required' => 'Data harus diisi.',
            'username.unique' => 'Data sudah ada.',
            'email.required' => 'Data harus diisi.',
            'email.unique' => 'Data sudah ada.',
            'password.required' => 'Data harus diisi.',
            'password.confirmed' => 'Password tidak sama.',
            'password_user.required' => 'Data harus diisi.',
            'avatar.image' => 'File harus gambar.',
        ];

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'username'      => 'required|unique:users,username',
            'email'         => 'required|unique:users,email',
            'password'      => 'required|confirmed',
            'avatar'        => 'image',
        ], $message);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->password_user, Auth::user()->password)) {
                $validator->errors()->add('password_user', 'Password salah');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


    	$index = new User;

        $index->name     = $request->name;
        $index->username = $request->username;
        $index->email    = $request->email;
        $index->password = bcrypt($request->password);
        $index->actor    = Auth::id();
        $index->access   = 0;

        if(Auth::user()->can('access-user'))
        {
            $index->permission = $permission;
        }

        if(Auth::user()->can('active-user'))
        {
            $index->active = isset($request->active) ? 1 : 0;
        }

        if ($request->hasFile('avatar'))
        {
            $pathSource = 'upload/user/avatar/';
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

    	return redirect()->route('admin.user')->with('success', 'Data berhasil ditambah');
    }

    public function edit($id)
    {
    	$index = User::find($id);

        if(!$this->usergrant($index->id, 'all-user'))
        {
            return redirect()->route('admin.user')->with('failed', 'Akses Ditolak');
        }

        $key   = User::keypermission();

    	return view('backend.user.edit')->with(compact('index','key'));
    }

    public function update($id, Request $request)
    {
        $index = User::find($id);

        if(!$this->usergrant($index->id, 'all-user'))
        {
            return redirect()->route('admin.user')->with('failed', 'Akses Ditolak');
        }

        $permission = $request->permission ? implode($request->permission, ', ') : '';

    	$message = [
            'name.required' => 'Data harus diisi.',
            'username.required' => 'Data harus diisi.',
            'username.unique' => 'Data sudah ada.',
            'email.required' => 'Data harus diisi.',
            'email.unique' => 'Data sudah ada.',
            'password.confirmed' => 'Password tidak sama.',
            'password_user.required' => 'Data harus diisi.',
            'avatar.image' => 'File harus gambar.',
        ];

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'username'      => 'required|unique:users,username,'.$id,
            'email'         => 'required|unique:users,email,'.$id,
            'password'      => 'confirmed',
            'avatar'        => 'image',
        ], $message);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->password_user, Auth::user()->password)) {
                $validator->errors()->add('password_user', 'Password salah');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $index->name       = $request->name;
        $index->username   = $request->username;
        $index->email      = $request->email;
        $index->password   = $request->password != '' ? bcrypt($request->password) : $index->password;
        $index->actor      = Auth::id();

        if(Auth::user()->can('access-user'))
        {
            $index->permission = $permission;
        }

        if(Auth::user()->can('active-user'))
        {
            $index->active = isset($request->active) ? 1 : 0;
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
	            $pathSource = 'upload/user/avatar/';
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

    	return redirect()->route('admin.user')->with('success', 'Data berhasill diubah');
    }

    public function delete($id)
    {
    	if(Auth::id() == $id)
    	{
            Session::flash('warning', 'Akses ditolak');
    		return redirect()->route('admin.user');
    	}

    	User::destroy($id);

    	return redirect()->route('admin.user')->with('success', 'Data berhasil dihapus');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		User::destroy($request->id);
            return redirect()->route('admin.user')->with('success', 'Data berhasil dihapus');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = User::whereIn('id', $request->id)->update(['active' => 1]);
            return redirect()->route('admin.user')->with('success', 'Data berhasil diaktifkan');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = User::whereIn('id', $request->id)->update(['active' => 0]);
            return redirect()->route('admin.user')->with('success', 'Data berhasil tidak diaktifkan');
    	}
    }

    public function active(Request $request)
    {
        if(Auth::id() == $request->id)
        {
            Session::flash('warning', 'Akses ditolak');
            return redirect()->route('admin.user');
        }
        
        $index = User::find($request->id);

        if ($index->active == 0)
        {
            $index->active = 1;
            $index->save();
            return redirect()->back()->with('success', 'Data berhasil diaktifkan');
        } 
        else if ($index->active == 1)
        {
            $index->active = 0;
            $index->save();
            return redirect()->back()->with('success', 'Data berhasil tidak diaktifkan');
        }
    }

    public function access($id)
    {
        $index = User::find($id);
        $key = User::keypermission();

        return view('backend.user.access')->with(compact('index', 'key'));
    }

    public function accessUpdate($id, Request $request)
    {
        $index = User::find($id);

        $message = [
            'password.required' => 'Data harus diisi.',
        ];

        $validator = Validator::make($request->all(), [
            'password'        => 'required',
        ], $message);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->password, Auth::user()->password)) {
                $validator->errors()->add('password', 'Password salah');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $grant  = $request->grant ? implode($request->grant, ', ') : '';
        $denied = $request->denied ? implode($request->denied, ', ') : '';
        
        $index->grant  = $grant;
        $index->denied = $denied;
        
        $index->save();
        
        return redirect()->route('admin.user')->with('success', 'Data berhasil diubah');
    }

    public function impersonate(Request $request)
    {
        $message = [
            'password.required' => 'This field required.',
        ];

        $validator = Validator::make($request->all(), [
            'password'        => 'required',
        ], $message);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->password, Auth::user()->password)) {
                $validator->errors()->add('password', 'Password salah');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('impersonate-user-error', '');
        }

        $index = User::find($request->id);

        if(true)
        {
            Auth::user()->setImpersonating($index->id);

        }
        else
        {
            return redirect()->back()->with('failed', 'Tidak bisa mengambil akses');
        }


        return redirect()->route('backend.home')->with('success', 'Masuk sebagai '. $index->fullname);
    }

    public function leave()
    {
        Auth::user()->stopImpersonating();

        return redirect()->route('backend.user')->with('success', 'Kembali ke user sebelumnya');
    }
}
