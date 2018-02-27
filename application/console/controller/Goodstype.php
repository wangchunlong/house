<?php

namespace app\console\controller;

use app\common\model\Goodstype as GoodstypeModel;
use app\common\service\Tool;
use think\Request;
use think\Url;

/**
 * Class Goodstype  商品分类控制器
 * @package app\console\controller
 */
class Goodstype extends Console
{
    /**
     * [index    商品分类列表]
     * console/goodstype/index
     *
     * @param GoodstypeModel $goodstype
     * @return mixed
     */
    public function index(GoodstypeModel $goodstype)
    {
        $goodstype_list = $goodstype
            ->field('id,parentid,title,picurl')
            ->order('parentid', 'asc')
            ->order('id', 'asc')
            ->select();

        $goodstype_list = self::tree($goodstype_list);

        return $this->fetch('index', [
            'list' => $goodstype_list,
        ]);
    }

    /**
     * [create    添加商品分类]
     * console/goodstype/create
     *
     * @param Request $request
     * @param GoodstypeModel $goodstype
     * @return mixed
     */
    public function create(Request $request, GoodstypeModel $goodstype)
    {
        if ($request->isPost()) {
            $data = $request->post();

            $result = $goodstype->allowField(true)->isUpdate(false)->save($data);


            if ($result) {
                $this->success('添加成功！', Url::build('index'));
            } else {
                $this->error('添加失败！');
            }
        }

        $goodstype_list = $goodstype
            ->field('id,parentid,title')
            ->order('parentid', 'asc')
            ->order('id', 'asc')
            ->select();

        $goodstype_list = self::tree($goodstype_list);

        return $this->fetch('create', [
            'goodstype_list' => $goodstype_list,
        ]);
    }


    /**
     * [update    修改商品分类]
     * console/goodstype/update
     *
     * @param Request $request
     * @param GoodstypeModel $goodstype
     * @return mixed
     */
    public function update(Request $request, GoodstypeModel $goodstype)
    {
        if ($request->post()) {
            $data = $request->post();
            $result = $goodstype->allowField(true)->isUpdate(true)->save($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }
        }

        $goodstype_list = $goodstype
            ->field('id,parentid,title')
            ->order('parentid', 'asc')
            ->order('id', 'asc')
            ->select();

        $goodstype_list = self::tree($goodstype_list);

        return $this->fetch('create', [
            'goodstype_list' => $goodstype_list,
            'vo' => $goodstype::get($request->param('id')),
        ]);

    }

    /**
     * 无限级分类
     * @param Array $data //数据库里获取的结果集
     * @param string $pid
     * @param int $count //第几级分类
     * @return array
     */
    static protected function tree(&$data, $parentid = '0', $count = 1)
    {
        $tree = array();
        foreach ($data as $key => $value) {
            //echo $value['parentid'];
            if ($value['parentid'] == $parentid) {
                $tree[] = [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'parentid' => $value['parentid'],
                    'count' => $count,
                    'data' => self::tree($data, $value['id'], $count + 1),
                ];
            }
        }
        return $tree;
    }


    /**
     * [delete    删除商品分类]
     * console/goodstype/delete
     *
     * @param Request $request
     * @param GoodstypeModel $goodstype
     */
    public function delete(Request $request, GoodstypeModel $goodstype)
    {
        $id = $request->param('id/a');
        $result = $goodstype::destroy($id);

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}