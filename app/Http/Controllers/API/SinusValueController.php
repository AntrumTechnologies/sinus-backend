<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Following;
use App\Models\Sinus;
use App\Models\SinusValue;
use App\Models\User;
use App\Notifications\NewWaveValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SinusValueController extends Controller
{
	function sendNotification($sinus_id) {
		$retrieveSine = Sinus::findOrFail($sinus_id);
		$retrieveFollowers = Following::where('following_user_id', $retrieveSine->user_id)->pluck('user_id')->toArray();

		$fcm_tokens = [];
		foreach ($retrieveFollowers as $user_id) {
			$retrieveUser = User::findOrFail($user_id);
			if ($retrieveUser->fcm_token != null) {
				array_push($fcm_tokens, $retrieveUser->fcm_token);
			}
		}

		// Retrieve arbitrary sinus value in order to send the notification
		$sinusValue = SinusValue::where('sinus_id', $sinus_id)->latest()->first();
		$sinusValue->updateFcmTokens($fcm_tokens);
		$sinusValue->notify(new NewWaveValue($retrieveSine->name));
	}
	
	public function notify($sinus_id)
	{
		$this->sendNotification($sinus_id);
		return Response::json("Success", 200);
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'sinus_id' => 'required|integer',
			'date' => 'required|date|before_or_equal:today',
			'value' => 'required|integer',
			'latitude' => 'sometimes',
			'longtitude' => 'sometimes',
			'tags' => 'sometimes|required|array',
			'description' => 'sometimes',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

		$request = $validator->safe()->all();

		$latestSinusValue = SinusValue::where('sinus_id', $request['sinus_id'])->latest()->first();
		if ($latestSinusValue) {
			if (strtotime($request['date']) <= strtotime($latestSinusValue->date)) {
				return Response::json('Given date before or equal to latest date');
			}
		}

		$newSinusValue = new SinusValue($request);
		$newSinusValue->save();

		$this->sendNotification($request['sinus_id']);
		return Response::json("Successfully added new wave value");
	}

	public function show($id, $limit = null)
	{
		if ($limit != null) {
        	$sinusValues = SinusValue::where('sinus_id', $id)->orderBy('date', 'DESC')->take($limit)->get();
		} else {
			$sinusValues = SinusValue::where('sinus_id', $id)->orderBy('date', 'ASC')->get();
		}
		
		$sinusValues = $sinusValues->makeHidden(['id', 'sinus_id', 'created_at', 'updated_at']);
		return response()->json($sinusValues);
	}

	// TODO(PATRO): retrieve multiple ID's using one API call
	// public function show(Request $request)
	// {
	// 	$request->validate([
	// 		'id' => 'required|array',
	// 	]);

	// 	if ($limit != null) {
    //     	$sinusValues = SinusValue::where('sinus_id', $id)->orderBy('date', 'DESC')->take($limit)->get();
	// 	} else {
	// 		$sinusValues = SinusValue::where('sinus_id', $id)->orderBy('date', 'ASC')->get();
	// 	}
    //     $sinusValues = $sinusValues->makeHidden(['id', 'sinus_id', 'created_at', 'updated_at']);
	// 	return response()->json($sinusValues);
	// }

	public function delete(Request $request) {
		$request->validate([
			'sinus_id' => 'required|integer',
			'date' => 'required|date',
		]);

		$sinusValues = SinusValue::where('sinus_id', $request->get('sinus_id'))->where('date', $request->get('date'));
		return response()->json($sinusValues->delete());
	}	
}
