<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sinus;
use App\Models\SinusValue;
use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SinusController extends Controller
{
	public function index($user_id = null)
	{
		if ($user_id != null) {
			$retrieveFollowing = Following::where('user_id', Auth::id());
			$retrieveSine = Sinus::where('user_id', Auth::id())->union($retrieveFollowing)->get();
			return Response::json($retrieveSine, 200);
		} else {
			$retrieveSine = Sinus::all();
			return Response::json($retrieveSine, 200);
		}
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
