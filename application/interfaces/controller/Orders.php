<?php

namespace app\interfaces\controller;

use app\common\model\Orders as OrdersModel;
use app\common\model\ShopEvaluate as ShopEvaluateModel;
use mrmiao\encryption\RSACrypt;
use think\Request;

class Orders extends InterfacesController
{
    /**
     * [ordersList    订单列表]
     * interfaces/Orders/ordersList
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function ordersList(RSACrypt $crypt, OrdersModel $orders)
    {
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


            $where['user_id'] = $data['user_id'];
            $where['state'] = 0;

            if (!empty($data['search'])) {
                $where['ordernum'] = ['LIKE', '%' . $data['search'] . '%'];
            }

            //订单状态
            switch ($data['status']) {
                case 0:
                    $where['paystate'] = ["in", "0,1,2,3,4,5,6"];
                    break;  //全部订单
                case 1:
                    $where['paystate'] = ['eq', "1"];
                    break;  //未支付
                case 2:
                    $where['paystate'] = ['in', "2,3"];
                    break;   //待收货
                case 3:
                    $where['paystate'] = ['in', "4"];
                    break;   //待评价
                case 4:
                    $where['paystate'] = ['eq', "0"];
                    break;   //已取消
            }

            $orders_list = $orders
                ->relation(['ordersgoods', 'shop'])
                ->field('id as order_id,shop_id,ordernum,paytype,paystate,money,account')
                ->where($where)
                ->order('posttime desc')
                ->paginate(10, false, [
                    'page' => $data['page'],
                ]);

            foreach ($orders_list as &$value) {
                $value['shop_name'] = $value['shop']['shop_name'];
                $value['data'] = $value['ordersgoods'];
                $value['goods_count'] = 0;

                foreach ($value['ordersgoods'] as $val) {
                    $value['goods_count'] += $val['number'];
                }

                $value->hidden(['shop', 'ordersgoods']);
            }

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $orders_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [ordersList    订单详情]
     * interfaces/Orders/ordersDetails
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function ordersDetails(RSACrypt $crypt, OrdersModel $orders)
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

            $orders_data = $orders
                ->relation(['ordersgoods', 'shop'])
                ->field('id as order_id,shop_id,name,mobile,address,ordernum,paytype,paystate,money,account,posttime')
                ->where('id', $data['order_id'])
                ->order('posttime desc')
                ->find();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $orders_data
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [companyDetails    查看物流]
     * interfaces/Orders/companyDetails
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function companyDetails(RSACrypt $crypt, OrdersModel $orders)
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

            $orders_data = $orders
                ->field('id as order_id,company_id,company_title,company_car,company_name,company_mobile,company_info')
                ->where('id', $data['order_id'])
                ->order('posttime desc')
                ->find();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $orders_data
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [ordersDel    删除订单]
     * interfaces/Orders/ordersDel
     *
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function ordersDel(RSACrypt $crypt, OrdersModel $orders)
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

            $result = $orders
                ->isUpdate(true)
                ->save([
                    'id' => $data['order_id'],
                    'state' => 1,
                ]);

            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '删除成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '删除失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [receiptGoods    确认收货]
     * interfaces/Orders/receiptGoods
     *
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function receiptGoods(RSACrypt $crypt, OrdersModel $orders)
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

            $result = $orders
                ->isUpdate(true)
                ->save([
                    'id' => $data['order_id'],
                    'paystate' => 4,
                ]);

            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }


    /**
     * [cancelOrders    取消订单]
     * interfaces/Orders/cancelOrders
     *
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function cancelOrders(RSACrypt $crypt, OrdersModel $orders)
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

            $result = $orders
                ->isUpdate(true)
                ->save([
                    'id' => $data['order_id'],
                    'paystate' => 0,
                ]);

            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [evaluate    评价订单]
     * interfaces/Orders/evaluate
     *
     * @param RSACrypt $crypt
     * @param OrdersModel $orders
     * @return mixed
     */
    public function evaluate(RSACrypt $crypt, OrdersModel $orders, ShopEvaluateModel $evaluate)
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


            $files = Request::instance()->file();
            $day = date('Ymd');
            $picarr = array();

            if (!empty($files)) {
                foreach ($files as $file) {
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'evaluate' . DS . $day);

                    $picarr[] = '/uploads/evaluate/' . $day . '/' . $info->getFilename();
                }
            }


            if (!empty($picarr)) {
                $data['picarr'] = implode(',', $picarr);
            }

            $result = $evaluate->allowField(true)->isUpdate(false)->save($data);

            if ($result) {
                $orders->isUpdate(true)->save([
                    'id' => $data['order_id'],
                    'paystate' => 5,
                ]);

                return $crypt->response([
                    'code' => 200,
                    'message' => '评价成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '评价失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

}