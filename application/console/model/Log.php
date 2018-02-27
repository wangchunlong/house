<?php

namespace app\console\model;


use app\common\model\SystemLog;

class Log extends SystemLog
{
    protected $table = 'db_system_log';

    public function manager()
    {
        /*设置预载入查询方式为JOIN方式      setEagerlyType(0)*/
        /*设置预载入查询方式为IN方式        setEagerlyType(1)*/
        return $this->hasOne('Manager', 'id', 'manager_id')
            ->field('id,username,nickname')
            ->bind('username,nickname');

//        return $this->hasOne('Manager', 'id', 'manager_id')
//            ->bind([
//                'email',
//                'truename' => 'nickname',
//                'profile_id' => 'id',
//            ]);
    }

    /*[createdata  模型添加]*/
    public static function createdata($data)
    {
        $model = new Log();

        $result = $model->allowField(true)->isUpdate(false)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}