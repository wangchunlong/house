<?php

namespace app\interfaces\validate;

use app\common\model\User;
use think\Cache;
use think\Validate;

class UserAuth extends Validate
{
    protected $rule = [
        'login' => 'require',

        'oldpassword' => 'require',
        'password' => 'require',
        'repassword' => 'confirm:password',

        'nickname' => 'require',
        'sms_code' => 'require',
        'user_id' => 'require',
    ];

    protected $message = [
        'login.require' => '手机号未填写',
        'login.unique' => '手机号已注册',

        'login.regex' => '手机号码错误',

        'oldpassword.require' => '原密码不能为空',
        'password.require' => '密码不能为空',
        'repassword.confirm' => '两次密码不一致',

        'nickname.require' => '昵称未填写',
        'sms_code.require' => '短信验证码不能为空',

        'user_id.require' => '用户 id 缺失',
    ];


    // 自定义验证短信验证
    protected function checkSMSCodeByMobile($value, $rule, $data)
    {
        $key = $rule . $data['login'];

        if (!Cache::has($key)) {
            return '验证码已过期,请重新获取';
        }

        return ((string)$value == Cache::get($key)) ? true : '验证码错误';
    }


    // 原密码验证
    protected function checkPassword($value, $rule, $data)
    {
        $user_data = User::get($data['user_id']);

        return ($user_data['password'] == md5(md5($value))) ? true : '原密码错误,请重新输入';
    }

    /*用户名验证*/
    protected function checkLogin($value, $rule, $data)
    {
        $user_data = User::get(['login' => $value]);

        return (!empty($user_data)) ? true : '手机号码未绑定任何用户';
    }

    /*验证手机号是否注册*/
    protected function checkRegister($value, $rule, $data)
    {
        $user_data = User::get(['login' => $value]);

        return (empty($user_data)) ? true : '该手机号已注册';
    }

    /*验证手机号格式是否正确*/
    protected function checkMobile($value, $rule, $data)
    {
        return (preg_match("/^1[34578]{1}\d{9}$/", $value)) ? true : "手机号码错误";
    }

    protected $scene = [

        'login' => [
            'login' => 'require|checkLogin|checkMobile',
            'password'
        ],

        'sendRegisterSMS' => [
            'login' => 'require|checkMobile|checkRegister',
        ],

        'register' => [
            'password',
            'nickname',
            'sms_code' => 'require|checkSMSCodeByMobile:user_register',
            'login' => 'require|unique:user|checkMobile|checkRegister',
        ],


        'sendResetPasswordSMS' => [
            'login' => 'require|checkMobile|checkLogin',
        ],


        'checkResetPassword' => [
            'login' => 'require|checkMobile|checkLogin',
            'sms_code' => 'require|checkSMSCodeByMobile:user_reset'
        ],

        'resetPassword' => [
            'user_id',
            'password',
            'repassword'
        ],

        'modifyPassword' => [
            'user_id',
            'oldpassword' => 'require|checkPassword',
            'password',
            'repassword'
        ],

    ];
}
