<?php

namespace App\Http\Controllers;

use App\Models\Fkilkom;
use Illuminate\Http\Request;

class Frontend extends Controller
{
    public function getdb() {
        $fkilkomss = Fkilkom::all();
        return view ('frontend.index', compact('fkilkomss'));
    }
}
