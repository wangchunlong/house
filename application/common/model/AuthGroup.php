<?php

namespace app\common\model;


use think\Db;
use think\Exception;
use think\Model;

class AuthGroup extends Model
{
    protected static function init()
    {
        // 写入事件
        self::event('before_write', function ($query) {
            Db::startTrans();
            try {
                Db::name('auth_group_rule')
                    ->where('group_id', 'eq', $query->id)
                    ->delete();

                foreach ($query->rule_id as $rule_id) {
                    Db::name('auth_group_rule')->insert([
                        'group_id' => $query->id,
                        'rule_id' => $rule_id,
                    ]);
                }
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
            }
        });
    }
}