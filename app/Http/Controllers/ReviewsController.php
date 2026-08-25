<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index()
    {
        $game = Review::latest()->paginate(15);

        // return collection of posts as a resource
        if (!$game) {
            abort(404);
        }
        return new ReviewResource(true, 'List Data Games', $game);
    }

    // Create a new Games
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required',
            'score' => 'required',
        ]);
        $review = Review::create($request->all());

        // 2. Cari game berdasarkan game_id lalu update total_reviews dan total_score
        $game = Game::find($request->game_id);
        if ($game) {
            $game->increment('total_review', 1);  // Menambahkan 1 ke total_reviews yang ada
            $game->increment('total_score', $request->score);  // Menambahkan score baru ke total_score yang ada
        }

        // Kembalikan response JSON (karena mengarah ke /api/reviews)
        return response()->json([
            'message' => 'Review berhasil ditambahkan dan data game diperbarui.',
            'data' => $review
        ], 201);
    }
}
