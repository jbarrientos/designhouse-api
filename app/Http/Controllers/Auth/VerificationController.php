<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Console\Presets\React;

// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user){

        // check if the url is a valid signed url
        if(!URL::hasValidSignature($request)){
            return response()->json(['errors' => [
                'message' => 'Invalid verification link'
            ]], 422);
        }

        // check if the user has already verified the account
        if($user->hasVerifiedEmail()){
            return response()->json(['errors' => [
                'message' => 'Email address already verified'
            ]], 422);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'message' => 'Email succesfully verified'
        ], 200);

    }

    public function resend(Request $request){
        $this->validate($request, [
            'email' => ['email', 'required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(['errors' => [
                'email' => 'No user could be found with this email address'
            ]], 422);
        }

        if($user->hasVerifiedEmail()){
            return response()->json(['errors' => [
                'message' => 'Email address already verified'
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'Verification link resent'], 200);
    }
}
