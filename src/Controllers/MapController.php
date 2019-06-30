<?php

namespace Samanar\Map;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\Map;

class MapController extends Controller
{
    // index file . returns map page
    public function index(Request $request, $user_id, $province, $state, $city = null)
    {
        $coordinates = $this->getCoordinates($province, $state, $city);
        if ($coordinates) {
            $long = $coordinates->longitude;
            $lat = $coordinates->latitude;
            $zoom = 12;
            return view('map::index')
                ->with('long', $long)
                ->with('lat', $lat)
                ->with('zoom', $zoom)
                ->with('province', $province)
                ->with('state', $state)
                ->with('city', $city)
                ->with('user_id', $user_id);
        } else {
            dd('not found');
        }
    }


    // get coordinates of given data.
    // container for get functions below
    public function getCoordinates($province, $state, $city = null)
    {
        $coordinates = null;
        if ($city) {
            $coordinates = $this->getWithProvinceAndStateAndCity($province, $state, $city);
        }

        if (!$coordinates) {
            $coordinates = $this->getWithProvinceAndState($province, $state);
        }

        if (!$coordinates) {
            // Todo: this condition should be removed after reformatting the database
            $coordinates = $this->getWithProvinceAndStateSwapped($province, $state);
        }
        if (!$coordinates) {
            $coordinates = $this->getWithProvinceAndStateLike($province, $state);
        }
        return $coordinates;
    }

    // find coordinate with province and state given
    private function getWithProvinceAndState($province, $state)
    {
        $query = Map::where('province', $province)->where('state', $state);
        return  $query->first();
    }

    private function getWithProvinceAndStateAndCity($province, $state, $city)
    {
        $query = Map::where('province', $province)->where('state', $state)->where('city', $city);
        return  $query->first();
    }

    // this function is called due to confusion found in database
    // Todo: this function should be removed after reformatting the database
    private function getWithProvinceAndStateSwapped($province, $state)
    {
        return $this->getWithProvinceAndState($state, $province);
    }

    private function getWithProvinceAndStateLike($province, $state)
    {
        $query = Map::where('province', 'like', '%' . $province . '%')
            ->where('state', 'like', '%' . $state . '%');
        return $query->first();
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

    // runs DMStoDD on all database records
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
