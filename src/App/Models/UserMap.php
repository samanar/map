<?php

namespace Samanar\Map\App\Models;


use Illuminate\Database\Eloquent\Model;


class UserMap extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = config('map.userMapRelationTableName');
    }

    public $timestamps = false;
    protected $fillable = ['user_id', 'longitude', 'latitude'];
}
