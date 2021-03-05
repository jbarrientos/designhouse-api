<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    //

    public function updateProfile(Request $request){

        $user = auth()->user();

        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->tagline = $request->tagline;
        $user->name = $request->name;
        $user->about = $request->about;
        $user->formatted_address = $request->formatted_address;
        $user->available_to_hire = $request->available_to_hire;
        $user->location = $location;

        // $user->save([
        //     'tagline' => $request->tagline,
        //     'name' => $request->name,
        //     'about' => $request->about,
        //     'formatted_address' => $request->formatted_address,
        //     'available_to_hire' => $request->available_to_hire,
        //     'location' => $location
        // ]);

        $user->save();

        return new UserResource($user);

    }

    public function updatePassword(Request $request){

        $this->validate($request, [
            'current_password'=>['required', new MatchOldPassword],
            'password'=>['required', 'confirmed', 'min:6', new CheckSamePassword],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated'
        ], 200);

    }


}
