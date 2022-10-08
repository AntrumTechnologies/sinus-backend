<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class UserController extends Controller
{
    use VerifiesEmail;

    public $successStatus = 200;
    public $errorStatus = 400;
    public $unauthorisedStatus = 401;

    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('Sinus')->accessToken;
            Log::info("Login for user ID ". $user->id ." succeeded");
            return response()->json(["success" => "Login successful!", $this->successStatus);
        } else {
            Log::info("Login for email ". reuqest('email') ." failed");
            return response()->json(["error" => "Login failed. Please check your credentials"], $this->error);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->token()->revoke();
            Log::info("User ID ". $user->id ." logged out");
            return response()->json(["success" => "Log out successful"], $this->successStatus);
        } else {
            Log::info("Log out failed");
            return response()->json(["error" => "Log out failed"], $this->errorStatus);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
