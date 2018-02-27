<?php

namespace app\console\controller;

use app\common\controller\BaseController;
use app\console\model\Upload;
use think\Request;
use think\response\Json;

class Common extends BaseController
{
    //上传图片
    public function upload(Request $request)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = $request->file('file_data');

        // 移动到框架应用根目录/public/uploads/ 目录下
        $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'image';

        $info = $file->rule('date')->move($path);

        if ($info) {
            Upload::create([
                'file_path' => '/uploads/image/' . date('Ymd') . '/' . $info->getFilename(),
                'file_name' => $info->getFilename(),
                'file_time' => time(),
            ]);

            return Json::create([
                'code' => 1,
                'image' => '/uploads/image/' . date('Ymd') . '/' . $info->getFilename(),
            ]);

        } else {

            return Json::create([
                'code' => 0,
                'error' => $info->getError(),
            ]);

        }
    }

    //删除图片
    public function updel(Request $request)
    {
        $filename = $request->param('filename');

        if (!empty($filename)) {
            unlink(ROOT_PATH . 'public' . $filename);
            $data['code'] = 1;
        } else {
            $data['code'] = 2;
        }

        return json_encode($data);
    }
}