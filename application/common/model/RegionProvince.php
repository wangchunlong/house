<?php

namespace app\common\model;

class RegionProvince extends BaseModel
{
    //定义表明
    protected $table = 'db_region_province';

    public function city()
    {
        return $this->hasMany('RegionCity', 'province_id', 'province_id')
            ->relation(['county'])
            ->field('city_id,city_name as name')
            ->where(['is_index' => 1]);
    }

}