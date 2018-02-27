<?php

namespace app\console\controller;

use app\common\service\Baksql as Bakmysql;
use think\Db;
use think\Request;

/**
 * Class Baksql 数据库备份
 * @package app\console\controller
 */
class Baksql extends Console
{
    /**
     * [index    数据库备份列表]
     * console/baksql/index
     * @return mixed
     */
    public function index()
    {
        $sql = new Bakmysql(\think\Config::get("database"));

        return $this->fetch("index", [
            "list" => $sql->get_filelist()
        ]);
    }

    /**
     * [mysql    查看数据库]
     * console/baksql/mysql
     * @return mixed
     */
    public function mysql(Request $request)
    {
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);

        if ($request->param('table')) {

            $table = Db::query("SHOW FULL COLUMNS FROM " . Request::instance()->param('table'));

            $this->assign('table', $table);

        } else {
            $data = Db::query("SHOW TABLE STATUS");
            $total = 0;
            foreach ($data as &$value) {
                $total += $value['Data_length'];
                $value['size'] = self::GetRealSize($value['Data_length']);
            }
            $this->assign('total', self::GetRealSize($total));
            $this->assign('tables', $data);
        }

        return $this->fetch('mysql');
    }

    private function GetRealSize($size)
    {
        $kb = 1024;          // Kilobyte
        $mb = 1024 * $kb;    // Megabyte
        $gb = 1024 * $mb;    // Gigabyte
        $tb = 1024 * $gb;    // Terabyte

        if ($size < $kb)
            return $size . 'B';

        else if ($size < $mb)
            return round($size / $kb, 2) . 'KB';

        else if ($size < $gb)
            return round($size / $mb, 2) . 'MB';

        else if ($size < $tb)
            return round($size / $gb, 2) . 'GB';

        else
            return round($size / $tb, 2) . 'TB';
    }

    /**
     * [backup    备份数据库]
     * console/baksql/backup
     *
     * @param Request $request
     * @return bool
     */
    public function backup(Request $request)
    {
        $sql = new Bakmysql(\think\Config::get("database"));
        $result = $sql->backup();

        if ($result) {
            $this->success($result);
        } else {
            $this->error($sql->getError());
        }

    }

    /**
     * [dowonload    下载备份]
     * console/baksql/dowonload
     *
     * @param Request $request
     * @return bool
     */
    public function dowonload(Request $request)
    {
        $sql = new Bakmysql(\think\Config::get("database"));

        return $sql->downloadFile($request->param('name'));
    }

    /**
     * [restore  还原]
     * console/baksql/restore
     *
     * @param Request $request
     * @return bool
     */
    public function restore(Request $request)
    {
        $sql = new Bakmysql(\think\Config::get("database"));

        $result = $sql->restore($request->param('name'));

        if ($result) {
            $this->success($result);
        } else {
            $this->error($sql->getError());
        }

    }

    /**
     * [delete  删除备份]
     * console/baksql/delete
     *
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request)
    {
        $sql = new Bakmysql(\think\Config::get("database"));

        $result = $sql->delfilename($request->param('name'));

        if ($result) {
            $this->success($result);
        } else {
            $this->error($sql->getError());
        }
    }
}