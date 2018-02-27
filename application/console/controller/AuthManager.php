<?php

namespace app\console\controller;

use app\common\model\AuthGroup as AuthGroupModel;
use app\common\model\AuthGroupAccess as AuthGroupAccessModel;
use app\common\model\AuthManager as AuthManagerModel;
use think\Request;
use think\Url;

/**
 * Class Manager    管理员控制器
 * @package app\console\controller
 */
class AuthManager extends Console
{

    /**
     * [index    管理员列表]
     * console/auth_manager/index
     * @return mixed
     */
    public function index(AuthManagerModel $authManager)
    {
        $auth_manager_list = $authManager
            ->order('create_time', 'DESC')
            ->select();

        return $this->fetch('index', [
            "list" => $auth_manager_list
        ]);
    }

    /**
     * [create    添加管理员]
     * console/auth_manager/create
     * @return mixed
     */
    public function create(Request $request, AuthManagerModel $authManager, AuthGroupModel $authGroup)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $authManager->allowField(true)->isUpdate(false)->save($data);

            if ($result) {
                $this->success('添加成功！', Url::build('index'));
            } else {
                $this->error('添加失败！');
            }
        }

        $auth_group_list = $authGroup
            ->field('id,title')
            ->where(['status' => 1])
            ->select();

        return $this->fetch('create', [
            'group_list' => $auth_group_list,
        ]);
    }

    /**
     * [update    修改管理员]
     * console/auth_manager/update
     * @return mixed
     */
    public function update(Request $request, AuthManagerModel $authManager, AuthGroupModel $authGroup, AuthGroupAccessModel $authGroupAccess)
    {
        if ($request->isPost()) {
            $data = $request->post();

            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = md5(md5($data['password']));
            }

            $result = $authManager->allowField(true)->isUpdate(true)->save($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }
        }

        $auth_manager_data = $authManager
            ->where([
                'id' => $request->param('id')
            ])
            ->find();

        $auth_group_list = $authGroup
            ->field('id,title')
            ->where(['status' => 1])
            ->select();

        $auth_group_access_data = $authGroupAccess
            ->where([
                'manger_id' => $request->param('id')
            ])->column('group_id');

        return $this->fetch('create', [
            'group_access' => $auth_group_access_data,
            'group_list' => $auth_group_list,
            'vo' => $auth_manager_data,
        ]);
    }

    /**
     * [update    更新管理员]
     * console/auth_manager/update
     * @return mixed
     */
    public function upfield(Request $request, AuthManagerModel $authManager)
    {
        $data = $request->param();
        $result = $authManager->where('id', $data['id'])->update($data);

        if ($result) {
            $this->success('更新成功！');
        } else {
            $this->error('更新失败！');
        }
    }

    /**
     * [delete    删除管理员]
     * console/auth_manager/delete
     * @return mixed
     */
    public function delete(Request $request, AuthManagerModel $authManager)
    {
        $result = $authManager->where('id', 'in', $request->param('id/a'))->delete();

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }


}