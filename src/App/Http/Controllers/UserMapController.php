<?php

namespace Samanar\Map\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\UserMap;

class UserMapController extends Controller
{
    public function index()
    { }

    public function store(Request $request)
    {
        // Todo: do validation
        $user_map = UserMap::updateOrCreate(
            ['user_id' => $request->user_id],
            ['longitude' => $request->longitude, 'latitude' => $request->latitude]
        );
        return redirect()->route('map.index', [
            'user_id' => $request->user_id,
            'province' => $request->province,
            'state' => $request->state,
            'city' => $request->city,
        ]);
    }
}
