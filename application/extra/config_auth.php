<?php

// 配置文件
return [
    'AUTH_ON'           => true,                    // 认证开关
    'AUTH_TYPE'         => 2,                       // 认证方式，1为时时认证；2为登录认证。
    'AUTH_GROUP'        => 'db_auth_group',         // 用户组数据表名
    'AUTH_GROUP_ACCESS' => 'db_auth_group_access',  // 用户组明细表
    'AUTH_GROUP_RULE'   => 'db_auth_group_rule',    // 用户组明细表
    'AUTH_RULE'         => 'db_auth_rule',          // 权限规则表
    'AUTH_USER'         => 'db_auth_manager',       // 用户信息表
];