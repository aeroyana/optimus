<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userList(){
        if(Auth::user()->type != 'admin'){
            return "forbidden";
        }
        $data = User::all();
        return view('userlist',compact('data'));
    }

    public function addUserIndex(){
        if(Auth::user()->type != 'admin'){
            return "forbidden";
        }
        return view('adduser');
    }

    public function userAdd(Request $request){
        if(User::where('email',$request->email)->exists()){
            return "Email already exists";
        }
        try{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'type'=>'user',
            ]);
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function userEdit($id){
        if(Auth::user()->type != 'admin'){
            return "forbidden";
        }
        $name = User::where('id',$id)->value('name');
        $email = User::where('id',$id)->value('email');
        return view('useredit',compact('name','email','id'));
    }

    public function userUpdate(Request $request){
        if($request->name == "" || $request->email == ""){
            return "Name and Email field can't be empty";
        }
        if($request->password == ""){
            try{
                User::where('id',$request->id)->update([
                   'name'=>$request->name,
                    'email'=>$request->email
                ]);
                return "success";
            }
            catch (\Exception $e){
                return $e->getMessage();
            }
        }
        else{
            try{
                User::where('id',$request->id)->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>bcrypt($request->password)
                ]);
                return "success";
            }
            catch (\Exception $e){
                return $e->getMessage();
            }
        }
    }

    public function userDel(Request $request){
        $userType = User::where('id',$request->id)->value('type');
        if($userType == 'admin'){
            return "You can't delete admin account";
        }

        try{
            User::where('id',$request->id)->delete();
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
