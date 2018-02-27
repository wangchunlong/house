<?php

namespace app\console\controller;

use app\common\model\WebInfo as WebModel;
use think\Request;
use think\Url;


class Webinfo extends Console
{
    /**
     * [console/webinfo/index    Web页管理]
     * @param WebModel $webInfo
     * @return mixed
     */
    public function index(WebModel $webInfo)
    {
        $webinfo_list = $webInfo
            ->order('id', 'ASC')
            ->select();

        return $this->fetch('index', [
            'list' => $webinfo_list,
        ]);

    }


    /**
     * [console/webinfo/update    更新Web页管理]
     * @param Request $request
     * @param WebModel $webInfo
     * @return mixed
     */
    public function update(Request $request, WebModel $webInfo)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $result = $webInfo->allowField(true)->isUpdate(true)->save($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }

        }

        $id = $request->param('id');

        $data = $webInfo::get($id);

        return $this->fetch('create', [
            'mate_title' => '修改Web页',
            "vo" => $data,
        ]);
    }
}