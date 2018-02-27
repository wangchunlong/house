<?php
namespace app\console\model;

use think\Model;

class Icon extends Base
{
    protected $table = 'db_system_icon';

    /*[createdata  模型添加]*/
    public function createdata($data)
    {
        $model = new Icon();

        $result = $model->allowField(true)->isUpdate(false)->save($data);

        if($result){
            return true;
        }else{
            return false;
        }
    }

    /*[updatedata  模型修改]*/
    public function updatedata($data)
    {
        $model = new Icon();

        $result = $model->allowField(true)->isUpdate(true)->save($data);

        if($result || $result == 0){
            return true;
        }else{
            return false;
        }
    }

    /*[deletedata  模型删除]*/
    public function deletedata($data)
    {
        $model = new Icon();

        $result = $model->where('id','in',$data)->delete();

        if($result){
            return true;
        }else{
            return false;
        }
    }
}
