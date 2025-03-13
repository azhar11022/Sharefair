<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RequestJoin extends Controller
{
    public function showPage($email,$name){
        return view('joinRegister',compact('email','name'));
    }
    public function addUser(Request $req){
        $valid =$req->validate([
                    'name'=>'required',
                    'email'=>'required|email',
                    'password'=>'required',
                    'confirm'=>'required'
                ]);
        $name = $req->name;
        $email = $req->email;
        if($req->password != $req->confirm){
        return redirect()->route('requestJoin',['email'=>$email,'name'=>$name])->with('error','password does not matched');
        }
        else{
            $user = User::where('email',$email)->first();
            $user->name = $name;
            $user->password = Hash::make($req->password);
            $user->user_status = "approved";
            $user->save();
            return redirect()->route('login');
        }
    }
}
