<?php
namespace app\console\validate;

use think\Validate;

class Goodsattr extends Base
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
