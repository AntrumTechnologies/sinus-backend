<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewWave;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 400;
    public $unauthorisedStatus = 401;

    public function notify()
    {
        $user = Auth::user();
        $user->notify(new NewWave);

        return response()->json(["success" => ""], $this->successStatus);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $token = User::where('email', $request->email)->first()->createToken('')->plainTextToken;
            return response()->json(["success" => $token], $this->successStatus);
        }

        return response()->json(["error" => "The provided credentials are incorrect"], $this->errorStatus);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete(); // Deletes all tokens

        /*
        TODO(PATBRO): room for improvement, to only delete the current session token
        $token = PersonalAccessToken::findtoken($request->bearerToken());
        $token->delete();
        */

        return response()->json(["success" => "Log out successful"], $this->successStatus);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], $this->errorStatus);
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Send email to verify emailaddress to user
        event(new Registered($user));

        $token = User::where('email', $request->email)->first()->createToken('')->plainTextToken;
        return response()->json(["success" => $token], $this->successStatus);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(["success" => $status], $this->successStatus)
            : response()->json(["error" => $status], $this->errorStatus);
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return $validator->messages();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(["success" => $status], $this->successStatus)
            : response()->json(["error" => $status], $this->errorStatus);
    }

    public function getDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json($user, $this->successStatus);
        }

        return response()->json(["error" => "Failed to retrieve user details"], $this->successStatus);
    }

    public function updateDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Only validate when posted
            'name' => 'sometimes',
            'password' => 'sometimes',
            'confirm_password' => 'sometimes|required|same:password',
            'avatar' => 'sometimes|mimes:jpeg,png|max:4096',
            'email' => 'sometimes|email|unique:users,email',
            'fcm_token' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 500);
        }

        $user = Auth::user();
        $validated = $validator->validated();

        if ($request->has('name')) {
            $user->name = $request->get('name');
        }

        if ($request->has('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        if ($request->has('avatar')) {
            if ($user->avatar != null) {
                Storage::delete($user->avatar);
            }

            $user->avatar = Storage::putFile('avatars', $request->file('avatar'));
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->input('fcm_token');
        }

        if ($request->has('email') && $request->get('email') != $user->email) {
            $user->email = $request->input('email');

            // Unset that email address is verified
            $user->email_verified_at = null;

            // Verify new email address of user
            event(new Registered($user));
        }

        $user->save();
        Log::info("Details for user ID ". $user->id ." were updated successfully");
        return response()->json(['success' => $user], 200);
    }
}
