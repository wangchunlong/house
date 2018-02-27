<?php

namespace app\console\model;

use app\common\model\SystemActive;

class Active extends SystemActive
{
    protected $table = 'db_system_active';

    protected static function init()
    {
        /*写入事件*/
        self::event('before_write', function ($query) {
            $query->create_time = time();
        });
    }

    /*[createdata  模型添加]*/
    public function createdata($data)
    {
        $model = new Active();

        $result = $model->allowField(true)->isUpdate(false)->save($data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*[updatedata  模型修改]*/
    public function updatedata($data)
    {
        $model = new Active();

        $result = $model->allowField(true)->isUpdate(true)->save($data);

        if ($result || $result == 0) {
            return true;
        } else {
            return false;
        }
    }

    /*[deletedata  模型删除]*/
    public function deletedata($data)
    {
        $model = new Active();

        $result = $model->where('id', 'in', $data)->delete();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
