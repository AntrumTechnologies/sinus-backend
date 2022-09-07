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
			'date' => $rquest->get('date'),
			'value' => $request->get('value'),
		]);

		$newSinusValue->save();

		return response()->json($newSinusValue);
	}

	public function show($id)
	{
		$sinusValues = SinusValue::where('sinus_id', '=', $request->get('id'))->get('sinus_id');
		return response()->json($sinusValues);
	}
}
