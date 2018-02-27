<?php

namespace app\console\controller;

use app\common\model\RegionCity as RegionCityModel;
use app\common\model\RegionCounty as RegionCountyModel;
use app\common\model\RegionProvince as RegionProvinceModel;
use think\Request;

/**
 * Class Region  地区管理
 * @package app\console\controller
 */
class Region extends Console
{
    /**
     * [province    省级列表]
     * console
     *
     * @param Request $request
     * @param RegionProvinceModel $province
     * @return mixed
     */
    public function province(Request $request, RegionProvinceModel $province)
    {

        $province_list = $province
            ->order('province_id', 'ASC')
            ->select();

        return $this->fetch('province', [
            'list' => $province_list,
        ]);
    }

    /**
     * [upProvinceField    省级状态]
     *
     * @param Request $request
     * @param RegionProvinceModel $province
     */
    public function upProvinceField(Request $request, RegionProvinceModel $province)
    {
        $data = $request->param();
        $result = $province->allowField(true)->isUpdate(true)->save($data);

        if ($result) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * [city    城市列表]
     *
     * @param Request $request
     * @param RegionCityModel $city
     * @return mixed
     */
    public function city(Request $request, RegionCityModel $city)
    {

        $city_list = $city
            ->where([
                'province_id' => $request->param('id'),
            ])
            ->select();

        return $this->fetch('city', [
            'list' => $city_list,
        ]);
    }


    /**
     * [upCityField    市级状态]
     *
     * @param Request $request
     * @param RegionCityModel $city
     */
    public function upCityField(Request $request, RegionCityModel $city)
    {
        $data = $request->param();
        $result = $city->allowField(true)->isUpdate(true)->save($data);

        if ($result) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * [area    获取区县列表]
     *
     * @param Request $request
     * @param RegionCountyModel $county
     * @return mixed
     */
    public function county(Request $request, RegionCountyModel $county)
    {
        $area_list = $county
            ->where([
                'city_id' => $request->param('id'),
            ])
            ->select();

        return $this->fetch('county', [
            'list' => $area_list,
        ]);
    }

    /**
     * [upCityField    区县状态]
     *
     * @param Request $request
     * @param RegionCountyModel $county
     */
    public function upCountyField(Request $request, RegionCountyModel $county)
    {
        $data = $request->param();
        $result = $county->allowField(true)->isUpdate(true)->save($data);

        if ($result) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        }
    }


}