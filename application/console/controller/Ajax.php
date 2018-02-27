<?php

namespace app\console\controller;

use app\common\controller\BaseController;
use app\common\model\RegionCity as RegionCityModel;
use app\common\model\RegionCounty as RegionCountyModel;
use think\Db;
use think\Request;
use think\response\Json;

/**
 * Class Ajax       AJAX  异步请求控制器
 * @package app\console\controller
 */
class Ajax extends BaseController
{
    public function getTwoLevelGoodsType(Request $request)
    {
        $type_id = $request->param("type_id");

        $data = Db::name("goods_type")
            ->where("parentid", $type_id)
            ->field("id,title")
            ->select();

        return Json::create($data);
    }

    public function getFlag()
    {
        $flag_id = Request::instance()->param('flag_id');

        $flag_list = Db::name('goods_pool_flag')
            ->alias('f')
            ->join('goods_pool_attr a', 'f.id = a.flag_id', 'LEFT')
            ->field('f.id as flag_id,f.title as flag_title,a.id as attr_id,a.title as attr_title')
            ->where('f.id', $flag_id)
            ->order('flag_id ASC')
            ->select();

        $flag_data = array();
        foreach ($flag_list as $value) {
            $flag_data['flag_id'] = $value['flag_id'];
            $flag_data['flag_title'] = $value['flag_title'];
            $flag_data['data'][] = [
                'attr_id' => $value['attr_id'],
                'attr_title' => $value['attr_title'],
            ];
        }

        return Json::create($flag_data);
    }


    public function getCityList(Request $request, RegionCityModel $city)
    {
        $province_id = $request->param('province_id');

        if (empty($province_id) || $province_id == 0) {
            return Json::create([
                ['city_id' => 0, 'city_name' => '请选择市级地区']
            ]);
        }

        $city_list = $city
            ->field('city_id,city_name')
            ->where([
                'province_id' => $province_id
            ])
            ->select();

        if (empty($city_list)) {
            return Json::create([
                ['city_id' => 0, 'city_name' => '请选择市级地区']
            ]);
        }

        return Json::create($city_list);

    }

    public function getCountyList(Request $request, RegionCountyModel $county)
    {
        $city_id = $request->param('city_id');

        if (empty($city_id) || $city_id == 0) {
            return Json::create([
                ['county_id' => 0, 'county_name' => '请选择区县地区']
            ]);
        }

        $county_list = $county
            ->field('county_id,county_name')
            ->where([
                'city_id' => $city_id
            ])
            ->select();

        if (empty($county_list)) {
            return Json::create([
                ['county_id' => 0, 'county_name' => '请选择区县地区']
            ]);
        }

        return Json::create($county_list);
    }
}