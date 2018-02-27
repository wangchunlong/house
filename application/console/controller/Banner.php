<?php

namespace app\console\controller;

use app\console\model\Banner as Model;
use think\Request;

/**
 * Class Banner     首页轮播控制器
 * @package app\console\controller
 */
class Banner extends Base
{
    /**
     * [index  首页轮播列表]
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        $model = new Model();

        $banner_list = $model
            ->order('create_time', 'DESC')
            ->paginate(10, false, $request->param());

        return $this->fetch('index', [
            'list' => $banner_list,
        ]);
    }

    /**
     * [create      添加首页轮播]
     * @return [type] [description]
     */
    public function create(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Banner.create');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->createdata($data);

            if ($result) {
                $this->success('添加成功', 'console/Banner/index');
            } else {
                $this->error('添加失败');
            }
        }

        return $this->fetch('create', []);
    }

    /**
     * [update  修改首页轮播]
     * @return [type]     [description]
     */
    public function update(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->param();

            /*验证参数*/
            $result = $this->validate($data, 'Banner.update');

            if (true !== $result) {
                $this->error($result);
            }

            $model = new Model();
            $result = $model->updatedata($data);

            if ($result) {
                $this->success('修改成功', 'console/Banner/index');
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
     * [upfield     更新首页轮播]
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
     * [delete      删除首页轮播]
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
