<?php

namespace app\interfaces\controller;

use app\common\model\RegionCity;
use app\common\model\RegionCounty;
use app\common\model\RegionProvince;
use mrmiao\encryption\RSACrypt;
use think\Request;

class Region extends InterfacesController
{

    /**
     * [province    省列表]
     * interfaces/region/province
     *
     * @param RSACrypt $crypt
     * @param RegionProvince $province
     * @return mixed
     */
    public function province(RSACrypt $crypt, RegionProvince $province)
    {
        try {
            $province_list = $province
                ->field('province_id,province_name as name')
                ->select();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $province_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [city    市列表]
     * interfaces/region/city
     *
     * @param RSACrypt $crypt
     * @param RegionCity $city
     * @return mixed
     */
    public function city(RSACrypt $crypt, RegionCity $city)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            if (empty($data['province_id'])) {
                return $crypt->response([
                    'code' => 400,
                    'message' => '省级 id 缺失'
                ], true);
            }

            $city_list = $city
                ->field('city_id,city_name as name')
                ->where('province_id', $data['province_id'])
                ->select();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $city_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [county    区列表]
     * interfaces/region/county
     *
     * @param RSACrypt $crypt
     * @param RegionCounty $county
     * @return mixed
     */
    public function county(RSACrypt $crypt, RegionCounty $county)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            if (empty($data['city_id'])) {
                return $crypt->response([
                    'code' => 400,
                    'message' => '市级 id 缺失'
                ], true);
            }

            $county_list = $county
                ->field('county_id,county_name as name')
                ->where('city_id', $data['city_id'])
                ->select();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $county_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }

    /**
     * [recommendProvince    推荐省]
     * interfaces/region/recommendProvince
     *
     * @param RSACrypt $crypt
     * @param RegionProvince $province
     * @return mixed
     */
    public function recommendProvince(RSACrypt $crypt, RegionProvince $province)
    {
        try {
            $province_list = $province
                ->field('province_id,province_name as name')
                ->where(['is_index' => 1])
                ->select();

            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $province_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }


    /**
     * [recommendCity   推荐市]
     * interfaces/region/recommendCity
     *
     * @param RSACrypt $crypt
     * @param RegionCity $city
     * @return array|mixed
     */
    public function recommendCity(RSACrypt $crypt, RegionCity $city)
    {
        try {

            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            if (empty($data['province_id'])) {
                return $crypt->response([
                    'code' => 400,
                    'message' => '省级 id 缺失'
                ], true);
            }

            $city_list = $city
                ->field('city_id,city_name as name')
                ->where('province_id', $data['province_id'])
                ->where('is_index', 1)
                ->select();


            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $city_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }


    /**
     * [recommendCounty   推荐区]
     * interfaces/region/recommendCounty
     *
     * @param RSACrypt $crypt
     * @param RegionCounty $county
     * @return mixed
     */
    public function recommendCounty(RSACrypt $crypt, RegionCounty $county)
    {
        try {
            //调用request()方法获取请求参数,request方法可选参数数组['参数名'=>'强制转换类型']
            $data = $crypt->request();

            if (empty($data['city_id'])) {
                return $crypt->response([
                    'code' => 400,
                    'message' => '市级 id 缺失'
                ], true);
            }

            $county_list = $county
                ->field('county_id,county_name as name')
                ->where('city_id', $data['city_id'])
                ->where('is_index', 1)
                ->select();


            return $crypt->response([
                'code' => 200,
                'message' => '成功',
                'data' => $county_list,
            ], true);

        } catch (\Exception $e) {
            return $crypt->response([
                'code' => 400,
                'message' => '当前数据异常,请等待处理'
            ], true);
        }
    }
}