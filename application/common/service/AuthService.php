<?php

namespace app\common\service;

use think\Config;
use think\Db;

class AuthService
{
    //默认配置
    protected $_config = [
        'AUTH_ON'           => true,          // 认证开关
        'AUTH_TYPE'         => 2,           // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP'        => '',         // 用户组数据表名
        'AUTH_GROUP_ACCESS' => '',  // 用户组明细表
        'AUTH_GROUP_RULE'   => '',    // 用户组明细表
        'AUTH_RULE'         => '',          // 权限规则表
        'AUTH_USER'         => '',          // 用户信息表
    ];


    public function __construct()
    {
        $this->_config = Config::get('config_auth');
    }

    /**
     * 检查权限
     * @param $name              需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param $uid               认证用户的id
     * @param int $type 认证方式，1为时时认证；2为登录认证。
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return bool              通过验证返回true;失败返回false
     */
    public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {

        if (!$this->_config['AUTH_ON']) return true;  //认证开关关闭则不认证

        $authList = $this->getAuthList($uid, $type); //获取用户需要验证的所有有效规则列表


        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
        $list = array(); //保存验证通过的规则名
        $REQUEST = '';
        if ($mode == 'url') {
            $REQUEST = unserialize(strtolower(serialize($_REQUEST)));
        }
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);

            if ($mode == 'url' && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {  //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else if (in_array($auth, $name)) {
                $list[] = $auth;
            }
        }
        if ($relation == 'or' and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation == 'and' and empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * 保存用户所属用户组设置的所有权限规则id
     * @param int $uid 用户id
     * @return mixed
     */
    public function getGroups($uid)
    {
        static $groups = array();

        if (isset($groups[$uid]))
            return $groups[$uid];

        $user_groups = Db::table($this->_config['AUTH_GROUP_ACCESS'])
            ->alias('a')
            ->where(['a.manger_id' => $uid, 'g.status' => '1'])
            ->join($this->_config['AUTH_GROUP'] . " g", 'a.group_id=g.id')
            ->join($this->_config['AUTH_GROUP_RULE'] . " r", 'r.group_id=g.id')
            ->column('rule_id');

        $groups[$uid] = $user_groups ?: array();

        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param $uid   用户id
     * @param $type
     * @return array|mixed
     */
    public function getAuthList($uid, $type)
    {
        static $_authList = array(); //保存用户验证通过的权限列表

        $t = implode(',', (array)$type);

        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }

        //读取用户所属用户组
        $groups = $this->getGroups($uid);

        $ids = array_unique($groups);


        if (empty($ids)) {
            $_authList[$uid . $t] = array();
            return array();
        }

        //读取用户组所有权限规则
        $rules = Db::table($this->_config['AUTH_RULE'])
            ->where([
                'id' => ['in', $ids],
                'status' => 1
            ])
            ->select();

        //循环规则，判断结果。
        $authList = array();   //
        foreach ($rules as $key => $rule) {

            if (!empty($rule['condition'])) { //根据condition进行验证

                $user = $this->getUserInfo($uid);//获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command);//debug
                @(eval('$condition=(' . $command . ');'));

                if ($condition) {
                    $authList[$key] = strtolower($rule['name']);
                }

            } else {
                //只要存在就记录
                $authList[$key] = strtolower($rule['name']);
            }
        }

        $_authList[$uid . $t] = $authList;

        if ($this->_config['AUTH_TYPE'] == 2) {
            //规则列表结果保存到session
            $_SESSION['_AUTH_LIST_' . $uid . $t] = $authList;
        }

        return array_unique($authList);
    }


    /**
     * 获得用户资料,根据自己的情况读取数据库
     */
    protected function getUserInfo($uid)
    {
        static $userinfo = array();

        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = Db::table($this->_config['AUTH_USER'])->find();
        }

        return $userinfo[$uid];
    }
}