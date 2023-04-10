<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserResetPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function submitForgetPasswordForm(Request $request, $value)
    {
        
            $request->validate([
                'email' => 'required|email',
            ]);
        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
            $user = User::where('email', $request->email)->first();
            if ($user) {
                    Notification::send($user, new UserResetPassword($user, $token));
            } else {
                return back()->with('error', 'Please enter valid email address');
            }
    
        return redirect()->route('home')->with('success', 'We have e-mailed your password reset link!');
    }
    public function showResetPasswordForm($token, $value)
    {
        return view('');
    }
    public function submitResetPasswordForm(Request $request, $value)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();
        $token = DB::table('password_resets')
            ->where('token', '=', $request->token)
            ->where('created_at', '>', Carbon::now()->subHours(1))
            ->first();
            if (!$token) {
                return back()->with('error', 'Your token has been expired');
            if (!$updatePassword) {
                return back()->withInput()->with('error', 'Invalid token!');
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->with('error', 'Please enter valid email address');
            }
            $user->password = Hash::make($request->password);
            $user->save();
        }
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('login')->with('success', 'Your password has been changed!');
    }
}
