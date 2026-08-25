<?php

namespace App\Http\Controllers;

use App\Models\Game;

class WebgamesController extends Controller
{
    public function index()
    {
        return view('games');
    }
}
