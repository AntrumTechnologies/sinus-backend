<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sinus;
use App\Models\SinusValue;
use Illuminate\Http\Request;

class SinusController extends Controller
{
	public function index()
	{
        $sinuses = Sinus::all();
		return response()->json($sinuses);
	}

	public function store(Request $request)
    {
		$request->validate([
			'name' => 'required|max:30',
			'date_name' => 'required|max:30',
		]);

		$newSinus = new Sinus([
			'name' => $request->get('name'),
			'date_name' => $request->get('date_name'),
        ]);

        $newSinus->save();

		return response()->json($newSinus);
	}

	public function show($id)
	{
        $sinus = Sinus::findOrFail($id);
		return response()->json($sinus);
	}

	public function delete(Request $request)
	{
		$request->validate([
			'id' => 'required|integer',
		]);

		$sinus = Sinus::where('id', $request->get('id'));
		$sinusDeletion = $sinus->delete();
		if (!$sinusDeletion) {
			return response()->json($sinus);
		}
		
		$sinusValues = SinusValue::where('sinus_id', $request->get('id'));
		if (!$sinusValues->delete() && $sinusDeletion) {
			// Rollback Sinus deletion if sinusValue deletion failed
			Sinus::onlyTrashed()->where('id', $request->get('id'))->restore();
		}

		return response()->json($sinusValues);
	}
}
