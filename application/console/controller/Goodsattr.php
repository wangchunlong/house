<?php

namespace app\console\controller;

use app\common\model\GoodsPoolAttr as Model;
use think\Request;

/**
 * Class Goodsattr  商品属性值
 * @package app\console\controller
 */
class Goodsattr extends Base
{
    /**
     * [index  商品属性值列表]
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        $flag_id = $request->param('flag_id');
        $model = new Model();

        $goods_pool_attr_list = $model
            ->where('flag_id', 'eq', $flag_id)
            ->order('id', 'ASC')
            ->select();

        return $this->fetch('index', [
            'list' => $goods_pool_attr_list,
            'flag_id' => $flag_id,
        ]);
    }

    /**
     * [create 添加商品属性值]
     * @return [type] [description]
     */
    public function create(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Goodsattr.create');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->createdata($data);

            if ($result) {
                $this->success('添加成功', 'console/Goodsattr/index');
            } else {
                $this->error('添加失败');
            }
        }

        $flag_id = $request->param('flag_id');

        return $this->fetch('create', [
            'flag_id' => $flag_id,
        ]);
    }

    /**
     * [update 修改商品属性值]
     * @return [type]     [description]
     */
    public function update(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Goodsattr.update');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->updatedata($data);

            if ($result) {
                $this->success('修改成功', 'console/Goodsattr/index');
            } else {
                $this->error('修改失败');
            }
        }

        $model = new Model();
        $data = $model->where('id', 'eq', $request->param('id'))->find();

        return $this->fetch('create', [
            'vo' => $data,
        ]);
    }

    /**
     * [delete 删除商品属性值]
     * @return [type] [description]
     */
    public function delete(Request $request)
    {
        $model = new Model();
        $result = $model->deletedata($request->param('id/a'));

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}
