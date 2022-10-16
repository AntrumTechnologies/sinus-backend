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
            $success['token'] = $request->session()->regenerate();
            return response()->json(["success" => $success], $this->successStatus);
        }

        return response()->json(["error" => "Login failed. Please check your credentials"], $this->errorStatus);
    }

    public function logout(Request $request)
    {
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

        Auth::login($user, $remember = true);
        return response()->json(["success" => "Registration successful!"], $this->successStatus);
    }

    public function getDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json(["success" => $user], $this->successStatus);
        }

        return response()->json(["error" => "Failed to retrieve user details"], $this->successStatus);
    }
}
