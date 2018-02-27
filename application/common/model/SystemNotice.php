<?php

namespace app\common\model;


class SystemNotice extends BaseModel
{
    //定义表明
    protected $table = 'db_system_notice';

    protected static function init()
    {
        self::event('before_write', function ($eye) {
            $eye->create_time = time();
        });
    }

    public function getContentAttr($value)
    {
        return strip_tags($value);
    }

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d', $value);
    }

}