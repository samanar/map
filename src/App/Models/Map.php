<?php

namespace Samanar\Map\App\Models;

use Illuminate\Database\Eloquent\Model;


class Map extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('map.mapTableName');
    }

    public $timestamps = false;
}
