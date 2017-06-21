<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Tools\HomeController;

class GoodsController extends HomeController {
    //商品列表展示
    function showlist(){
        $cat_id = I('get.cat_id');

        $goods = new \Model\GoodsModel();
        $cdt['is_del'] = '不删除';
        $cdt['is_sale'] = '上架';

        /***********关联分类条件***********/
        //获取当前选取的分类 和 其内部全部子级分类，并合并为一个集合
        //商品的主分类、扩展分类必须在此集合中。
        //获得全部子级分类
        //全路径以当前选取分类的全路径为开始内容的分类信息
        $cat = D('Category');
        $now_cat = $cat -> find($cat_id); //当前选取的分类信息
        $now_path = $now_cat['cat_path'];
        $ext_cat = D('Category')->field('cat_id')->where("cat_path like '$now_path%'")->select();
        //SELECT * FROM `php41_category` WHERE ( cat_path like '1%' )
        //dump($ext_cat); //选取的、子级的都存在

        $s = "";
        foreach($ext_cat as $k => $v){
            $s .= $v['cat_id'].",";
        }
        $s = rtrim($s,',');
        //dump($s);//string(19) "1,7,8,9,12,13,14,15"

        //获得商品列表，条件是：“主分类”或“扩展分类”都在$s里边
        $sql = "select goods_id from __GOODS__ where cat_id in ($s) union select goods_id from __GOODS_CAT__ where cat_id in ($s)";
        $ids = D()->query($sql);
        //dump($ids);//二维数组信息，符合要求的商品id

        $w = "";
        foreach($ids as $kk => $vv){
            $w .= $vv['goods_id'].",";
        }
        $w = rtrim($w,',');

        //从$w的条件里边，获得需要的商品列表信息
        $cdt['goods_id'] = array('in',$w);
        /***********关联分类条件***********/


        $info = $goods->where($cdt) ->order('goods_id desc')-> select();
        //SELECT * FROM `php41_goods` WHERE `is_del` = '不删除' AND `goods_id` IN ('30','31','29','28') ORDER BY goods_id desc
        $this -> assign('info',$info);

        $this -> display();
    }
}
