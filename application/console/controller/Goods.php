<?php

namespace app\console\controller;

use app\common\model\Goods as GoodsModel;
use think\Db;
use think\Request;
use think\Url;

/**
 * Class Goods 商品控制器
 * @package app\console\controller
 */
class Goods extends Console
{
    /**
     * [index    商品列表]
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $where = [];
        if ($request->has('search')) {
            $search = $request->param('search');
            $where['title'] = ['like', '%' . $search . '%'];
            $this->assign('search', $search);
        }

        $goods = new GoodsModel();

        $goods_list = $goods
            ->relation(['type'])
            ->where($where)
            ->order('id', 'DESC')
            ->paginate(10, false, ['query' => $request->get()]);

        return $this->fetch('index', [
            'list' => $goods_list,
        ]);
    }

    /**
     * [create    添加商品]
     * @return mixed
     */
    public function create(Request $request)
    {
        if ($request->isPost()) {
            $goods = new GoodsModel();

            $data = $request->param();
            $result = $goods->createdata($data);

            if ($result) {
                $this->success('添加成功！', Url::build('index'));
            } else {
                $this->error('添加失败！');
            }
        }

        /*商品一级分类*/
        $one_type_list = Db::name("GoodsType")
            ->field("id,title")
            ->where('parentid', 0)
            ->order('id', 'ASC')
            ->select();

        /*商品二级分类*/
        $two_type_list = Db::name("GoodsType")
            ->field("id,title")
            ->where('parentid', $one_type_list[0]['id'])
            ->order('id', 'ASC')
            ->select();
        /*商品属性*/
        $poolflag_list = Db::name('GoodsPoolFlag')
            ->field('id as flag_id,title as flag_title')
            ->order('flag_id ASC')
            ->select();

        return $this->fetch('create', [
            "one_type_list" => $one_type_list,
            "two_type_list" => $two_type_list,
            'poolflag_list' => $poolflag_list,
        ]);
    }

    /**
     * [update    修改商品]
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {

        if ($request->isPost()) {
            $goods = new GoodsModel();

            $data = $request->param();

            $result = $goods->updatedata($data);

            if ($result || $result == 0) {
                $this->success('修改成功！', Url::build('index'));
            } else {
                $this->error('修改失败！');
            }
        }

        $goods_id = $request->param("id");

        //商品信息
        $goods = new GoodsModel();

        $goods_data = $goods
            ->relation('stock')
            ->where('id', $goods_id)
            ->find();

        //商品一级分类
        $one_type_list = Db::name("goods_type")
            ->field("id,title")
            ->where('parentid', 0)
            ->order('id', 'ASC')
            ->select();

        //商品二级分类
        $two_type_list = Db::name("goods_type")
            ->field("id,title")
            ->where('parentid', $goods_data['type_str'][0])
            ->order('id', 'ASC')
            ->select();

        $poolflag_list = Db::name('goods_pool_flag')
            ->field('id as flag_id,title as flag_title')
            ->order('flag_id ASC')
            ->select();

        $flag_list = Db::name('goods_flag')
            ->alias('f')
            ->join('goods_pool_flag gf', 'f.flag_id = gf.id', 'LEFT')
            ->join('goods_pool_attr ga', 'f.flag_id = ga.flag_id', 'LEFT')
            ->field('gf.id as flag_id,gf.title as flag_title,ga.id as attr_id,ga.title as attr_title')
            ->where('goods_id', $goods_id)
            ->order('flag_id ASC,attr_id ASC')
            ->select();

        $attr_data = Db::name('goods_attr')
            ->where('goods_id', $goods_id)
            ->column('attr_id');

        $flag_data = array();
        foreach ($flag_list as $value) {
            $flag_data[$value['flag_id']]['flag_id'] = $value['flag_id'];
            $flag_data[$value['flag_id']]['flag_title'] = $value['flag_title'];

            $flag_data[$value['flag_id']]['data'][] = [
                'attr_id' => $value['attr_id'],
                'attr_title' => $value['attr_title'],
            ];
        }

        return $this->fetch('create', [
            "one_type_list" => $one_type_list,
            "two_type_list" => $two_type_list,
            'poolflag_list' => $poolflag_list,
            'flag_data' => $flag_data,
            'attr_data' => $attr_data,
            "vo" => $goods_data,
        ]);

    }

    /**
     * [upfield    更新商品]
     * @param Request $request
     * @param GoodsModel $goods
     */
    public function upfield(Request $request, GoodsModel $goods)
    {
        $data = $request->param();
        $result = $goods->where('id', $data['id'])->update($data);

        if ($result) {
            $this->success('修改成功！', Url::build('index'));
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * [delete    删除商品]
     * @param Request $request
     * @param GoodsModel $goods
     */
    public function delete(Request $request, GoodsModel $goods)
    {
        $result = $goods->deletedata($request->param('id/a'));

        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

}