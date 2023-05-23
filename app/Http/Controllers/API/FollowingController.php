<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class FollowingController extends Controller
{
    // Retrieve list of all users that the current user is following
    public function index()
    {
        $following = Following::where('user_id', Auth::id());
		return Response::json($following, 200);
    }

	public function store(Request $request)
    {
		$request->validate([
			'user_id_to_follow' => 'required|integer',
		]);

        // Build model in order to return later
        $newFollowing = new Following([
            'user_id' => Auth::id(),
            'following_user_id' => $request->get('user_id_to_follow'),
        ]);

        // Check whether combination is already present in database
        try {
            $following = Following::where([
                ['user_id', '=', Auth::id()],
                ['following_user_id', '=', $request->get('user_id_to_unfollow')],
            ])->firstOrFail();

            Log::notice("Follow call was executed even though combination of user ID (". Auth::id() .") and following user ID (". $request->get('user_id_to_follow') .") is already present in table");
        } catch (ModelNotFoundException $e) {
            // Only save to database when combination is not yet present in database
            $newFollowing->save();
        }

        return Response::json($newFollowing, 200);
	}

	public function delete(Request $request)
	{
        $request->validate([
			'user_id_to_unfollow' => 'required|integer',
		]);

        try {
            // Retrieve following details in order to return later
            $following = Following::where([
                ['user_id', '=', Auth::id()],
                ['following_user_id', '=', $request->get('user_id_to_unfollow')],
            ])->firstOrFail();

            $followingDeletion = $following->delete();
            if (!$followingDeletion) {
                Log:error("Failed to unfollow. User ID ". Auth::id() .", Following user ID ". $request->get('user_id_to_unfollow'));
                return Reponse::json(['error' => 'Failed to unfollow'], 500);
            }

            return Response::json($following, 200);
        } catch (ModelNotFoundException $e) {
            return Response::json('', 200);
        }
	}
}
