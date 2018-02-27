<?php

namespace app\common\service;


class Descartes
{
    /**
     * 如果传入的参数只有一个数组，求笛卡尔积结果
     * @param $arr1 一维数组
     * @returns {Array}
     */
    static function descartes1($arr1)
    {
        // 返回结果，是一个二维数组
        $result = [];
        foreach ($arr1 as $value) {
            $result[] = $value;
        }
        return $result;
    }

    /**
     * 如果传入的参数只有两个数组，求笛卡尔积结果
     * @param $arr1 一维数组
     * @param $arr2 一维数组
     * @returns {Array}
     */
    static function descartes2($arr1, $arr2)
    {
        // 返回结果，是一个二维数组
        $result = [];
        foreach ($arr1 as $value) {
            foreach ($arr2 as $val) {
                $result[] = [$value, $val];
            }
        }
        return $result;
    }

    /**
     *
     * @param $arr2D 二维数组
     * @param $arr1D 一维数组
     * @returns {Array}
     */
    static function descartes2DAnd1D($arr2D, $arr1D)
    {
        // 返回结果，是一个二维数组
        $result = [];
        foreach ($arr2D as $value) {
            foreach ($arr1D as $val) {
                $arr = $value;
                $arr[] = $val;
                $result[] = $arr;
            }
        }
        return $result;
    }


    static function descartes3($list)
    {
        // 返回结果，是一个二维数组
        // 为了便于观察，采用这种顺序
        $arr2D = self::descartes2($list[0], $list[1]);
        for ($i = 2; $i < count($list); $i++) {
            $arr2D = self::descartes2DAnd1D($arr2D, $list[$i]);
        }
        return $arr2D;
    }

    //笛卡儿积组合
    static function descartes($list)
    {
        if (!$list) {
            return [];
        }
        if (count($list) <= 0) {
            return [];
        }
        if (count($list) == 1) {
            return self::descartes1($list[0]);
        }

        if (count($list) == 2) {
            return self::descartes2($list[0], $list[1]);
        }

        if (count($list) >= 3) {
            return self::descartes3($list);
        }
    }

}