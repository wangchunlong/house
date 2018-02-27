<?php

namespace app\interfaces\Controller;

use think\Db;
use think\Loader;
use think\Request;


class Alipay
{

    public function notifyurl()
    {

        Loader::import('Alipay.wappay.service.AlipayTradeService');
        Loader::import('Alipay.config');
        /* *
        * 功能：支付宝服务器异步通知页面
        * 版本：2.0
        * 修改日期：2016-11-01
        * 说明：
        * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

        *************************页面功能说明*************************
        * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
        * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
        * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
        */
        // 计算得出通知验证结果
        global $config;
        $data = Request::instance()->param();
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($data);
        $alipaySevice->writeLog("支付宝回调开始：======================");
        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if ($result) {//验证成功
            $alipaySevice->writeLog("验证成功：" . $result);
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //自定义参数
            //商户订单号
            $out_trade_no = $data['out_trade_no'];
            //支付宝交易号
            $trade_no = $data['trade_no'];
            //交易状态
            $trade_status = $data['trade_status'];
            //交易金额
            $total_amount = $data['total_amount'];
            $alipaySevice->writeLog('订单号：' . $out_trade_no);
            $alipaySevice->writeLog('交易金额：' . $total_amount);
            $alipaySevice->writeLog('交易状态：' . $trade_status);
            /* $datas = json_encode($data);
             // 操作
             $alipaySevice->writeLog("body：{$datas}");*/
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            if ($trade_status == 'TRADE_FINISHED') {
                // todo 交易成功，且可对该交易做操作
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } else if ($trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                /**
                 * 业务逻辑处理处！！！！！！
                 */
                /*$datas = json_encode($data);*/
                // 操作
                if ($data['body'] == '1') { //  1支付  总订单


                    $alipaySevice->writeLog('支付-类型：1支付  总订单');

                    // 查询订单信息
                    $orders_data = Db::name('orders')
                        ->field('id,user_id,shop_id,total')
                        ->where([
                            'ordernumber' => $out_trade_no,
                            'paystate' => '1'
                        ])
                        ->select();


                    if (!empty($orders_data)) {
                        // 启动事务
                        // 修改订单支付状态
                        Db::startTrans();
                        try {
                            //修改订单状态
                            Db::name('orders')
                                ->where('ordernumber', $out_trade_no)
                                ->update([
                                    'paystate' => '2'
                                ]);

                            $ordergoods_data = Db::name('ordersgoods')
                                ->where('ordernumber', $out_trade_no)
                                ->field('id,order_id,shop_id,goods_id,number,price,acount')
                                ->select();

                            foreach ($ordergoods_data as $v) {
                                //修改销量
                                Db::name('goods')
                                    ->where('id', $v['goods_id'])
                                    ->setInc("sales", $v['number']);
                            }

                            // 提交事务
                            Db::commit();

                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();
                            exit("fail");
                        }
                    }
                } else if ($data['body'] == '2') {  //1充值

                    $alipaySevice->writeLog('支付-类型：2支付  店铺订单');

                    // 查询订单信息
                    $orders_data = Db::name('orders')
                        ->field('id,user_id,shop_id,total')
                        ->where([
                            'ordernum' => $out_trade_no,
                            'paystate' => '1'
                        ])
                        ->select();


                    if (!empty($orders_data)) {
                        // 启动事务
                        // 修改订单支付状态
                        Db::startTrans();
                        try {
                            //修改订单状态
                            Db::name('orders')
                                ->where('ordernum', $out_trade_no)
                                ->update([
                                    'paystate' => '2'
                                ]);

                            $ordergoods_data = Db::name('ordersgoods')
                                ->where('ordernum', $out_trade_no)
                                ->field('id,order_id,shop_id,goods_id,number,price,acount')
                                ->select();

                            foreach ($ordergoods_data as $v) {
                                //修改销量
                                Db::name('goods')
                                    ->where('id', $v['goods_id'])
                                    ->setInc("sales", $v['number']);
                            }

                            // 提交事务
                            Db::commit();

                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();
                            exit("fail");
                        }
                    }
                }
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";        //请不要修改或删除
        } else {
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }
}