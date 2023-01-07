<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //use VerifiesEmail; // TODO(PATBRO)

    public $successStatus = 200;
    public $errorStatus = 400;
    public $unauthorisedStatus = 401;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
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

        // TODO(PATBRO): are the three lines of code below necessary?
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
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

        $token = User::where('email', $request->email)->first()->createToken('')->plainTextToken;
        return response()->json(["success" => $token], $this->successStatus);
    }

    public function getDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json(["success" => $user], $this->successStatus);
        }

        return response()->json(["error" => "Failed to retrieve user details"], $this->successStatus);
    }

    public function updateDetails(Request $reuqest)
    {
        $validator = Validator::make($request->all(), [
            // Only validate when posted
            'avatar' => 'sometimes|mimes:jpeg,png|max:2048',
            'email' => 'sometimes|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 500);
        }

        $user = Auth::user();
        $original_user = $user;
        if ($request->has('avatar')) {
            $user->avatar = Storage::putFile('avatars', $request->file('avatar'));
        }

        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        $user->save();
        Log::info("Details for user ID ". $user->id ." were updated successfully");
        return response()->json(['success' => $user], 200);
    }
}
