<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sinus;
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
			'date_name' => $request->get('date'),
		]);

		return response()->json($newSinus);
	}

	public function show($id)
	{
		$sinus = Sinus::findOrFail($id);
		return response()->json($sinus);
	}
}
