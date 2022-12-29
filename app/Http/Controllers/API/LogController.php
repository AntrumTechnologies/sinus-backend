<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
			'level' => 'required|max:12',
			'message' => 'required',
		]);

        switch ($request->get('level')) {
            case "emergency":
                Log::emergency($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "alert":
                Log::alert($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "critical":
                Log::critical($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "error":
                Log::error($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "warning":
                Log::warning($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "notice":
                Log::notice($request->get('message'), ['user_id' => Auth::id()]);
                break;

            case "info":
                Log::info($request->get('message'), ['user_id' => Auth::id()]);
                break;

            default:
                Log::debug($request->get('message'), ['user_id' => Auth::id()]);
                break;
        }
    }
}
