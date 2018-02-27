<?php

namespace app\console\controller;

use app\common\controller\BaseController;
use app\common\service\AuthService;
use app\console\model\Log;
use think\Cache;
use think\Config;
use think\Db;
use think\Request;
use think\Session;

class Console extends BaseController
{
    protected function _initialize()
    {
        if (!Session::has('console_manager_id')) {
            $this->redirect('console/login/index');
        }

        /*当前模块名*/
        $module = Request::instance()->module();
        $module_name = strtolower($module);
        $this->assign('module', $module_name);

        /*当前控制名*/
        $controller = Request::instance()->controller();
        $controller_name = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $controller), "_"));
        $this->assign('controller', $controller_name);

        /*当前方法名*/
        $action = Request::instance()->action();
        $action_anme = strtolower($action);
        $this->assign('action', $action_anme);

        $console_manager_id = Session::get('console_manager_id');

        $auth = new AuthService;
        $result = $auth->check($module_name . '/' . $controller_name . '/' . $action_anme, $console_manager_id);

        if (!$result) {
            $this->error('抱歉您没有权限');
        }

        /*当前账号的用户信息*/
        $user_data = Db::name('AuthManager')
            ->field('id,username,avatar,nickname,mobile')
            ->find($console_manager_id);
        $this->assign('user', $user_data);

        /*左侧菜单*/
        $menu_left = Config::get('config_menu');
        $this->assign('menu_left', $menu_left);

        $webconfig = Cache::get('webconfig');

        $this->assign('app_title', $webconfig['web_name']);

        $mate_title = Db::name('auth_rule')
            ->where('name', 'eq', $module_name . '/' . $controller_name . '/' . $action_anme)
            ->value('title');

        $this->assign('mate_title', $mate_title);

        $param = Request::instance()->param();
        if (!empty($param)) {
            Log::createdata([
                'manager_id' => $console_manager_id,
                'name' => $module_name . '/' . $controller_name . '/' . $action_anme,
                'parameter' => json_encode($param),
                'ip' => gethostbyname($_SERVER['SERVER_NAME']),
                'title' => $mate_title,
                'create_time' => time(),
            ]);
        }


    }
}