<?php

namespace app\console\controller;

use app\common\model\GoodsPoolFlag as Model;
use think\Request;

class Goodsflag extends Base
{
    /**
     * [index  商品属性列表]
     * @return [type] [description]
     */
    public function index()
    {
        $model = new Model();
        $goods_pool_flag_list = $model
            ->order('id', 'ASC')
            ->select();

        return $this->fetch('index', [
            'list' => $goods_pool_flag_list,
        ]);
    }

    /**
     * [create      添加商品属性方法]
     * @return [type] [description]
     */
    public function create(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Goodsflag.create');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->createdata($data);

            if ($result) {
                $this->success('添加成功', 'console/Goodsflag/index');
            } else {
                $this->error('添加失败');
            }
        }

        return $this->fetch('create', []);
    }

    /**
     * [update      修改商品属性方法]
     * @return [type]     [description]
     */
    public function update(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Goodsflag.update');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->updatedata($data);

            if ($result) {
                $this->success('修改成功', 'console/Goodsflag/index');
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
     * [upfield         更新商品属性字段]
     * @return [type] [description]
     */
    public function upfield(Request $request)
    {
        $data = $request->param();
        $model = new Model();
        $result = $model->where('id', $data['id'])->update($data);

        if ($result) {
            $this->success('更新成功！');
        } else {
            $this->error('更新失败！');
        }
    }

    /**
     * [delete      删除商品属性方法]
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
