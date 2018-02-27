<?php

namespace app\interfaces\controller;

use app\common\service\Alidayu;
use app\common\service\Hx;
use mrmiao\encryption\RSACrypt;
use think\Db;

class UserAuth extends InterfacesController
{
    /**
     * [login    会员登录]
     * interfaces/user_auth/login
     *
     * @param RSACrypt $crypt
     * @param UserModel $user
     * @return mixed
     */
    public function login(RSACrypt $crypt, UserModel $user)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.login');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }

            $user_data = $user
                ->field('login,nickname,avatar,sex,id')
                ->where([
                    'login' => $data['login'],
                    'password' => md5(md5($data['password'])),
                ])
                ->find();

            if (empty($user_data)) {
                return $crypt->response([
                    'code' => 400,
                    'message' => '登录失败,密码错误'
                ], true);
            }

            $hx_data = Db::name('HxUser')->where([
                'user_id' => $user_data['id'],
                'type' => 1,
                'chat_state' => 1,
            ])->find();


            return $crypt->response([
                'code' => 200,
                'message' => '登录成功',
                'data' => [
                    'login' => $user_data['login'],
                    'avatar' => $user_data['avatar'],
                    'nickname' => $user_data['nickname'],
                    'sex' => $user_data['sex'],
                    'user_id' => $user_data['id'],
                    'hx_id' => $hx_data['id'],
                ]
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [sendRegisterSMS    注册用,发送短信验证码]
     * interfaces/user_auth/sendRegisterSMS
     *
     * @param RSACrypt $crypt
     * @return mixed
     */
    public function sendRegisterSMS(RSACrypt $crypt)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.sendRegisterSMS');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);

            }
            $templateCode = 123456;
            $phoneNumbers = 15114644710;

            $alidayu = new Alidayu();
            $result = $alidayu->sendSms($templateCode, $phoneNumbers, $templateParam = null, $outId = '123456');


            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '短信发送成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '短信发送失败'
            ], true);

        } catch (\Exception $e) {

            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [register    会员注册]
     * interfaces/user_auth/register
     *
     * @param RSACrypt $crypt
     * @param UserModel $user
     * @return mixed
     */
    public function register(RSACrypt $crypt, UserModel $user)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();
            //验证参数
            $result = $this->validate($data, 'UserAuth.register');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }


            $result = $user
                ->allowField(true)
                ->isUpdate(false)
                ->save($data);

            $hx_id = Db::name('HxUser')->insertGetId([
                'user_id' => $user->id,
                'type' => 1,
                'chat_state' => 1,
            ]);

            if ($hx_id) {
                $hx = new Hx();
                $hx->regUser($hx_id, md5(md5('123456')), '');
            }

            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '注册成功',
                    'data' => [
                        'login' => $user->login,
                        'nickname' => $user->nickname,
                        'user_id' => $user->id,
                        'hx_id' => $hx_id,
                    ]
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '注册失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }


    /**
     * [sendResetPasswordSMS    会员重置密码用,发送短信验证码]
     * interfaces/user_auth/sendResetPasswordSMS
     *
     * @param RSACrypt $crypt
     * @return mixed
     */
    public function sendResetPasswordSMS(RSACrypt $crypt)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.sendResetPasswordSMS');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }

            $result = SMS::sendRegisterUserSMS('user_reset', $data['login']);

            if ($result) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '短信发送成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '短信发送失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }

    }

    /**
     * [checkResetPassword    会员重置密码用,验证短信验证码]
     * interfaces/user_auth/checkResetPassword
     *
     * @param RSACrypt $crypt
     * @param UserModel $user
     * @return mixed
     */
    public function checkResetPassword(RSACrypt $crypt, UserModel $user)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.checkResetPassword');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }

            $user_data = $user
                ->where(['login' => $data['login']])
                ->find();

            return $crypt->response([
                'code' => 200,
                'message' => '验证成功',
                'data' => [
                    'user_id' => $user_data['id']
                ]
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }

    }

    /**
     * [resetPassword    会员重置密码]
     * interfaces/user_auth/resetPassword
     *
     * @param RSACrypt $crypt
     * @param UserModel $user
     * @return mixed
     */
    public function resetPassword(RSACrypt $crypt, UserModel $user)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.resetPassword');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }

            $result = $user
                ->allowField(true)
                ->isUpdate(true)
                ->save([
                    'id' => $data['user_id'],
                    'password' => md5(md5($data['password'])),
                ]);

            if ($result || $result == 0) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '密码重置成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '密码重置失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [modifyPassword    会员修改密码]
     * interfaces/user_auth/modifyPassword
     *
     * @param RSACrypt $crypt
     * @param UserModel $user
     * @return mixed
     */
    public function modifyPassword(RSACrypt $crypt, UserModel $user)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            //验证参数
            $result = $this->validate($data, 'UserAuth.modifyPassword');

            if (true !== $result) {
                return $crypt->response([
                    'code' => 400,
                    'message' => $result
                ], true);
            }

            $result = $user
                ->allowField(true)
                ->isUpdate(true)
                ->save([
                    'id' => $data['user_id'],
                    'password' => md5(md5($data['password'])),
                ]);

            if ($result || $result == 0) {
                return $crypt->response([
                    'code' => 200,
                    'message' => '密码修改成功'
                ], true);
            }

            return $crypt->response([
                'code' => 400,
                'message' => '密码修改失败'
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }
}