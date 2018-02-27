<?php

namespace app\common\model;


use think\Model;

class WebType extends Model
{
    //定义表明
    protected $table = 'db_web_type';

    public function webconfig()
    {
        return $this->hasMany('WebConfig', 'vargroup', "id");
    }
}