<?php

namespace app\console\controller;

use app\common\controller\BaseController;
use think\Session;

class Index extends BaseController
{
    public function index()
    {
        if (Session::has('console_manager_id')) {
            $this->redirect('Dashboard/index');
        } else {
            $this->redirect('Login/index');
        }
    }
}