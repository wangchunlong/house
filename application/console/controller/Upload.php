<?php

namespace app\console\controller;

use app\console\model\Upload as Model;
use think\Request;

class Upload extends Console
{
    /**
     * [index    上传文件列表]
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $model = new Model();
        $list = $model->paginate(18, false, $request->param());

        return $this->fetch('index', [
            'list' => $list,
        ]);
    }

    /**
     * [delete    删除上传文件]
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $model = new Model();

        $result = $model::destroy($request->param('id/a'));

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}