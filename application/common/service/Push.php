<?php

namespace app\common\service;

class Push
{
    private $appkey = false;
    private $masterSecret = false;

    public function __construct($appkey, $masterSecret)
    {
        $this->appkey = $appkey;
        $this->masterSecret = $masterSecret;
    }

    public function execute($param)
    {
        $base64 = base64_encode($this->appkey . ":" . $this->masterSecret);
        $header = array("Authorization:Basic $base64", "Content-Type:application/json");
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, "https://api.jpush.cn/v3/push"); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch); // 运行curl
        curl_close($ch);
        return $data;
    }

    public static function total($title, $ordersn)
    {
        $data = [
            "platform" => "all",
            "audience" => "all",
            "notification" => [
                "android" => [
                    "alert" => $title,
                    "extras" => ["ordersn" => "20180101123456"],
                ],
                "ios" => [
                    "alert" => $title,
                    "sound" => "default",
                    "content-available" => true,
                    "extras" => ["ordersn" => "20180101123456"],
                ]
            ],
            "options" => [
                "time_to_live" => 60,
                "apns_production" => true,
            ],
        ];

        return self::execute(json_encode($data));
    }

    public static function user($title, $uid, $ordersn)
    {
        $data = [
            "platform" => "all",
            "audience" => [
                "tag" => [$uid]
            ],
            "notification" => [
                "android" => [
                    "alert" => $title,
                    "extras" => ["ordersn" => $ordersn],
                ],
                "ios" => [
                    "alert" => $title,
                    "sound" => "default",
                    "content-available" => true,
                    "extras" => ["ordersn" => $ordersn],
                ]
            ],
            "options" => [
                "time_to_live" => 60,
                "apns_production" => true,
            ],
        ];

        return self::execute(json_encode($data));
    }

    public static function shop($title, $uid, $ordersn)
    {
        $data = [
            "platform" => "all",
            "audience" => [
                "tag" => [$uid]
            ],
            "notification" => [
                "android" => [
                    "alert" => $title,
                    "extras" => ["ordersn" => $ordersn],
                ],
                "ios" => [
                    "alert" => $title,
                    "sound" => "default",
                    "content-available" => true,
                    "extras" => ["ordersn" => $ordersn],
                ]
            ],
            "options" => [
                "time_to_live" => 60,
                "apns_production" => true,
            ],
        ];

        return self::execute(json_encode($data));
    }
}