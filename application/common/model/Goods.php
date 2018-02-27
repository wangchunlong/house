<?php

namespace app\common\model;

use think\Db;
use think\Request;


class Goods extends BaseModel
{
    //定义表明
    protected $table = 'db_goods';

    protected static function init()
    {
        // 写入事件
        self::event('before_write', function ($query) {
            $query->create_time = time();

            if (Request::instance()->has('picarr')) {
                $picarr = Request::instance()->param('picarr/a');
                $query->picarr = empty($picarr) ? '' : implode(',', $picarr);
            }
        });
    }

    public function getPicarrAttr($value)
    {
        return empty($value) ? array() : explode(',', $value);
    }

    public function type()
    {
        return $this->belongsTo('GoodsType', 'type_id')
            ->field('title');
    }

    public function stock()
    {
        return $this->hasMany('GoodsStock', 'goods_id')
            ->field('id,token,title,num,price');
    }


    public function createdata($data)
    {
        Db::startTrans();
        try {
            $this->allowField(true)->save($data);

            $data['goods_id'] = $this->id;

            foreach ($data['flag_id'] as $value) {
                Db::name('goods_flag')->insertGetId([
                    'flag_id' => $value,
                    'goods_id' => $data['goods_id'],
                ]);

                foreach ($data['attr'][$value] as $val) {
                    Db::name('goods_attr')->insert([
                        'attr_id' => $val,
                        'goods_id' => $data['goods_id'],
                    ]);
                }
            }

            foreach ($data['token'] as $key => $value) {
                Db::name('goods_stock')->insert([
                    'goods_id' => $data['goods_id'],
                    'token' => $data['token'][$key],
                    'price' => $data['price'][$key],
                    'title' => $data['stock_title'][$key],
                    'num' => $data['stock_num'][$key],
                ]);
            }

            //提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            //回滚事务
            Db::rollback();
            return false;
        }

    }

    public function updatedata($data)
    {
        Db::startTrans();
        try {
            $this->isUpdate(true)->allowField(true)->save($data);

            $data['goods_id'] = $this->id;

            Db::name('goodsflag')->where('goods_id', $data['goods_id'])->delete();
            Db::name('goodsattr')->where('goods_id', $data['goods_id'])->delete();

            foreach ($data['flag_id'] as $value) {
                Db::name('goods_flag')->insertGetId([
                    'flag_id' => $value,
                    'goods_id' => $data['goods_id'],
                ]);

                foreach ($data['attr'][$value] as $val) {
                    Db::name('goods_attr')->insert([
                        'attr_id' => $val,
                        'goods_id' => $data['goods_id'],
                    ]);
                }
            }

            foreach ($data['token'] as $key => $value) {
                $stock_data = Db::name('goods_stock')
                    ->where([
                        'goods_id' => $data['goods_id'],
                        'token' => $data['token'][$key],
                    ])->find();

                if (empty($stock_data)) {
                    $stock_data['id'] = Db::name('goodsstock')->insertGetId([
                        'goods_id' => $data['goods_id'],
                        'token' => $data['token'][$key],
                        'title' => $data['stock_title'][$key],
                        'num' => $data['stock_num'][$key],
                    ]);
                } else {
                    Db::name('goodsstock')
                        ->where([
                            'goods_id' => $data['goods_id'],
                            'token' => $data['token'][$key],
                        ])->update([
                            'title' => $data['stock_title'][$key],
                            'num' => $data['stock_num'][$key],
                        ]);
                }

                foreach ($data['category_id'] as $val) {
                    $token_data = Db::name('goodstoken')
                        ->where([
                            'category_id' => $val,
                            'goods_id' => $data['goods_id'],
                            'stock_id' => $stock_data['id'],
                        ])->find();

                    if (empty($token_data)) {
                        Db::name('goodstoken')->insert([
                            'category_id' => $val,
                            'goods_id' => $data['goods_id'],
                            'stock_id' => $stock_data['id'],
                            'price' => $data['stock_price'][$val][$value],
                        ]);
                    } else {

                        Db::name('goodstoken')
                            ->where([
                                'category_id' => $val,
                                'goods_id' => $data['goods_id'],
                                'stock_id' => $stock_data['id'],
                            ])->update([
                                'price' => $data['stock_price'][$val][$value],
                            ]);
                    }
                }
            }

            //提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            //回滚事务
            Db::rollback();
            return false;
        }
    }

    public function deletedata($data)
    {
        return self::destroy($data);
    }
}
