<?php

namespace app\common\model;

class RegionCity extends BaseModel
{
    //定义表明
    protected $table = 'db_region_city';

    public function county()
    {
        return $this->hasMany('RegionCounty', 'city_id', 'city_id')
            ->field('county_id,county_name as name')
            ->where(['is_index' => 1]);
    }

}