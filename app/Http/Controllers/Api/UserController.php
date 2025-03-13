<?php

namespace App\Http\Controllers\Api;

use App\Jobs\otp;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $req){
        $expiredUsers = User::where('otp_expires_at', '<', Carbon::now())
        ->where('user_status','pending')
        ->get();

        foreach ($expiredUsers as $user) {
            $user->delete();
        }
        
        $validateUser = Validator::make($req->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required',
        ]);
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>"Validation failed",
                'error'=>$validateUser->errors()->all(),
            ],401);
        }

        $otp = rand(100000, 999999);
        dispatch(new otp($req->name, $req->email,$otp));

        $user = User::create([
            'name'=>$req->name,
            'email'=>$req->email,
            'password'=>$req->password,
            'user_status'=>'pending',
            'otp' => $otp,
            'otp_expires_at'=> now()->addMinutes(5),
        ]);
        // sending otp


        return response()->json([
            'status'=>true,
            'otp'=>$otp,
            'time'=>now()->addMinutes(5),
        ],200);

        // return response()->json([
        //     'status'=>true,
        //     'message'=>'user registered successfully',
        // ],200);
    }

    public function login(Request $req){
        $validateUser = Validator::make($req->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>"Validation failed",
                'error'=>$validateUser->errors()->all(),
            ],401);
        }
        if(Auth::attempt(['email' => $req->email, 'password' => $req->password])){
            $authUser = Auth::user();
            return response()->json([
                'status'=>true,
                'message'=>"Logined Successfully",
                'token'=>$authUser->createToken("API Token")->plainTextToken,
                'token_type'=>'bearer',
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>"Email or password incorrect",
            ],401);
        }
    }

    public function logout(Request $req){
        $user = $req->user();
        $user->tokens()->delete();
        return response()->json([
            'status'=>true,
            'message'=>"logout successfully",
        ],200);
    }

    public function verifyOtp(Request $req){
        $validateUser = Validator::make($req->all(),[
            'otp'=>'required',
            'email'=>'required|email',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>"Validation failed",
                'error'=>$validateUser->errors()->all(),
            ],401);
        }

        $expiredUsers = User::where('otp_expires_at', '<', Carbon::now())
        ->where('user_status','pending')
        ->get();

        foreach ($expiredUsers as $user) {
            $user->delete();
        }


        $user = User::where('email',$req->email)->first();
        if($user){
            if($user->otp  == $req->otp){
                $user->user_status = 'approved';
                $user->save();
                return response()->json([
                    'status'=>true,
                    'message'=>'user registered successfully',
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message'=>'otp not matched',
                ],401);
            }
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'user not found',
            ],401);
        }
        
    }
}
