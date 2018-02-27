<?php

namespace app\console\controller;

use app\common\model\AuthRule as AuthRuleModel;
use think\Request;
use think\Url;

/**
 * Class AuthRule   权限规则控制器
 * @package app\console\controller
 */
class AuthRule extends Console
{
    /**
     * [index    权限规则列表]
     * console/AuthRule/index
     * @return mixed
     */
    public function index(AuthRuleModel $authRule)
    {
        $auth_rule_list = $authRule
            ->order('name', 'ASC')
            ->select();

        return $this->fetch('index', [
            "list" => $auth_rule_list,
        ]);
    }

    /**
     * [create    添加权限规则]
     * console/AuthRule/create
     * @return mixed
     */
    public function create(Request $request, AuthRuleModel $authRule)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $authRule->allowField(true)->isUpdate(false)->save($data);

            if ($result) {
                $this->success('添加成功！', Url::build('index'));
            } else {
                $this->error('添加失败！');
            }
        }

        return $this->fetch('create', []);
    }

    /**
     * [update    修改权限规则]
     * console/AuthRule/update
     *
     * @param Request $request
     * @param AuthRuleModel $authRule
     * @return mixed
     */
    public function update(Request $request, AuthRuleModel $authRule)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $authRule->allowField(true)->isUpdate(true)->save($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }
        }

        $auth_rule_data = $authRule
            ->where(['id' => $request->param('id/i')])
            ->find();

        return $this->fetch('create', [
            "vo" => $auth_rule_data,
        ]);

    }

    /**
     * [upfield    更新权限规则]
     * console/AuthRule/upfield
     *
     * @param Request $request
     * @param AuthRuleModel $authRule
     */
    public function upfield(Request $request, AuthRuleModel $authRule)
    {
        $data = $request->param();
        $result = $authRule->allowField(true)->isUpdate(true)->save($data);

        if ($result) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * [delete    删除权限规则]
     * console/AuthRule/delete
     *
     * @param Request $request
     * @param AuthRuleModel $authRule
     */
    public function delete(Request $request, AuthRuleModel $authRule)
    {
        $id = $request->param('id/a');
        $result = $authRule->where('id', 'in', $id)->delete();

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}