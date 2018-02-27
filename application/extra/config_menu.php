<?php

// 配置文件
return [
    [
        ['控制台', 'console/dashboard/index', 'fa fa-home', ['dashboard']],
    ],
    [

        ['权限管理', 'fa fa-picture-o', ['auth_rule', 'auth_group', 'auth_manager', 'log']],
        [
            ['权限规则', 'console/auth_rule/index', ['auth_rule']],
            ['权限角色', 'console/auth_group/index', ['auth_group']],
            ['管理员列表', 'console/auth_manager/index', ['auth_manager']],
            ['管理员日志', 'console/log/index', ['log']],
        ]
    ],
    [
        ['商品管理', 'fa fa-users', ['goodstype', 'goodsflag', 'goodsattr', 'goods', 'goodsappraise']],
        [
            ['商品分类', 'console/goodstype/index', ['goodstype']],
            ['商品属性', 'console/goodsflag/index', ['goodsflag', 'goodsattr']],
            ['商品列表', 'console/goods/index', ['goods', 'goodsappraise']],
        ]
    ],
    [
        ['系统推广', 'fa fa-picture-o', ['banner', 'icon', 'advert', 'notice', 'active']],
        [
            ['首页轮播', 'console/banner/index', ['banner']],
            ['首页图标', 'console/icon/index', ['icon']],
            ['首页广告', 'console/advert/index', ['advert']],
            ['系统活动', 'console/active/index', ['active']],
            ['系统消息', 'console/notice/index', ['notice']],
        ]
    ],
    [
        ['系统设置', 'fa fa-cog', ['baksql', 'webinfo', 'system', 'region', 'upload']],
        [
            ['数据库管理', 'console/baksql/index', ['baksql']],
            ['web页管理', 'console/webinfo/index', ['webinfo']],
            ['地区管理', 'console/region/province', ['region']],
            ['配置管理', 'console/system/index', ['system']],
            ['文件管理', 'console/upload/index', ['upload']],
        ]
    ],
];