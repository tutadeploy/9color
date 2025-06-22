<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class GoodsList extends Model
{

    protected $tabel = 'xy_goods_list';

    /**
     * 添加商品
     *
     * @param string $shop_name
     * @param string $goods_name
     * @param string $goods_price
     * @param string $goods_pic
     * @param string $goods_info
     * @param string $id 传参则更新数据,不传则写入数据
     * @return array
     */
    public function submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$cid,$id='')
    {
        if(!$goods_pic) return ['code'=>1,'info'=>('请上传商品图片')];
        if(!$goods_name) return ['code'=>1,'info'=>('请输入商品名称')];
        if(!$shop_name) return ['code'=>1,'info'=>('请输入店铺名称')];
        if(!$goods_price) return ['code'=>1,'info'=>('请填写正确的商品价格')];
        $data = [
            'shop_name'     => $shop_name,
            'goods_name'    => $goods_name,
            'goods_price'   => $goods_price,
            'goods_pic'     => $goods_pic,
            'goods_info'    => $goods_info,
            'cid'    => $cid,
            'addtime'       => time()
        ];
        if(!$id){
            $res = Db::table('xy_goods_list')->insert($data);
        }else{
            $res = Db::table('xy_goods_list')->where('id',$id)->update($data);
        }
        if($res)
            return ['code'=>0,'info'=>'操作成功!'];
        else 
            return ['code'=>1,'info'=>'操作失败!'];
    }

    /**
     * 搜索可派单商品
     * @param string $title 商品标题
     * @param float $minPrice 最低价格
     * @param float $maxPrice 最高价格
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public function searchForDispatch($title = '', $minPrice = 0, $maxPrice = 999999, $page = 1, $pageSize = 10)
    {
        $where = [];
        $where[] = ['status', '=', 1]; // 只显示上架商品
        
        if ($title) {
            $where[] = ['goods_name', 'like', "%{$title}%"];
        }
        
        if ($minPrice > 0) {
            $where[] = ['goods_price', '>=', $minPrice];
        }
        
        if ($maxPrice < 999999) {
            $where[] = ['goods_price', '<=', $maxPrice];
        }
        
        // 查询商品列表
        $goodsList = Db::name('xy_goods_list')
            ->where($where)
            ->field('id,goods_name,shop_name,goods_price,goods_pic,addtime')
            ->order('id desc')
            ->page($page, $pageSize)
            ->select();
        
        // 获取总数
        $total = Db::name('xy_goods_list')->where($where)->count();
        
        return [
            'list' => $goodsList,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => ceil($total / $pageSize)
        ];
    }

    /**
     * 获取商品详情（用于派单）
     * @param int $goodsId 商品ID
     * @return array|null
     */
    public function getGoodsForDispatch($goodsId)
    {
        return Db::name('xy_goods_list')
            ->where('id', $goodsId)
            ->where('status', 1)
            ->find();
    }
}