<?php
namespace app\console\model;

use app\common\model\SystemAdvert;
use think\Model;

class Advert extends SystemAdvert
{
    protected $table = 'db_system_advert';

    /*[createdata  模型添加]*/
    public function createdata($data)
    {
        $model = new Advert();

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
        $model = new Advert();

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
        $model = new Advert();

        $result = $model->where('id','in',$data)->delete();

        if($result){
            return true;
        }else{
            return false;
        }
    }
}
