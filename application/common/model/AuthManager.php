<?php

namespace app\common\model;

use think\Model;
use think\Session;

class AuthManager  extends Model
{
    // 登录方法
    public static function login($data)
    {
        $where = [
            'username' => $data['username'],
            'password' => md5(md5($data['password'])),
            'state' => 1
        ];

        //取出数据/*读取条件*/
        $manager_data = self::where($where)->find();

        // 用户名&密码 验证成功
        if (!empty($manager_data)) {
            Session::set('console_manager_id', $manager_data['id']);
            return true;
        }

        return false;
    }
}