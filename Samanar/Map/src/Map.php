<?php

namespace Samanar\Map;

use Illuminate\Database\Eloquent\Model;
use config;


class Map extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('map.mapTableName');
    }

    public $timestamps = false;
}
