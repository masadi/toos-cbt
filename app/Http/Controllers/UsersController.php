<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use File;
use Image;
use Carbon\Carbon;
class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('admin');
        $this->path = storage_path('app/public/uploads/profile');
        $this->dimensions = ['50', '200', '300'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $you = auth()->user();
        $users = User::all();
        return view('dashboard.admin.usersList', compact('users', 'you'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('dashboard.admin.userShow', compact( 'user' ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('dashboard.admin.userEditForm', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'       => 'required|min:1|max:256',
            'email'      => 'required|email|max:256'
        ]);
        $user = User::find($id);
        $user->name       = $request->input('name');
        $user->email      = $request->input('email');
        $user->save();
        $request->session()->flash('message', 'Successfully updated user');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if($user){
            $user->delete();
        }
        $request->session()->flash('success', 'Successfully delete user');
        return redirect()->route('users.index');
    }
    public function profile()
    {
        $user = auth()->user();
        return view('dashboard.admin.profile', compact( 'user' ));
    }
    public function update_data(Request $request, $id)
    {
        if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($this->path);
        }
        $validatedData = $request->validate([
            'current_password' => ['nullable', new MatchOldPassword],
            'new_password' => ['nullable','required_with:current_password','min:6'],
            'new_confirm_password' => ['same:new_password'],
            'name'       => ['required','min:1','max:256'],
            'email'      => ['required','email','max:256'],
            'file'     => ['nullable', 'image', 'mimes:jpg,png,jpeg']
        ]);
        $user = User::find($id);
        $user->name       = $request->input('name');
        $user->email      = $request->input('email');
        if($request->new_password){
            $user->password = Hash::make($request->input('new_password'));
            $user->logout   = TRUE;
        }
        if ($request->hasFile('file')) {
            $image_path = $this->path."/".$user->photo;
			if(File::exists($image_path)) {
				File::delete($image_path);
			}
            $file = $request->file('file');
            //$filename = time() . '-'.$user->username.'.' . $file->getClientOriginalExtension();
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            //$file->storeAs('public/uploads/profile', $filename);
            Image::make($file)->save($this->path . '/' . $fileName);
            foreach ($this->dimensions as $row) {
                $image_dimensions = $this->path."/".$row.'/'.$user->photo;
				if(File::exists($image_dimensions)) {
					File::delete($image_dimensions);
				}
                $canvas = Image::canvas($row, $row);
                $resizeImage  = Image::make($file)->resize($row, $row, function($constraint) {
                    $constraint->aspectRatio();
                });
                if (!File::isDirectory($this->path . '/' . $row)) {
                    File::makeDirectory($this->path . '/' . $row);
                }
                $canvas->insert($resizeImage, 'center');
                $canvas->save($this->path . '/' . $row . '/' . $fileName);
            }
            $user->photo = $fileName;
        }
        $user->save();
        $request->session()->flash('success', 'Profil Pengguna berhasil diperbaharui');
        return redirect()->route('users.profile');
    }
}
