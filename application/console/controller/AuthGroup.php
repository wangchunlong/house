<?php

namespace app\console\controller;

use app\common\model\AuthGroup as AuthGroupModel;
use app\common\model\AuthGroupRule;
use app\common\model\AuthRule as AuthRuleModel;
use think\Request;
use think\Url;

/**
 * Class AuthGroup      权限角色控制器
 * @package app\console\controller
 */
class AuthGroup extends Console
{
    /**
     * [index    权限角色列表]
     * console/auth_group/index
     * @return mixed
     */
    public function index(AuthGroupModel $authGroup)
    {
        $auth_group_list = $authGroup->select();

        return $this->fetch('index', [
            "list" => $auth_group_list,
        ]);
    }

    /**
     * [create    添加权限角色]
     * console/auth_group/create
     * @return mixed
     */
    public function create(Request $request, AuthGroupModel $authGroup, AuthRuleModel $authRule)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $authGroup->allowField(true)->isUpdate(false)->save($data);

            if ($result) {
                $this->success('添加成功！', Url::build('index'));
            } else {
                $this->error('添加失败！');
            }
        }

        $auth_rule_list = $authRule
            ->field('id,title,controller')
            ->order('name', 'ASC')
            ->select();

        $auth_rule_data = array();
        foreach ($auth_rule_list as $value) {
            $auth_rule_data[$value['controller']][] = $value;
        }
        array_values($auth_rule_data);
        return $this->fetch('create', [
            'rule_list' => $auth_rule_data,
        ]);
    }


    /**
     * [update    修改权限角色]
     * console/auth_group/update
     * @return mixed
     */
    public function update(Request $request, AuthGroupModel $authGroup, AuthRuleModel $authRule, AuthGroupRule $authGroupRule)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $authGroup->allowField(true)->isUpdate(true)->save($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }
        }

        $auth_rule_list = $authRule
            ->field('id,title,controller')
            ->where(['status' => 1])
            ->select();

        $auth_rule_data = array();
        foreach ($auth_rule_list as $value) {
            $auth_rule_data[$value['controller']][] = $value;
        }

        $auth_group_data = $authGroup
            ->where([
                'id' => $request->param('id')
            ])
            ->find();

        $auth_group_rule = $authGroupRule
            ->where([
                'group_id' => $request->param('id')
            ])->column('rule_id');


        return $this->fetch('create', [
            'group_rule' => $auth_group_rule,
            'rule_list' => $auth_rule_data,
            'vo' => $auth_group_data
        ]);
    }


    /**
     * [upfield    更新权限角色]
     * console/auth_group/upfield
     * @return mixed
     */
    public function upfield(Request $request, AuthGroupModel $authGroup)
    {
        $data = $request->param();
        $result = $authGroup->where('id', $data['id'])->update($data);

        if ($result) {
            $this->success('更新成功！');
        } else {
            $this->error('更新失败！');
        }
    }

    /**
     * [delete    删除权限角色]
     * console/auth_group/delete
     * @return mixed
     */
    public function delete(Request $request, AuthGroupModel $authGroup)
    {
        $result = $authGroup->where('id', 'in', $request->param('id/a'))->delete();

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}