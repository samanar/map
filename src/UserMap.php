<?php

namespace Samanar\Map;

use Illuminate\Database\Eloquent\Model;
use config;


class UserMap extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('map.userMapRelationTableName');
    }

    public $timestamps = false;
}
