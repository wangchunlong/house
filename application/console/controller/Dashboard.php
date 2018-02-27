<?php

namespace app\console\controller;
use think\Db;


/**
 * Class Dashboard  控制台控制器
 * @package app\console\controller
 */
class Dashboard extends Console
{
    /**
     * [dashboard    控制台]
     * console/dashboard/index
     * @return mixed
     */
    public function index()
    {
        //会员总人数
        $user_count = 10;
        //总商品
        $goods_count = 100;
        //总订单
        $order_count = 1000;
        //总反馈消息
        $shop_count = 20;

        $system_info = [
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '用户的IP地址' => $_SERVER['REMOTE_ADDR'],
            '主机名' => $_SERVER['SERVER_NAME'],
            '通信协议' => $_SERVER['SERVER_PROTOCOL'],
        ];

        $system_info2 = [
            'ThinkPHP版本' => THINK_VERSION,
            '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '服务器域名/IP' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
        ];

        return $this->fetch('index', [
            'system_info' => $system_info,
            'system_info2' => $system_info2,
            'user_count' => $user_count,
            'goods_count' => $goods_count,
            'order_count' => $order_count,
            'shop_count' => $shop_count,
        ]);
    }
}