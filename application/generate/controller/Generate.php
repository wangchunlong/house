<?php

namespace app\generate\controller;

use app\common\model\AuthRule;
use app\common\service\Generate as GenerateService;
use think\Controller;
use think\Request;

class Generate extends Controller
{
    public function index(Request $request)
    {
        if ($request->isPost()) {

            $generate = new GenerateService(['module' => 'console']);

            AuthRule::create([
                'title' => "{$request->param('name/s')}列表",
                'module' => "console",
                'controller' => $request->param('controller/s', '', 'strtolower'),
                'action' => 'index',
                'status' => '1',
                'type' => '0',
            ]);
            AuthRule::create([
                'title' => "添加{$request->param('name/s')}",
                'module' => "console",
                'controller' => $request->param('controller/s', '', 'strtolower'),
                'action' => 'create',
                'status' => '1',
                'type' => '0',
            ]);
            AuthRule::create([
                'title' => "修改{$request->param('name/s')}",
                'module' => "console",
                'controller' => $request->param('controller/s', '', 'strtolower'),
                'action' => 'update',
                'status' => '1',
                'type' => '0',
            ]);
            AuthRule::create([
                'title' => "更新{$request->param('name/s')}",
                'module' => "console",
                'controller' => $request->param('controller/s', '', 'strtolower'),
                'action' => 'upfield',
                'status' => '1',
                'type' => '0',
            ]);
            AuthRule::create([
                'title' => "删除{$request->param('name/s')}",
                'module' => "console",
                'controller' => $request->param('controller/s', '', 'strtolower'),
                'action' => 'delete',
                'status' => '1',
                'type' => '0',
            ]);
            $data = [
                'controller' => $request->param('controller/s'),
                'model' => $request->param('controller/s'),
                'validate' => $request->param('controller/s'),
                'group' => $request->param('group/a'),
                'name' => $request->param('name/s'),
            ];

            $result = $generate->run($data);
            halt($result);
        }

        return $this->fetch('index');
    }
}