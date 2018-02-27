<?php

namespace app\common\service;


class Generate
{
    private $MODULE;/*生成代码的模块*/
    private $CONTROLLER;/*生成控制器*/
    private $MODEL;/*生成模型*/
    private $VALIDATE;/*生成验证器*/
    private $NAME;/*生成控制去的名称*/
    private $folder;/*初始化生成的文件夹*/
    private $blacklistName;/*控制器黑名单*/

    public function __construct($data)
    {
        $this->MODULE = $data['module'];

        $this->folder = [
            "behavior",
            "controller",
            "model",
            "validate",
            "view",
        ];

        $this->blacklistName = [
            'Ajax',
            'AuthGroup',
            'Manager',
            'AuthRule',
            'Baksql',
            'Common',
            'Console',
            'Manager',
        ];

        $this->createFolder();
    }

    public function run($data)
    {
        // 检查目录是否可写
        $pathCheck = APP_PATH . $this->MODULE . DS;

        if (!$this->checkWritable($pathCheck)) {
            throw new \Exception("目录没有权限不可写，请执行一下命令修改权限：<br>chmod -R 755 " . realpath($pathCheck), 403);
        }

        $this->CONTROLLER   = $data['controller'];
        $this->MODEL        = $data['model'];
        $this->VALIDATE     = $data['validate'];
        $this->NAME         = $data['name'];


        $result = array();

        if (in_array("1", $data['group'])) {
            /*判断是否在黑名单中*/
            if (in_array($data['controller'], $this->blacklistName)) {
                throw new \Exception('该控制器不允许创建');
            }

            /*新建文件的路径*/
            $filePath = APP_PATH . $this->MODULE . DS . "controller" . DS . $this->CONTROLLER . ".php";

            /*检查当前模型文件 */
            if (is_file($filePath)) {
                throw new \Exception('当前控制器已存在');
            }

            $result['controller'] = $this->createController($filePath);
        }

        if (in_array("2", $data['group'])) {

            /*新建文件的路径*/
            $filePath = APP_PATH . $this->MODULE . DS . "model" . DS . $this->MODEL . ".php";

            /*检查当前模型文件 */
            if (is_file($filePath)) {
                throw new \Exception('当前模型已存在');
            }

            $result['model'] = $this->createModel($filePath);
        }

        if (in_array("3", $data['group'])) {

            /*新建文件的路径*/
            $filePath = APP_PATH . $this->MODULE . DS . "validate" . DS . $this->VALIDATE . ".php";

            /*检查当前模控制器文件*/
            if (is_file($filePath)) {
                throw new \Exception('当前验证器已存在');
            }

            $result['validate'] = $this->createValidate($filePath);
        }

        return $result;
    }

    /*创建控制器文件*/
    private function createController($filePath)
    {
        $template = file_get_contents(APP_PATH . 'template' . DS . "controller.tpl");

        $content = str_replace(
            ["[MODULE]", "[CONTROLLER]", "[NAME]"],
            [$this->MODULE, $this->CONTROLLER, $this->NAME],
            $template
        );

        return file_put_contents($filePath, $content);
    }

    /*创建模型文件*/
    private function createModel($filePath)
    {
        $template = file_get_contents(APP_PATH . 'template' . DS . "model.tpl");

        $content = str_replace(
            ["[MODULE]", "[MODEL]", "[NAME]"],
            [$this->MODULE, $this->MODEL, $this->NAME],
            $template
        );

        return file_put_contents($filePath, $content);
    }

    /*创建验证器文件*/
    private function createValidate($filePath)
    {
        $template = file_get_contents(APP_PATH . 'template' . DS . "validate.tpl");

        $content = str_replace(
            ["[MODULE]", "[VALIDATE]", "[NAME]"],
            [$this->MODULE, $this->VALIDATE, $this->NAME],
            $template
        );

        return file_put_contents($filePath, $content);
    }

    /*检查当前模块目录是否可写    @return bool*/
    private function checkWritable($path = '')
    {
        try {
            $path = $path ? $path : APP_PATH . $this->MODULE . DS;
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            // 解除锁定
            unlink($testFile);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*创建目录*/
    private function createFolder()
    {
        foreach ($this->folder as $folder) {
            if (!is_dir(APP_PATH . $this->MODULE . DS . $folder)) {
                // 创建目录
                mkdir(APP_PATH . $this->MODULE . DS . $folder, 0755, true);
            }
        }
    }
}