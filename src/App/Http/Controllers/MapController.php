<?php

namespace Samanar\Map\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\App\Models\Map;
use Samanar\Map\App\Models\UserMap;
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;

class MapController extends Controller
{


    // index file . returns map page
    public function index(Request $request, $user_id, $province, $state, $city = null)
    {
        // first see if user already exists and has a previous coordinate
        $previous_lat = null;
        $previous_long = null;
        $zoom = 12;
        $user_map = UserMap::where('user_id', $user_id)->first();
        if ($user_map) {
            $previous_lat = $user_map->latitude;
            $previous_long = $user_map->longitude;
        }

        // find coordinates by province,state,city
        $coordinates = $this->getCoordinates($province, $state, $city);
        if ($coordinates) {
            $long = $coordinates->longitude;
            $lat = $coordinates->latitude;
        } else {
            $coordinates = $this->requestApi($province, $state);
            if ($coordinates) {
                $long = $coordinates['longitude'];
                $lat = $coordinates['latitude'];
            } else {
                dd('not found');
            }
        }


        // Todo: add api resource
        // $data = [];
        // return response()->json($data)
        return view('map::index')
            ->with('long', $long)
            ->with('lat', $lat)
            ->with('zoom', $zoom)
            ->with('province', $province)
            ->with('state', $state)
            ->with('city', $city)
            ->with('previous_lat', $previous_lat)
            ->with('previous_long', $previous_long)
            ->with('user_id', $user_id);
    }



    // get coordinates of given data.
    // container for get functions below
    public function getCoordinates($province, $state, $city = null)
    {
        $coordinates = null;


        if ($city) $coordinates = $this->getWithProvinceAndStateAndCity($province, $state, $city);


        if (!$coordinates) $coordinates = $this->getWithProvinceAndState($province, $state);


        if (!$coordinates) $coordinates = $this->getWithProvinceAndStateLike($province, $state);


        return $coordinates;
    }



    // find coordinate with province and state given
    private function getWithProvinceAndState($province, $state)
    {
        if ($data = $this->getWithProvinceAndStateAndCity($province, $state, $state))  return $data;

        return Map::where('province', $province)->where('state', $state)->first();
    }



    private function getWithProvinceAndStateAndCity($province, $state, $city)
    {
        return Map::where('province', $province)->where('state', $state)->where('city', $city)->first();
    }



    private function getWithProvinceAndStateLike($province, $state)
    {
        return Map::where('province', 'like', '%' . $province . '%')
            ->where('state', 'like', '%' . $state . '%')->first();
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
        return $deg + ((($min * 60) + ($sec)) / 3600);
    }




    // requesting from opencagedata.com (forward geocoding request)
    private function requestApi($province, $state)
    {
        $key = config('map.openCageApiKey');
        $data = 'q=';
        $data .= $province . ' ' . $state;
        $data .= '&key=' . $key;
        $client = new Client([
            'base_uri' => 'https://api.opencagedata.com/geocode/v1/json?no_annotations=1&no_record=1&limit=2&' . $data,
            'timeout'  => 5.0,
        ]);

        try {
            $response = $client->request('GET', '');
        } catch (RequestException $e) {
            return null;
        }


        $body = json_decode($response->getBody(), true);


        $coordinates = null;
        if (sizeof($body['results'])) {
            $coordinates['latitude'] = $body['results'][0]['geometry']['lat'];
            $coordinates['longitude'] = $body['results'][0]['geometry']['lng'];
        }


        return $coordinates;
    }
}
