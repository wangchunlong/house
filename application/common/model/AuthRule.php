<?php

namespace app\common\model;


use think\Model;

class AuthRule extends Model
{
    protected static function init()
    {
        // 写入事件
        self::event('before_write', function ($query) {
            $query->data['name'] = $query->module . '/' . $query->controller . '/' . $query->action;
        });
    }
}