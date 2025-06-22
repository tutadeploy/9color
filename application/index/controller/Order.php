<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

/**
 * 订单列表
 */
class Order extends Base
{


    public function index()
    {
        $uid = session('user_id');
        
          if(!$uid && request()->isPost()){
                $this->error(lang('请先登录'));
            }
            if(!$uid) $this->redirect('User/login'); 
        
        $this->status = $status = input('get.status/d', null);
        $this->balance = Db::name('xy_users')->where('id',$uid)->value('balance');//获取用户今日已充值金额

        $query = $this->_query('xy_convey')
            ->where('xc.uid',session('user_id'))
            ->alias('xc')
            ->leftJoin('xy_goods_list xg','xc.goods_id=xg.id')
            ->field('xc.*,xg.goods_name,xg.shop_name,xg.goods_price,xg.goods_pic')
            ->order('xc.addtime desc');
            
        // 应用状态筛选逻辑
        if ($status !== null) {
            if ($status == -1 || $status == 0) { // 待处理订单 (status=-1 或 status=0)
                $query->where(function($q) {
                    $q->where(function($subq) {
                        // 手动派单：已派单等待结算的订单
                        $subq->where('xc.manual_dispatch', 1)
                            ->where('xc.dispatch_status', 2)
                            ->where('xc.status', 0);
                    })->whereOr(function($subq) {
                        // 传统模式：待付款订单
                        $subq->where('xc.auto_dispatch', 0)
                            ->where('xc.manual_dispatch', 0)
                            ->where('xc.status', 0);
                    });
                    // 注意：自动派单冷却期内的订单不显示给用户
                });
            } else {
                $query->where('xc.status', $status);
            }
        } else {
            // 全部订单：排除自动派单冷却期的订单
            $query->where(function($q) {
                $q->whereOr(function($subq) {
                    // 显示已完成的订单
                    $subq->where('xc.status', 1);
                })->whereOr(function($subq) {
                    // 显示手动派单等待结算的订单
                    $subq->where('xc.manual_dispatch', 1)
                        ->where('xc.dispatch_status', 2)
                        ->where('xc.status', 0);
                })->whereOr(function($subq) {
                    // 显示传统模式待付款的订单
                    $subq->where('xc.auto_dispatch', 0)
                        ->where('xc.manual_dispatch', 0)
                        ->where('xc.status', 0);
                });
                // 不显示：自动派单冷却期订单 (auto_dispatch=1 AND dispatch_status=0)
                // 不显示：手动派单等待匹配订单 (manual_dispatch=1 AND dispatch_status=0)
            });
        }
        
        $_query = $query->page(true,false);
        $this->list = $_query['list'];
        $color = sysconf('app_color');
        if($color){
            return $this->fetch('index-'.$color);
        }else{

            return $this->fetch('index-blue');
        }
    }
     public function get_order_info()
    {
        
        $id = input('post.id',0);
        
		$order = Db::name('xy_convey')->where('uid',session('user_id'))->field('id,addtime')->find($id);
		$start = strtotime(date("Y-m-d",$order['addtime']));
		$end = $start+24*60*60-1;
	/****/	$orderlist = Db::name('xy_convey')->where('uid',session('user_id'))->where('addtime','between',[$start,$end])->field('id,addtime')->select();
		for ($x=0; $x<=count($orderlist); $x++) {
		    if($id==$orderlist[$x]['id']){
		        $data['today_num']=$x+1;
		         break;
		    }
        }
        $data['code']="1";
        $data['date']=date('m-d', $order['addtime']);
        $data['parent_username']="41y.cn";
        $data['addtime']=$order['addtime'];
        $data['start']=$start;
        return json($data);
    }

    /**
     * 获取订单列表 - 优化派单模式显示逻辑
     */
    public function order_list()
    {
        $page = input('post.page/d',1);
        $num = input('post.num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $type = input('post.type/d',1);
        
        $query = db('xy_convey')
                ->where('xc.uid',session('user_id'))
                ->alias('xc')
                ->leftJoin('xy_goods_list xg','xc.goods_id=xg.id')
                ->field('xc.*,xg.goods_name,xg.shop_name,xg.goods_price,xg.goods_pic')
                ->order('xc.addtime desc')
                ->limit($limit);
        
        switch($type){
            case 1: //获取待处理订单
                $query->where(function($q) {
                    $q->where(function($subq) {
                        // 手动派单：已派单等待结算的订单
                        $subq->where('xc.manual_dispatch', 1)
                            ->where('xc.dispatch_status', 2)
                            ->where('xc.status', 0);
                    })->whereOr(function($subq) {
                        // 传统模式：待付款订单
                        $subq->where('xc.auto_dispatch', 0)
                            ->where('xc.manual_dispatch', 0)
                            ->where('xc.status', 0);
                    });
                    // 注意：自动派单冷却期内的订单不显示给用户
                });
                break;
            case 2: //获取冻结中订单
                $query->where('xc.status', 5);
                break;
            case 3: //获取已完成订单
                $query->where('xc.status', 1);
                break;
        }
        
        $data = $query->select();
        
        foreach ($data as &$datum) {
            $datum['endtime'] = date('Y/m/d H:i:s',$datum['endtime']);
            $datum['addtime'] = date('Y/m/d H:i:s',$datum['addtime']);
            
            // 添加订单状态描述，便于前端显示
            if ($datum['auto_dispatch'] == 1) {
                if ($datum['dispatch_status'] == 0) {
                    $timeLeft = $datum['cooling_end_time'] - time();
                    if ($timeLeft > 0) {
                        $datum['status_desc'] = '冷却中 (' . ceil($timeLeft/60) . '分钟)';
                    } else {
                        $datum['status_desc'] = '冷却结束，待自动结算';
                    }
                } elseif ($datum['dispatch_status'] == 1) {
                    $datum['status_desc'] = '已完成（自动）';
                }
            } elseif ($datum['manual_dispatch'] == 1) {
                if ($datum['dispatch_status'] == 0) {
                    $datum['status_desc'] = '等待匹配订单';
                } elseif ($datum['dispatch_status'] == 2) {
                    $datum['status_desc'] = $datum['status'] == 0 ? '等待手动结算' : '已完成（手动）';
                }
            } else {
                // 传统模式
                switch ($datum['status']) {
                    case 0: $datum['status_desc'] = '等待付款'; break;
                    case 1: $datum['status_desc'] = '完成付款'; break;
                    case 2: $datum['status_desc'] = '用户取消'; break;
                    case 3: $datum['status_desc'] = '强制付款'; break;
                    case 4: $datum['status_desc'] = '系统取消'; break;
                    case 5: $datum['status_desc'] = '订单冻结'; break;
                    default: $datum['status_desc'] = '未知状态'; break;
                }
            }
        }

        if(!$data) return json(['code'=>1,'info'=>lang('暂无数据')]);
        return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$data]);
    }

    /**
     * 获取单笔订单详情
     */
    public function order_info()
    {
        if(\request()->isPost()){
            $oid = input('post.id','');
            $oinfo = db('xy_convey')
                        ->alias('xc')
                        ->leftJoin('xy_member_address ar','ar.uid=xc.uid','ar.is_default=1')
                        ->leftJoin('xy_goods_list xg','xg.id=xc.goods_id')
                        ->leftJoin('xy_users u','u.id=xc.uid')
                        ->field('xc.id oid,xc.commission,xc.addtime,xc.endtime,xc.status,xc.num,xc.goods_count,xc.add_id,xg.goods_name,xg.goods_price,xg.shop_name,xg.goods_pic,ar.name,ar.tel,ar.address,u.balance')
                        ->where('xc.id',$oid)
                        ->where('xc.uid',session('user_id'))
                        ->find();
            if(!$oinfo) return json(['code'=>1,lang('暂无数据')]);
            $oinfo['endtime'] = date('Y/m/d H:i:s', $oinfo['endtime']  );
            $oinfo['addtime'] = date('Y/m/d H:i:s', $oinfo['addtime']  );

            return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$oinfo]);
        }
    }
    
    /**
     * 处理订单
     */
    public function do_order()
    {
        if(request()->isPost()){
            $oid = input('post.oid/s','');
            $status = input('post.status/d',1);
            $add_id = input('post.add_id/d',0);
            if(!\in_array($status,[1,2])) json(['code'=>1,'info'=>lang('参数错误')]);

            $res = model('admin/Convey')->do_order($oid,$status,session('user_id'),$add_id);
            return json($res);
        }
        return json(['code'=>1,'info'=>lang('错误请求')]);
    }

    /**
     * 获取充值订单
     */
    public function get_recharge_order()
    {
        $uid = session('user_id');
        $page = input('post.page/d',1);
        $num = input('post.num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $info = db('xy_recharge')->where('uid',$uid)->order('addtime desc')->limit($limit)->select();
        if(!$info) return json(['code'=>1,'info'=>lang('暂无数据')]);
        return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$info]);
    }

    /**
     * 验证提现密码
     */
    public function check_pwd2()
    {
        if(!request()->isPost()) return json(['code'=>1,'info'=>lang('错误请求')]);
        $pwd2 = input('post.pwd2/s','');
        $info = db('xy_users')->field('pwd2,salt2')->find(session('user_id'));
        if($info['pwd2']=='') return json(['code'=>1,'info'=>lang('未设置交易密码')]);
        if($info['pwd2']!=sha1($pwd2.$info['salt2'].config('pwd_str'))) return json(['code'=>1,'info'=>lang('密码错误')]);
        return json(['code'=>0,'info'=>lang('验证通过')]);
    }
}