<?php

namespace app\console\controller;


use app\common\controller\BaseController;
use app\common\model\AuthManager as AuthManagerModel;
use think\Request;
use think\Session;
use think\Url;

class Login extends BaseController
{
    protected $beforeActionList = [
        'checkLogin' => ['only' => 'index'],
    ];

    public function index(Request $request, AuthManagerModel $manager)
    {
        if ($request->isPost()) {
            $data = $request->param();
            $result = $manager->login($data);

            if ($result) {
                $this->success('登录成功', Url::build('console/dashboard/index'));
            } else {
                $this->error('登录失败', Url::build('console/login/index'));
            }

        }

        return $this->fetch('index', [
            "meta_title" => '管理登录'
        ]);
    }

    /**
     * 检测用户是否登录
     */
    protected function checkLogin()
    {
        if (Session::has('console_manager_id'))
            $this->redirect(Url::build('console/dashboard/index'));
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        Session::delete('console_manager_id');
        $this->redirect(Url::build('console/login/index'));
    }
}