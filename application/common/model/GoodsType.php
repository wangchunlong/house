<?php

namespace app\common\model;

class GoodsType extends BaseModel
{
    //定义表明
    protected $table = 'db_goods_type';

    protected static function init()
    {
        self::event('before_write', function ($eye) {
            $eye->create_time = time();
        });
    }

    public function goods()
    {
        return $this->hasMany('goods', 'type_id', 'id');
    }
}
