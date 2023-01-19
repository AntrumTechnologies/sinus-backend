<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sinus;
use App\Models\SinusValue;
use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SinusController extends Controller
{
	public function indexCreated()
	{
		$createdSinuses = Sinus::where('user_id', Auth::id())->get();
		return Response::json($createdSinuses, 200);
	}

	public function indexExplore()
	{
		$retrieveSine = Sinus::where('archived', false)->get();
		return Response::json($retrieveSine, 200);
	}

	public function indexFollowing()
	{
		$retrieveFollowing = Following::where('user_id', Auth::id())->pluck('following_user_id')->toArray();
		array_push($retrieveFollowing, Auth::id()); // User always follows themselves
		$retrieveSine = DB::table('sinuses')->whereIn('user_id', $retrieveFollowing)->where('archived', false)->get();

		return Response::json($retrieveSine, 200);
	}

	public function store(Request $request)
    {
		$request->validate([
			'name' => 'required|max:30',
			'date_name' => 'required|max:30',
		]);

		$newSinus = new Sinus([
			'name' => $request->get('name'),
			'user_id' => Auth::id(),
			'date_name' => $request->get('date_name'),
        ]);

        $newSinus->save();

		return Response::json($newSinus, 200);
	}

	public function show($id)
    {
        $sinus = Sinus::findOrFail($id);
        if (Following::where('user_id', Auth::id())->where('following_user_id', $sinus->user_id)->first()) {
            $following = true;
        } else {
            $following = false;
        }

        $sinus->following = $following;
        return Response::json($sinus, 200);
	}

	public function delete(Request $request)
	{
		$request->validate([
			'id' => 'required|integer',
		]);

		$sinus = Sinus::where('id', $request->get('id'));
		$sinusDeletion = $sinus->delete();
		if (!$sinusDeletion) {
			return Response::json($sinus, 200);
		}
		
		$sinusValues = SinusValue::where('sinus_id', $request->get('id'));
		if (!$sinusValues->delete() && $sinusDeletion) {
			// Rollback Sinus deletion if sinusValue deletion failed
			Sinus::onlyTrashed()->where('id', $request->get('id'))->restore();
		}

		return Response::json($sinusValues, 200);
	}
}
