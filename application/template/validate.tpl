<?php
namespace app\[MODULE]\validate;

use think\Validate;

class [VALIDATE] extends Base
{
    protected $rule =       [
        /*'id' => 'require',*/
    ];

    protected $message =    [
       /*'login.require' => '手机号未填写',*/
    ];

    protected $scene =      [
        'create' => [
            /*'login' => 'require|checkLogin|checkMobile',*/
        ],

        'update' => [
            /*'login' => 'require|checkLogin|checkMobile',*/
        ],
    ];
}
