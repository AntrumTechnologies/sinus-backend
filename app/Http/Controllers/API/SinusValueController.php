<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SinusValue;
use App\Providers\NewWaveValue;
use Illuminate\Http\Request;

class SinusValueController extends Controller
{
    public function notify($sinus_id)
    {
		$retrieveSine = Sinus::where('id', $sinus_id)->first();
		$retrieveFollowers = Following::where('following_user_id', $retrieveSine->user_id)->pluck('user_id')->toArray();

		return response()->json($retrieveFollowers, 200);

		SinusValue::updateFcmTokens($fcm_tokens);

		$user = Auth::user();
		$user->notify(new NewWave);

		return response()->json(["success" => ""], $this->successStatus);
    }

	public function store(Request $request)
	{
		$request->validate([
			'sinus_id' => 'required|integer',
			'date' => 'required|date|before_or_equal:today',
            'value' => 'required|integer',
            'latitude' => 'sometimes',
            'longtitude' => 'sometimes',
			'tags' => 'sometimes|required|array',
			'description' => 'sometimes',
		]);

		$latestSinusValue = SinusValue::where('sinus_id', $request->get('sinus_id'))->latest()->first();
		if ($latestSinusValue) {
			if (strtotime($request->get('date')) <= strtotime($latestSinusValue->date)) {
				return response()->json('Given date before or equal to latest date');
			}
		}

		$newSinusValue = new SinusValue([
			'sinus_id' => $request->get('sinus_id'),
			'date' => $request->get('date'),
			'value' => $request->get('value'),
			'latitude' => $request->get('latitude'),
			'longitude' => $request->get('longitude'),
			'tags' => $request->get('tags'),
			'description' => $request->get('description'),
		]);

		$newSinusValue->save();

		NewWaveValue::dispatch($newSinusValue);

		return response()->json($newSinusValue);
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
