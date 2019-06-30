<?php

namespace Samanar\Map;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\UserMap;

class UserMapController extends Controller
{
    public function index()
    { }

    public function store(Request $request)
    {
        dd($request);
    }
}
