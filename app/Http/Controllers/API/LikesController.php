<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Likes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class LikesController extends Controller
{
    // Retrieve list of all users that like the corresponding wave
    public function index($wave_id)
    {
        $likes = Likes::where('wave_id', $wave_id);
        return Response::json($likes, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'wave_id_to_like' => 'required|integer',
        ]);

        // Build model in order to return later
        $newLike = new Likes([
            'user_id' => Auth::id(),
            'wave_id' => $request->get('wave_id_to_like'),
        ]);

        // Check whether combination is already present in database
        try {
            $like = Likes::where([
                ['user_id', '=', Auth::id()],
                ['wave_id', '=', $request->get('wave_id_to_like')],
            ])->firstOrFail();

            Log::notice("Like call was executed even though combination of user ID (". Auth::id() .") and wave ID (". $request->get('wave_id_to_like') .") is already present in table");
        } catch (ModelNotFoundException $e) {
            // Only save to database when combination is not yet present in database
            $newLike->save();
        }

        return Response::json($newLike, 200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'wave_id_to_dislike' => 'required|integer',
        ]);

        try {
            // Retrieve like details in order to return later
            $like = Likes::where([
                ['user_id', '=', Auth::id()],
                ['wave_id', '=', $request->get('wave_id_to_dislike')],
            ])->firstOrFail();

            $likeDeletion = $like->delete();
            if (!$likeDeletion) {
                Log:error("Failed to dislike. User ID ". Auth::id() .", wave ID ". $request->get('wave_id_to_dislike'));
                return Reponse::json(['error' => 'Failed to dislike'], 500);
            }

            return Response::json($like, 200);
        } catch (ModelNotFoundException $e) {
            return Response::json('', 200);
        }
    }
}
