<?php

namespace app\common\service;

use think\Config;

/**
 * Class Hx  环信第三方
 */
class Hx
{
    // public       表示全局，类内部外部子类都可以访问；
    // private      表示私有的，只有本类内部可以使用；
    // protected    表示受保护的，只有本类或子类或父类中可以访问；

    //  org_name 企业的唯一标识，开发者在环信开发者管理后台注册账号之后自动生成的企业 ID。
    protected $org_name;

    // app_name 同一“企业”下“APP”唯一标识，开发者在环信开发者管理后台创建应用时填写的“应用名称”。
    protected $app_name;

    public function __construct()
    {
        $this->org_name = Config::get('config_hx.org_name');
        $this->app_name = Config::get('config_hx.app_name');
    }


    // 注册 IM 用户  POST  /{org_name}/{app_name}/users
    public function regUser($username, $password, $nikename)
    {
        $url = "users";

        $body = array(
            "username" => $username,
            "password" => $password,
            "nikename" => $nikename,
        );

        $body = json_encode($body);

        $header = array(
            $this->getToken()
        );

        $method = "POST";

        $response = $this->request($url, $body, $header, $method);

        $arrayResult = json_decode($response, true);

        return $arrayResult;
    }

    // 获取 IM 用户的黑名单   GET  /{org_name}/{app_name}/users/{owner_username}/blocks/users
    public function getUserBlocksList($owner_username)
    {
        //获取一个IM用户的黑名单。黑名单中的用户无法给该 IM 用户发送消息。
        $url = "users/{$owner_username}/blocks/users";

        $body = array();

        $body = json_encode($body);

        $header = array(
            $this->getToken()
        );

        $method = "GET";

        $response = $this->request($url, $body, $header, $method);

        $arrayResult = json_decode($response, true);

        return $arrayResult;
    }


    // 往 IM 用户的黑名单中加人  GET /{org_name}/{app_name}/users/{owner_username}/blocks/users
    public function getUserBlocksAdd($owner_username, $blocks_username)
    {
        //往一个 IM 用户的黑名单中加人，一次可以添加一个或多个。黑名单中的用户无法给该 IM 用户发送消息。
        $url = "users/{$owner_username}/blocks/users";

        $body = array(
            'usernames' => array(
                $blocks_username
            )
        );

        $body = json_encode($body);

        $header = array(
            $this->getToken()
        );

        $method = "POST";

        $response = $this->request($url, $body, $header, $method);

        $arrayResult = json_decode($response, true);

        return $arrayResult;
    }

    // 从 IM 用户的黑名单中减人  DELETE /{org_name}/{app_name}/users/{owner_username}/blocks/users/{blocked_username}
    public function getUserBlocksDel($owner_username, $blocked_username)
    {
        //从一个 IM 用户的黑名单中减人。将用户从黑名单移除后，恢复好友关系，可以互相收发消息。
        $url = "users/{$owner_username}/blocks/users{$blocked_username}";

        $body = array();

        $body = json_encode($body);

        $header = array(
            $this->getToken()
        );

        $method = "DELETE";

        $response = $this->request($url, $body, $header, $method);

        $arrayResult = json_decode($response, true);

        return $arrayResult;
    }

    // 获取聊天记录 GET /{org_name}/{app_name}/chatmessages


    //获取token  先获取app管理员token POST /{org_name}/{app_name}/token
    public function getToken()
    {
        $url = "token";

        $body = array(
            "grant_type" => Config::get('hx_config.grant_type'),
            "client_id" => Config::get('hx_config.client_id'),
            "client_secret" => Config::get('hx_config.client_secret')
        );

        $body = json_encode($body);

        $header = array();

        $method = "POST";

        $response = $this->request($url, $body, $header, $method);

        $tokenResult = json_decode($response, true);

        return "Authorization: Bearer " . $tokenResult["access_token"];
    }

    //curl 请求环信接口
    public function request($url, $body, $header = array(), $method)
    {
        $org_name = $this->org_name;

        $app_name = $this->app_name;

        array_push($header, 'Accept:application/json');
        array_push($header, 'Content-Type:application/json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, "https://a1.easemob.com/{$org_name}/{$app_name}/{$url}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, $method, 1);https://a1.easemob.com/easemob-demo/chatdemoui/chatgroups

        switch ($method) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

        if (isset($body{3}) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $ret = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return $err;
        }

        return $ret;
    }
}