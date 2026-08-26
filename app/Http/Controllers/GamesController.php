<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    public function index()
    {
        $game = Game::latest()->paginate(15);

        // return collection of posts as a resource
        if (!$game) {
            abort(404);
        }
        return new GameResource(true, 'List Data Games', $game);
    }

    // Create a new Games
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
        ]);
        return Game::create($request->all());
    }

    // Get a single Games by ID
    public function show($id)
    {
        return Game::find($id);
    }

    // Update a Games by ID
    public function update(Request $request, $id)
    {
        $Games = Game::find($id);

        $request->validate([
            'title' => 'string|max:255',
            'author' => 'string|max:255',
        ]);

        $Games->update($request->all());

        return $Games;
    }

    // Delete a Games by ID
    public function destroy($id)
    {
        return Game::destroy($id);
    }
}
