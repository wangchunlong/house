<?php
namespace app\[MODULE]\model;

use think\Model;

class [MODEL] extends Base
{

    /*[createdata  模型添加]*/
    public function createdata($data)
    {
        $model = new [MODEL]();

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
        $model = new [MODEL]();

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
        $model = new [MODEL]();

        $result = $model->where('id','in',$data)->delete();

        if($result){
            return true;
        }else{
            return false;
        }
    }
}
