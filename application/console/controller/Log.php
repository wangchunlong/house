<?php

namespace app\console\controller;

use app\console\model\Log as Model;
use think\Request;

class Log extends Console
{
    public function index(Request $request)
    {
        $model = new Model();
        
        $log_list = $model
            ->with('manager')
            ->order('create_time', 'DESC')
            ->paginate(10, false, $request->param());

        return $this->fetch('index', [
            'list' => $log_list
        ]);
    }
}