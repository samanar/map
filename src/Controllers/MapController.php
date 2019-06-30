<?php

namespace Samanar\Map;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Samanar\Map\Map;
use Samanar\Map\UserMap;
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;
use Config;

class MapController extends Controller
{
    // index file . returns map page
    public function index(Request $request, $user_id, $province, $state, $city = null)
    {
        // first see if user already exists and has a previous coordinate
        // Todo: database should be improved
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


    // requesting from opencagedata.com (forward geocoding request)
    //
    private function requestApi($province, $state)
    {
        $key = Config::get('map.openCageApiKey');
        $data = 'q=';
        $data .= $province . ' ' . $state;
        $data .= '&key=' . $key;
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.opencagedata.com/geocode/v1/json?no_annotations=1&no_record=1&limit=2&' . $data,
            // You can set any number of default request options.
            'timeout'  => 5.0,
        ]);

        try {
            $response = $client->request('GET', '');
        } catch (RequestException $e) {
            dd($e);
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
