<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SinusValue;
use Illuminate\Http\Request;

class SinusValueController extends Controller
{
	public function store(Request $request)
	{
		$request->validate([
			'sinus_id' => 'required|integer',
			'date' => 'required|date',
			'value' => 'required|integer',
		]);

		$newSinusValue = new SinusValue([
			'sinus_id' => $request->get('sinus_id'),
			'date' => $request->get('date'),
			'value' => $request->get('value'),
		]);

		$newSinusValue->save();

		return response()->json($newSinusValue);
	}

	public function show($id)
	{
        $sinusValues = SinusValue::where('sinus_id', $id)->get();
        $sinusValues = $sinusValues->makeHidden(['id', 'sinus_id', 'created_at', 'updated_at']);
		return response()->json($sinusValues);
	}

	public function delete(Request $request) {
		$request->validate([
			'sinus_id' => 'required|integer',
			'date' => 'required|date',
		]);

		$sinusValues = SinusValue::where('sinus_id', $request->get('sinus_id'))->where('date', $request->get('date'))->delete();
		return response()->json($sinusValues);
	}	
}
