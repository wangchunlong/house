<?php

namespace app\interfaces\controller;

use mrmiao\encryption\RSACrypt;
use app\common\model\Address as AddressModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\Cart as CartModel;
use app\common\model\Orders as OrdersModel;
use app\common\model\Ordersgoods as OrdersgoodsModel;
use think\Cache;
use think\Db;
use think\Request;

class Buy extends InterfacesController
{

    /**
     * [direct    立即购买  确认订单]
     * interfaces/Buy/direct
     *
     * @param RSACrypt $crypt
     * @param GoodsModel $goods
     * @param AddressModel $address
     * @return array|mixed
     */
    public function direct(RSACrypt $crypt, GoodsModel $goods, AddressModel $address)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();
//            //验证参数
//            $result = $this->validate($data, 'Collect.goodsList');
//
//            if (true !== $result) {
//                return $crypt->response([
//                    'code' => 400,
//                    'message' => $result
//                ], true);
//            }

            $goods_data = $goods
                ->relation('shop')
                ->field('id as goods_id,shop_id,picurl,title,price')
                ->where('id', $data['goods_id'])
                ->find();

            $goods_data['number'] = $data['number'];

            $toprice = $goods_data['number'] * $goods_data['price'];
            $total = $toprice;

            $goods_data->hidden(['shop', 'shop_id']);

            $address_data = $address
                ->relation(['province', 'city', 'county'])
                ->field('id as address_id,user_id,name,mobile,province_id,city_id,county_id,address')
                ->where('user_id', $data['user_id'])
                ->order('state', "DESC")
                ->find();

            $goods_list[] = $goods_data;

            $shop_data = [
                'shop_id' => $goods_data['shop_id'],
                'shop_name' => $goods_data['shop']['shop_name'],
                'toprice' => number_format($toprice, 2, '.', ''),
                'data' => $goods_list,//商品属性  商品信息
            ];

            //店铺信息  商品信息
            $shop_list[] = $shop_data;

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'hasaddr' => empty($address_data) ? 0 : 1,    //是否存在默认地址
                'address' => empty($address_data) ? array() : $address_data,//默认收货地
                'total' => number_format($total, 2, '.', ''),
                'data' => $shop_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [cart    确认订单 购物车]
     * interfaces/Buy/cart
     *
     * @param RSACrypt $crypt
     * @param CartModel $cart
     * @param AddressModel $address
     * @return array|mixed
     */
    public function cart(RSACrypt $crypt, CartModel $cart, AddressModel $address)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

//            //验证参数
//            $result = $this->validate($data, 'Collect.goodsList');
//
//            if (true !== $result) {
//                return $crypt->response([
//                    'code' => 400,
//                    'message' => $result
//                ], true);
//            }


            $address_data = $address
                ->relation(['province', 'city', 'county'])
                ->field('id as address_id,user_id,name,mobile,province_id,city_id,county_id,address')
                ->where('user_id', $data['user_id'])/*用户id*/
                ->order('state', "DESC")
                ->find();

            //购物车列表
            $cart_list = $cart
                ->relation(['shop', 'goods'])
                ->field('id as cart_id,shop_id,goods_id,number')
                ->where('id', 'in', $data['cart_str'])/*购物车id字符串*/
                ->order('shop_id', 'ASC')
                ->select();


            //数组整合
            $shop_list = array();

            foreach ($cart_list as $key => &$value) {
                $value['goods']['number'] = $value['number'];
                $value['goods']['cart_id'] = $value['cart_id'];

                $shop_list[$value['shop_id']]['shop_id'] = $value['shop_id'];
                $shop_list[$value['shop_id']]['shop_name'] = $value['shop']['shop_name'];
                $shop_list[$value['shop_id']]['toprice'] = 0;
                $shop_list[$value['shop_id']]['money'] = 0;
                $shop_list[$value['shop_id']]['data'][] = $value['goods'];

            }

            $total = 0;

            foreach ($shop_list as &$value) {
                foreach ($value['data'] as &$val) {
                    $value['toprice'] += $val['number'] * $val['price'];
                }
                //计算总价
                $total += $value['toprice'];

                if ($value['toprice'] >= 100000) {
                    //10万元以上，订单金额为2万元
                    $value['money'] = 20000;
                } elseif ($value['toprice'] >= 10000 && $value['toprice'] < 100000) {
                    //10万元以下，订单金额为1万元
                    $value['money'] = 10000;
                } else {
                    //不足1万元的，按实际金额支付；
                    $value['money'] = $value['toprice'];
                }
            }


            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'hasaddr' => empty($address_data) ? 0 : 1,    //是否存在默认地址
                'address' => empty($address_data) ? array() : $address_data,//默认收货地
                'total' => number_format($total, 2, '.', ''),
                'data' => array_values($shop_list),
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [cart    确认订单 购物车]
     * interfaces/Buy/submitOrder
     *
     * @param RSACrypt $crypt
     * @param CartModel $cart
     * @param AddressModel $address
     * @return array|mixed
     */
    public function submitOrder(RSACrypt $crypt, CartModel $cart, AddressModel $address, OrdersModel $orders, OrdersgoodsModel $ordersgoods)
    {
        Db::startTrans();
        try {

            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

//            $data = Request::instance()->param();
//            //验证参数
//            $result = $this->validate($data, 'Collect.goodsList');
//
//            if (true !== $result) {
//                return $crypt->response([
//                    'code' => 400,
//                    'message' => $result
//                ], true);
//            }

            $time = time();
            //生成唯一订单
            $ordernumber = date('Ymd', $time) . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

            if (!empty($data['address_id'])) {
                $address_data = $address
                    ->relation(['province', 'city', 'county'])
                    ->field('id as address_id,user_id,name,mobile,province_id,city_id,county_id,address')
                    ->where('id', $data['address_id'])
                    ->find();
            } else {
                return $crypt->response([
                    'code' => 400,
                    'message' => '请选择地址'
                ], true);
            }

            $shop_list = json_decode($data['data'], true);

            foreach ($shop_list as $value) {
                $order_data = [
                    'ordernumber' => $ordernumber,
                    'posttime' => $time,

                    'ordernum' => $ordernumber . $value['shop_id'],
                    'user_id' => $address_data['user_id'],
                    'name' => $address_data['name'],
                    'mobile' => $address_data['mobile'],
                    'address' => $address_data['province']['province_name'] . $address_data['city']['city_name'] . $address_data['county']['county_name'] . $address_data['address'],

                    'paystate' => ($data['paytype'] == 4 || $data['paytype'] == 3) ? 2 : 1,
                    'paytype' => $data['paytype'],
                    'total' => ($data['paytype'] == 3) ? $data['total'] * 0.2 : $data['total'],

                    'shop_id' => $value['shop_id'],
                    'account' => $value['toprice'],
                    'money' => $value['money'],
                    'info' => $value['info'],
                ];

                $order_id = $orders->insertGetId($order_data);

                $ordersgoods_data = array();

                foreach ($value['goods'] as $val) {

                    $ordersgoods_data[] = [
                        'order_id' => $order_id,
                        'shop_id' => $value['shop_id'],
                        'goods_id' => $val['goods_id'],
                        'ordernumber' => $order_data['ordernumber'],
                        'ordernum' => $order_data['ordernum'],
                        'number' => $val['number'],
                    ];

                    if ($data['paytype'] == 4) {
                        Db::name('goods')
                            ->where('id', $val['goods_id'])
                            ->setInc('sales', $val['number']);
                    }

                }
                $ordersgoods->saveAll($ordersgoods_data);
            }
            $cart->where('id', 'in', $data['cart_id'])->delete();

            // 提交事务
            Db::commit();

            return $crypt->response([
                'code' => 200,
                'message' => '下单成功',
                'data' => $ordernumber,
            ], true);

        } catch (\Exception $e) {
            //回滚事务
            Db::rollback();
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }
}