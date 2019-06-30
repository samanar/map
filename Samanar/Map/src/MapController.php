<?php

namespace Samanar\Map;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\Map;

class MapController extends Controller
{
    // index file . returns map page
    public function index(Request $request)
    {
        $province = "مرکزی";
        $state = "محلات";
        $city = "";

        $query = Map::where('province', $province)->where('state', $state);
        if (isset($city) && $city != "") {
            $query->where('city', $city);
        }
        $coordinates = $query->first();
        if ($coordinates) {
            $long = $coordinates->longitude;
            $lat = $coordinates->latitude;
            return view('map::index')->with('long', $long)->with('lat', $lat);
        } else {
            return abort(500);
        }
    }


    // container for DMStoDD function
    // container needs to be called with given deg min sec string
    public function split_input($text)
    {
        $parts = explode(' ', $text);
        $deg = (float) rtrim($parts[0], '°');;
        $min = (float) rtrim($parts[1], '\'');
        $sec = (float) $parts[2];
        return $this->DMStoDD($deg, $min, $sec);
    }

    // do not call this function with format saved in database 
    // use container above to call this function
    private function DMStoDD($deg, $min, $sec)
    {
        // Converting DMS ( Degrees / minutes / seconds ) to decimal format
        return $deg + ((($min * 60) + ($sec)) / 3600);
    }

    // converts database seed longitudes and latitudes to decimal
    // Todo: use chunk method for better memory utilization
    private function updateData()
    {
        $coordinates = Map::all();
        foreach ($coordinates as $coordinate) {
            $coordinate['latitude'] = $this->split_input($coordinate['latitude']);
            $coordinate['longitude'] = $this->split_input($coordinate['longitude']);
            $coordinate->save();
        }
    }
}
