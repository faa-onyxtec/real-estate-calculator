<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerifyUser;
use App\Notifications\VerifyUserEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginRegisterController extends Controller
{
    public function login(Request $request){
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        if (Auth::guard()->attempt($request->only(['email','password']), $request->get('remember'))){
            return redirect()->intended('/');
        }
        return back()->withInput($request->only('email', 'remember'));
    }
   public function register(Request $request){
    $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'contact_no' => 'required'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }
   try{
    $user = new User();
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->password = $request->input('password');
    $user->contact_no = $request->input('contact_no');
    $user->save();
    $verify_token = Str::random(32);
    VerifyUser::create([
        'token' => $verify_token,
        'user_id' => $user->id,
    ]);
    Notification::send($user, new VerifyUserEmail($user, $verify_token));
   }catch(Exception $e){
    Log::error($e->getMessage());
   }
   }

   public function verifyUser($token)
   {
       $verifiedUser = VerifyUser::where('token', $token)->first();
       $user = User::where('id', $verifiedUser->user_id)->first();
       if (isset($verifiedUser)) {
           $user = $verifiedUser->user;
           if ($user->email_verified_at == null) {
               $user->email_verified_at = Carbon::now();
               $user->save();
               $message = "Your Email is verified successfully";
               Auth::guard()->login($user);
               return redirect()->route('home')->with('success', $message);
           }
           if ($user->email_verified_at != null) {
               $message = "Your Email is already verified";
               Auth::guard('')->login($user);
               return redirect()->route('home')->with('success', $message);
           }
       } else {
           $message = "Something went wrong";
           return redirect(route('home'))->with('error', $message);
       }
   }
}
