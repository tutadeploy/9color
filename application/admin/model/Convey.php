<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Convey extends Model
{

    protected $table = 'xy_convey';

    /**
     * 创建订单
     *
     * @param int $uid
     * @return array
     */
    public function create_order($uid,$cid=1)
    {
        $add_id = Db::name('xy_member_address')->where('uid',$uid)->value('id');//获取收款地址信息s
        if(!$add_id) return ['code'=>1,'info'=>lang('还没有设置收货地址')];
        $uinfo = Db::name('xy_users')->field('deal_status,balance,level,deal_min_num,deal_max_num,pipei_type,pipei_grouping')->find($uid);
        if($uinfo['deal_status']!=2) return ['code'=>1,'info'=>lang('抢单已终止')];
        
        $cidinfo= Db::name('xy_goods_cate')->field('deal_min_num,deal_max_num')->find($cid);;//获取收款地址信息s
     
      // 简化佣金计算：只使用系统配置的订单区间
      $min = $uinfo['balance']*config('deal_min_num')/100;
      $max = $uinfo['balance']*config('deal_max_num')/100;

        
         
        $goods = $this->rand_order($min,$max,$cid);

//return ['code'=>8,'info'=>'第'.$di_num_dan.'单;区间min:'.$min.'max:'.$max.'总价'.$goods['num'].'数量'.$goods['count'].'单价'.$goods['price'].'利润:'.$commission];
        $level = $uinfo['level'];
        !$uinfo['level'] ? $level = 0 : '';
        $ulevel = Db::name('xy_level')->where('level',$level)->find();
        if ($uinfo['balance'] < $ulevel['num_min']) {
            return ['code'=>1,'info'=>lang('会员等级余额不足')];
        }

        $id = getSn('UB');
        Db::startTrans();
        
        // 不立即设置deal_status=3，保持为2等待派单处理
        // 原来：$res = Db::name('xy_users')->where('id',$uid)->update(['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')]);
        $res = Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')]);
        
        // 佣金计算：只基于用户等级的佣金比例
        $commission = $goods['num'] * $ulevel['bili'];
        //var_dump($cate,123,$goods);die;

        $res1 = Db::name($this->table)
                ->insert([
                    'id'            => $id,
                    'uid'           => $uid,
                    'ubalance'      => $uinfo['balance'],
                    'num'           => $goods['num'],
                    'addtime'       => time(),
                    'endtime'       => time()+config('deal_timeout'),
                    'add_id'        => $add_id,
                    'goods_id'      => $goods['id'],
                    'goods_count'   => $goods['count'],
                    'commission'    => $commission,  // 只基于用户等级佣金比例计算
                ]);
        if($res && $res1){
            Db::commit();
            return ['code'=>0,'info'=>lang('抢单成功'),'oid'=>$id];
        }else{
            Db::rollback();
            return ['code'=>1,'info'=>lang('抢单失败!请稍后再试')];
        }
    }

    /**
     * 随机生成订单
     */
    private function rand_order($min,$max,$cid=1)
    {
        // 直接在价格区间内查找商品，确保商品数量为1
        $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$min,$max])
                ->where('cid','=',$cid)
                ->find();

        // 如果没有找到完全匹配的商品，逐步放宽价格区间
        if (!$goods) {
            $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$min/2,$max])
                ->where('cid','=',$cid)
                ->find();
                
            if (!$goods) {
                $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$min/5,$max])
                ->where('cid','=',$cid)
                ->find();
                
                if (!$goods) {
                    $goods = Db::name('xy_goods_list')
                    ->orderRaw('rand()')
                    ->where('goods_price','between',[$min/10,$max])
                    ->where('cid','=',$cid)
                    ->find();
                    
                    if(!$goods){
                        $goods = Db::name('xy_goods_list')
                        ->orderRaw('rand()')
                        ->where('goods_price','between',[$min/20,$max])
                        ->where('cid','=',$cid)
                        ->find();
                        
                        if(!$goods){ 
                            $goods = Db::name('xy_goods_list')
                            ->orderRaw('rand()')
                            ->where('goods_price','between',[$min/50,$max])
                            ->where('cid','=',$cid)
                            ->find();
                            
                            if(!$goods){
                                 $goods = Db::name('xy_goods_list')
                                ->orderRaw('rand()')
                                ->where('goods_price','between',[$min/100,$max])
                                ->where('cid','=',$cid)
                                ->find();
                                
                                if(!$goods){
                                     $goods = Db::name('xy_goods_list')
                                    ->orderRaw('rand()')
                                    ->where('goods_price','between',[$min/200,$max])
                                    ->where('cid','=',$cid)
                                    ->find();
                                    
                                    if(!$goods){
                                         $goods = Db::name('xy_goods_list')
                                            ->orderRaw('rand()')
                                            ->where('goods_price','between',[$min/500,$max])
                                            ->where('cid','=',$cid)
                                            ->find();
                                            
                                            if(!$goods){
                                                 $goods = Db::name('xy_goods_list')
                                                    ->orderRaw('rand()')
                                                    ->where('goods_price','between',[$min/1000,$max])
                                                    ->where('cid','=',$cid)
                                                    ->find();
                                                    
                                                    if(!$goods){
                                                         $goods = Db::name('xy_goods_list')
                                                            ->orderRaw('rand()')
                                                            ->where('goods_price','between',[$min/2000,$max])
                                                            ->where('cid','=',$cid)
                                                            ->find();
                                                            
                                                            if(!$goods){ 
                                                                $goods = Db::name('xy_goods_list')
                                                                    ->orderRaw('rand()')
                                                                    ->where('goods_price','between',[$min/5000,$max])
                                                                    ->where('cid','=',$cid)
                                                                    ->find();
                                                                    
                                                                    if(!$goods){
                                                                        $goods = Db::name('xy_goods_list')
                                                                        ->orderRaw('rand()')
                                                                        ->where('goods_price','between',[$min/10000,$max])
                                                                        ->where('cid','=',$cid)
                                                                        ->find();
                                                                        
                                                                        if(!$goods){
                                                                            return ['code'=>1,'info'=>lang('抢单失败,该分类下价格区间库存不足')];
                                                                        }
                                                                    }
                                                            }
                                                    }
                                            }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // 固定商品数量为1，订单总金额就是商品价格
        $count = 1;
        $totalAmount = $goods['goods_price'];
        
        // 验证商品价格是否在允许的区间内
        if($totalAmount < $min || $totalAmount > $max){
            // 如果商品价格不在区间内，递归重新查找
            return self::rand_order($min,$max,$cid);
        }
        
        return [
            'code' => 0,
            'count' => $count,           // 固定为1
            'id' => $goods['id'],
            'num' => $totalAmount,       // 订单总金额 = 商品价格
            'cid' => $goods['cid'],
            'price' => $goods['goods_price']
        ];
    }

    /**
     * 处理订单
     *
     * @param string $oid      订单号
     * @param int    $status   操作      1会员确认付款 2会员取消订单 3后台强制付款 4后台强制取消
     * @param int    $uid      用户ID    传参则进行用户判断
     * @param int    $uid      收货地址
     * @return array
     */
    public function do_order($oid,$status,$uid='',$add_id='')
    {
        $info = Db::name('xy_convey')->find($oid);
        if(!$info) return ['code'=>1,'info'=>lang('订单号不存在')];
        if($uid && $info['uid']!=$uid) return ['code'=>1,'info'=>lang('参数错误,请确认订单号')];
        if(!in_array($info['status'],[0,5])) return ['code'=>1,'info'=>lang('该订单已处理,请刷新页面')];

        //TODO 判断余额是否足够
        $userPrice = Db::name('xy_users')->where('id',$info['uid'])->value('balance');
        if($status!=4){
            if ($userPrice < $info['num']) return ['code'=>1,'info'=>lang('账户可用余额不足'),'need'=>$info['num']-$userPrice];
        }

        //$tmp = ['endtime'=>time(),'status'=>$status];
        $tmp = ['endtime'=>time()+config('deal_feedze'),'status'=>$status];
        $add_id?$tmp['add_id']=$add_id:'';
        Db::startTrans();
        $res = Db::name('xy_convey')->where('id',$oid)->update($tmp);
        if(in_array($status,[1,3])){
            //确认付款
            try {$res1 = Db::name('xy_users')
                        ->where('id', $info['uid'])
                        ->dec('balance',$info['num'])
                        ->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金
                        ->update(['deal_status' => 1,'status'=>1]);
            } catch (\Throwable $th) {
                Db::rollback();
                return ['code'=>1,'info'=>lang('请检查账户余额')];
            }
            $res2 = Db::name('xy_balance_log')->insert([
                'uid'           => $info['uid'],
                'oid'           => $oid,
                'num'           => $info['num'],
                'type'          => 2,
                'status'        => 2,
                'addtime'       => time()
            ]);
            if($status==3) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制付款，如有疑问请联系客服'),'addtime'=>time(),'status'=>0]);
            //系统通知
            if($res && $res1 && $res2){
                Db::commit();
                $c_status = Db::name('xy_convey')->where('id',$oid)->value('c_status');
                //判断是否已返还佣金
                if($c_status===0) $this->deal_reward($info['uid'],$oid,$info['num'],$info['commission']);
                return ['code'=>0,'info'=>lang('操作成功')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>lang('操作失败')];
            }
        }elseif (in_array($status,[2,4])) {
            $res1 = Db::name('xy_users')->where('id',$info['uid'])->update(['deal_status'=>1]);
            if($status==4) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制取消，如有疑问请联系客服'),'addtime'=>time(),'status'=>0]);
            //系统通知
            if($res && $res1!==false){
                Db::commit();
                return ['code'=>0,'info'=>lang('操作成功')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>lang('操作失败'),'data'=>$res1];
            }
        }
    }

    /**
     * 交易返佣
     *
     * @return void
     */
    public function deal_reward($uid,$oid,$num,$cnum)
    {
        // 检查是否已经发放过佣金，避免重复发放
        $c_status = Db::name('xy_convey')->where('id',$oid)->value('c_status');
        if($c_status == 1) {
            // 佣金已发放，只发放上级奖励
            $this->deal_reward_to_parent_only($uid, $oid, $num, $cnum);
            return;
        }
        
        //$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res2 = Db::name('xy_users')->where('id',$uid)->where('status',1)->setDec('freeze_balance',$num+$cnum);

        if($res){
                $res1 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $oid,
                    'num'       => $num+$cnum,  // 记录商品价格+佣金
                    'type'      => 3,
                    'addtime'   => time()
                ]);
                //将订单状态改为已返回佣金
                Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>1,'status'=>1]);
                Db::name('xy_reward_log')->insert(['oid'=>$oid,'uid'=>$uid,'num'=>$num,'addtime'=>time(),'type'=>2]);//记录充值返佣订单
                 /************* 发放交易奖励 *********/
                    $userList = model('admin/Users')->parent_user($uid,5);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1){
                                Db::name('xy_reward_log')
                                ->insert([
                                    'uid'       => $v['id'],
                                    'sid'       => $uid,
                                    'oid'       => $oid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'lv'        => $v['lv'],
                                    'type'      => 2,
                                    'status'    => 1,
                                    'addtime'   => time(),
                                ]);
                                $res1 = Db::name('xy_balance_log')->insert([
                                    //记录返佣信息
                                    'uid'       => $v['id'],
                                    'oid'       => $oid,
                                    'sid'       => $uid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'type'      => 6,
                                    'status'    => 1,
                                    'f_lv'        => $v['lv'],
                                    'addtime'   => time()
                                ]);

                                $num3 = $cnum*config($v['lv'].'_d_reward'); //佣金
                                $res = Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                            }
                        }
                    }
                 /************* 发放交易奖励 *********/
        }else{
            $res1 = Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>2]);//记录账号异常
        }
        
    }

    /**
     * 只发放上级奖励，不重复发放当前用户佣金
     * 用于手动结算等场景，避免重复发放佣金
     */
    public function deal_reward_to_parent_only($uid,$oid,$num,$cnum)
    {
        /************* 发放交易奖励 *********/
        $userList = model('admin/Users')->parent_user($uid,5);
        if($userList){
            foreach($userList as $v){
                if($v['status']===1){
                    // 检查是否已经发放过此上级用户的奖励
                    $existingReward = Db::name('xy_reward_log')
                        ->where('uid', $v['id'])
                        ->where('sid', $uid)
                        ->where('oid', $oid)
                        ->where('type', 2)
                        ->find();
                    
                    if (!$existingReward) {
                        Db::name('xy_reward_log')
                        ->insert([
                            'uid'       => $v['id'],
                            'sid'       => $uid,
                            'oid'       => $oid,
                            'num'       => $cnum*config($v['lv'].'_d_reward'),
                            'lv'        => $v['lv'],
                            'type'      => 2,
                            'status'    => 1,
                            'addtime'   => time(),
                        ]);
                        $res1 = Db::name('xy_balance_log')->insert([
                            //记录返佣信息
                            'uid'       => $v['id'],
                            'oid'       => $oid,
                            'sid'       => $uid,
                            'num'       => $cnum*config($v['lv'].'_d_reward'),
                            'type'      => 6,
                            'status'    => 1,
                            'f_lv'        => $v['lv'],
                            'addtime'   => time()
                        ]);

                        $num3 = $cnum*config($v['lv'].'_d_reward'); //佣金
                        $res = Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                    }
                }
            }
        }
        /************* 发放交易奖励 *********/
    }
     public function getGroupingField($id,$field){
        $res=db('xy_grouping')->find($id);
        return $res[$field]; 
    }

        public function get_group_pipei_config($uid)
   {
        
        $uinfo = db('xy_users')->where('id',$uid)->field('balance,pipei_type,pipei_grouping')->find();
         $where['uid']=$uid;
         $where['status']=1;
         $where['grouping_id']=$uinfo['pipei_grouping'];
         
        $user_order = db('xy_convey')->where($where)->field('id')->Distinct(true)->select();
        $data['num'] = count($user_order)+1;
        $data['cancontinue']=1;
        $pipeiinfo=db('xy_grouping')->find($uinfo['pipei_grouping']);
        
        
        if(!$pipeiinfo['content']){
            $pipeiinfo['content']='[{"pipei_dan_num":1,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":2,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":3,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":4,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":5,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":6,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":7,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":8,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":9,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":10,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":11,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":12,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":13,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":14,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":15,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":16,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":17,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":18,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":19,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":20,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan_num":21,"pipei_dan_run":"0","pipei_min":"0","pipei_max":"0"}]';
        }
        
         $temp=json_decode($pipeiinfo['content'], true);
         
           $array=array();
            foreach($temp as $key => $value){
                if($value['pipei_dan_run']==0){
                    break;
                }else{
                    $array[$key]=$value;
                }
            }
        $grouping_size=count($array);
        if($data['num']>$grouping_size){
           $data['order_xu']=$data['num']%$grouping_size;
           if($data['order_xu']==0) $data['order_xu']=1;
        }else{
             $data['order_xu']=$data['num'];
        }
        
             $data['leftorder']=$grouping_size-$data['order_xu']+1;
        
            $pipei_run=array_column($array,'pipei_dan_run','pipei_dan_num');
            $pipei_min=array_column($array,'pipei_min','pipei_dan_num');
            $pipei_max=array_column($array,'pipei_max','pipei_dan_num');
        //type为1则为赚(匹配区间为金额),0为杀(匹配区间为百分比)
        
        if($pipeiinfo['type']==1){
            if($data['num']>$grouping_size){
            $data['cancontinue']=1;
            }
            $data['pipei_dan_run']=$pipei_run[$data['order_xu']];
            $data['pipei_max']=$pipei_max[$data['order_xu']];
            $data['pipei_min']=$pipei_min[$data['order_xu']]; 
            
        }else{
            
            $data['pipei_dan_run']=($pipei_run[$data['order_xu']]*$uinfo['balance'])/100;
            $data['pipei_max']=($pipei_max[$data['order_xu']]*$uinfo['balance'])/100;
            $data['pipei_min']=($pipei_min[$data['order_xu']]*$uinfo['balance'])/100;
            
        }
       
        return $data;
    }

    /**
     * 创建派单订单（新方法）
     * @param int $uid 用户ID
     * @param int $cid 商品分类ID
     * @return array
     */
    public function create_dispatch_order($uid, $cid = 1)
    {
        // 检查用户状态
        $userModel = new Users();
        $checkResult = $userModel->checkUserDispatchStatus($uid);
        if ($checkResult['code'] != 0) {
            return $checkResult;
        }

        // 获取用户派单模式
        $isAutoDispatch = $userModel->getUserDispatchMode($uid);
        
        if ($isAutoDispatch) {
            // 自动派单：创建完整订单
            $orderResult = $this->create_order($uid, $cid);
            
            if ($orderResult['code'] != 0) {
                return $orderResult;
            }

            $orderId = $orderResult['oid'];
            
            // 设置自动派单相关字段
            $coolingPeriod = get_dispatch_config('cooling_period_minutes', 1) * 60;
            $updateData = [
                'auto_dispatch' => 1,
                'dispatch_status' => 0, // 0=冷却中
                'manual_dispatch' => 0,
                'cooling_end_time' => time() + $coolingPeriod,
                'endtime' => time() + $coolingPeriod, // 自动派单订单的超时时间与冷却期匹配
            ];

            // 更新订单派单信息
            $updateResult = Db::name('xy_convey')->where('id', $orderId)->update($updateData);
            
            if ($updateResult === false) {
                return ['code' => 1, 'info' => '派单设置失败'];
            }

            return [
                'code' => 0,
                'info' => '自动派单成功，冷却期后自动结算',
                'oid' => $orderId,
                'dispatch_mode' => 'auto'
            ];
            
        } else {
            // 手动派单：创建不完整订单（等待管理员匹配商品）
            $orderResult = $this->create_manual_dispatch_order($uid, $cid);
            
            if ($orderResult['code'] != 0) {
                return $orderResult;
            }

            return [
                'code' => 0,
                'info' => '手动派单成功，等待管理员匹配',
                'oid' => $orderResult['oid'],
                'dispatch_mode' => 'manual'
            ];
        }
    }

    /**
     * 创建手动派单订单（等待管理员匹配商品）
     * @param int $uid 用户ID
     * @param int $cid 商品分类ID
     * @return array
     */
    private function create_manual_dispatch_order($uid, $cid = 1)
    {
        $add_id = Db::name('xy_member_address')->where('uid',$uid)->value('id');
        if(!$add_id) return ['code'=>1,'info'=>lang('还没有设置收货地址')];
        
        $uinfo = Db::name('xy_users')->field('deal_status,balance,level')->find($uid);
        if($uinfo['deal_status']!=2) return ['code'=>1,'info'=>lang('抢单已终止')];

        $level = $uinfo['level'];
        !$uinfo['level'] ? $level = 0 : '';
        $ulevel = Db::name('xy_level')->where('level',$level)->find();
        if ($uinfo['balance'] < $ulevel['num_min']) {
            return ['code'=>1,'info'=>lang('会员等级余额不足')];
        }

        $id = getSn('UB');
        Db::startTrans();
        
        // 更新用户统计信息但不改变deal_status
        $res = Db::name('xy_users')->where('id',$uid)->update([
            'deal_time'=>strtotime(date('Y-m-d')),
            'deal_count'=>Db::raw('deal_count+1')
        ]);

        // 创建空订单记录（等待管理员匹配商品）
        $res1 = Db::name($this->table)->insert([
            'id'            => $id,
            'uid'           => $uid,
            'ubalance'      => $uinfo['balance'],
            'num'           => 0, // 等待管理员设置
            'addtime'       => time(),
            'endtime'       => time()+config('deal_timeout'),
            'add_id'        => $add_id,
            'goods_id'      => 0, // 等待管理员选择
            'goods_count'   => 0, // 等待管理员设置
            'commission'    => 0, // 等待计算
            'auto_dispatch' => 0,
            'dispatch_status' => 0, // 0=等待匹配商品
            'manual_dispatch' => 1,
            'cooling_end_time' => 0, // 手动派单无冷却期
            'order_num' => 0,
            'grouping_id' => 0,
        ]);
        
        if($res && $res1){
            Db::commit();
            return ['code'=>0,'info'=>lang('手动派单订单创建成功，等待管理员匹配商品'),'oid'=>$id];
        }else{
            Db::rollback();
            return ['code'=>1,'info'=>lang('订单创建失败!请稍后再试')];
        }
    }

    /**
     * 获取冷却期剩余时间
     * @param string $orderId 订单ID
     * @return int 剩余秒数，0表示已结束
     */
    public function getCoolingTimeLeft($orderId)
    {
        $order = Db::name('xy_convey')->where('id', $orderId)->find();
        
        if (!$order || !$order['cooling_end_time']) {
            return 0;
        }
        
        $timeLeft = $order['cooling_end_time'] - time();
        return max(0, $timeLeft);
    }

    /**
     * 检查并处理冷却期结束的订单
     * @return array 处理结果
     */
    public function processCoolingOrders()
    {
        // 查找冷却期结束的自动派单订单
        $orders = Db::name('xy_convey')
            ->where('auto_dispatch', 1)
            ->where('dispatch_status', 0) // 0=冷却中/等待状态
            ->where('cooling_end_time', '>', 0) // 有设置冷却时间
            ->where('cooling_end_time', '<=', time()) // 冷却期已结束
            ->where('status', 0) // 待付款状态
            ->select();

        $processedCount = 0;
        $errors = [];

        foreach ($orders as $order) {
            try {
                Db::startTrans();
                
                // 检查是否已经扣款（通过余额日志判断）
                $paymentLog = Db::name('xy_balance_log')
                    ->where('oid', $order['id'])
                    ->where('type', 2) // 支出类型
                    ->where('status', 2) // 支出状态
                    ->find();
                
                $alreadyPaid = !empty($paymentLog);
                
                if ($alreadyPaid) {
                    // 场景2：手动匹配后切回自动 - 已扣款，只需结算
                    
                    // 获取用户信息
                    $user = Db::name('xy_users')->where('id', $order['uid'])->find();
                    if (!$user) {
                        throw new \Exception('用户不存在');
                    }
                    
                    // 直接执行结算：返还本金+佣金
                    $orderAmount = $order['num'];
                    $commission = $order['commission'];
                    $settleAmount = $orderAmount + $commission;
                    
                    $updateBalanceResult = Db::name('xy_users')
                        ->where('id', $order['uid'])
                        ->update([
                            'balance' => Db::raw('balance + ' . $settleAmount),
                            'deal_status' => 1 // 恢复正常状态
                        ]);
                    
                    if (!$updateBalanceResult) {
                        throw new \Exception('结算余额失败');
                    }
                    
                    // 记录结算日志
                    $logData = [
                        'uid' => $order['uid'],
                        'oid' => $order['id'],
                        'num' => $settleAmount, // 记录商品价格+佣金
                        'type' => 3, // 结算
                        'status' => 1,
                        'addtime' => time()
                    ];
                    
                    $logResult = Db::name('xy_balance_log')->insert($logData);
                    if (!$logResult) {
                        throw new \Exception('记录结算日志失败');
                    }
                    
                    // 更新订单状态
                    $updateOrderResult = Db::name('xy_convey')
                        ->where('id', $order['id'])
                        ->update([
                            'status' => 1, // 交易完成
                            'dispatch_status' => 1, // 派单完成
                            'c_status' => 1, // 佣金已发放
                            'endtime' => time()
                        ]);
                    
                    if (!$updateOrderResult) {
                        throw new \Exception('更新订单状态失败');
                    }
                    
                    // 记录奖励日志
                    Db::name('xy_reward_log')->insert([
                        'oid' => $order['id'],
                        'uid' => $order['uid'],
                        'num' => $orderAmount,
                        'addtime' => time(),
                        'type' => 2
                    ]);
                    
                    Db::commit();
                    
                    // 异步发放上级奖励
                    try {
                        $this->deal_reward_to_parent_only($order['uid'], $order['id'], $orderAmount, $commission);
                    } catch (\Exception $e) {
                        error_log("发放上级奖励失败: " . $e->getMessage());
                    }
                    
                    $processedCount++;
                    error_log("自动结算成功（已扣款订单）: 订单ID {$order['id']}, 用户ID {$order['uid']}, 金额 {$orderAmount}");
                    
                } else {
                    // 场景1：纯自动派单 - 未扣款，需要先扣款再结算
                    
                    // 先设置用户状态为交易中
                    $updateUserResult = Db::name('xy_users')
                        ->where('id', $order['uid'])
                        ->update(['deal_status' => 3]); // 3=交易中
                    
                    if (!$updateUserResult) {
                        throw new \Exception('更新用户状态失败');
                    }
                    
                    // 调用原有的扣款+结算逻辑
                    $result = $this->do_order($order['id'], 1); // 1=确认付款
                    
                    if ($result['code'] == 0) {
                        // 确保派单状态和订单状态一致
                        Db::name('xy_convey')
                            ->where('id', $order['id'])
                            ->update([
                                'dispatch_status' => 1, // 1=已自动派单并结算
                                'status' => 1 // 确保订单状态也是已完成
                            ]);
                        
                        // 恢复用户状态为正常
                        Db::name('xy_users')
                            ->where('id', $order['uid'])
                            ->update(['deal_status' => 1]); // 1=停止交易（正常状态）
                        
                        Db::commit();
                        $processedCount++;
                        
                        error_log("自动派单成功（扣款+结算）: 订单ID {$order['id']}, 用户ID {$order['uid']}, 金额 {$order['num']}");
                        
                    } else {
                        Db::rollback();
                        $errors[] = "订单 {$order['id']} 处理失败: " . $result['info'];
                    }
                }
                
            } catch (\Exception $e) {
                Db::rollback();
                $errors[] = "订单 {$order['id']} 处理异常: " . $e->getMessage();
                error_log("自动派单异常: 订单ID {$order['id']}, 错误: " . $e->getMessage());
            }
        }

        return [
            'code' => 0,
            'processed_count' => $processedCount,
            'total_found' => count($orders),
            'errors' => $errors
        ];
    }

    /**
     * 手动提前结算（冷却期内）- 优化版本
     * @param string $orderId 订单ID
     * @param int $uid 用户ID（如果为0表示管理员操作）
     * @return array
     */
    public function manualSettleOrder($orderId, $uid)
    {
        // 构建查询条件
        $where = ['id' => $orderId];
        if ($uid > 0) {
            $where['uid'] = $uid;
        }
        
        $order = Db::name('xy_convey')->where($where)->find();
        
        if (!$order) {
            return ['code' => 1, 'info' => '订单不存在'];
        }

        // 检查订单状态：自动派单且在冷却期，或手动派单已付款等待结算
        $canSettle = false;
        $settleType = '';
        
        if ($order['auto_dispatch'] == 1 && $order['dispatch_status'] == 0) {
            // 自动派单冷却期内的订单：需要区分是否已扣款
            // 检查是否存在扣款记录
            $paymentLog = Db::name('xy_balance_log')
                ->where('oid', $orderId)
                ->where('type', 2) // 支出类型
                ->where('status', 2) // 支出状态
                ->find();
            
            if ($paymentLog) {
                // 已扣款：手动匹配后切到自动的情况，只需结算
                $canSettle = true;
                $settleType = 'already_paid_auto';
            } else {
                // 未扣款：纯自动派单冷却期，需要先扣款再结算
                $canSettle = true;
                $settleType = 'auto_manual';
            }
        } elseif ($order['manual_dispatch'] == 1 && $order['dispatch_status'] == 2 && $order['status'] == 0) {
            // 手动派单已付款等待结算
            $canSettle = true;
            $settleType = 'manual_settle';
        }
        
        if (!$canSettle) {
            return ['code' => 1, 'info' => '该订单当前状态不支持手动结算'];
        }

        try {
            Db::startTrans();
            
            if ($settleType == 'auto_manual') {
                // 自动派单冷却期内手动结算 - 直接实现，避免调用do_order
                
                // 获取用户信息
                $user = Db::name('xy_users')->where('id', $order['uid'])->find();
                if (!$user) {
                    throw new \Exception('用户不存在');
                }
                
                // 检查余额是否足够
                if ($user['balance'] < $order['num']) {
                    throw new \Exception('账户余额不足，需要: ' . $order['num'] . '，当前余额: ' . $user['balance']);
                }
                
                // 1. 执行付款：扣除余额，增加冻结余额
                $updateUserResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update([
                        'balance' => Db::raw('balance - ' . $order['num']),
                        'freeze_balance' => Db::raw('freeze_balance + ' . ($order['num'] + $order['commission'])),
                        'deal_status' => 3 // 交易中
                    ]);
                
                if (!$updateUserResult) {
                    throw new \Exception('扣除用户余额失败');
                }
                
                // 2. 记录付款日志
                $paymentLogResult = Db::name('xy_balance_log')->insert([
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => $order['num'],
                    'type' => 2, // 付款
                    'status' => 2,
                    'addtime' => time()
                ]);
                
                if (!$paymentLogResult) {
                    throw new \Exception('记录付款日志失败');
                }
                
                // 3. 立即执行结算：返还本金+佣金，减少冻结余额
                $settleAmount = $order['num'] + $order['commission'];
                $settleUserResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update([
                        'balance' => Db::raw('balance + ' . $settleAmount),
                        'freeze_balance' => Db::raw('freeze_balance - ' . $settleAmount),
                        'deal_status' => 1 // 恢复正常状态
                    ]);
                
                if (!$settleUserResult) {
                    throw new \Exception('结算用户余额失败');
                }
                
                // 4. 记录佣金日志
                $commissionLogResult = Db::name('xy_balance_log')->insert([
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => $order['num'] + $order['commission'],  // 记录商品价格+佣金
                    'type' => 3, // 佣金
                    'status' => 1,
                    'addtime' => time()
                ]);
                
                if (!$commissionLogResult) {
                    throw new \Exception('记录佣金日志失败');
                }
                
                // 5. 更新订单状态
                $updateOrderResult = Db::name('xy_convey')
                    ->where('id', $orderId)
                    ->update([
                        'status' => 1, // 交易完成
                        'dispatch_status' => 1, // 派单完成
                        'c_status' => 1, // 佣金已发放
                        'endtime' => time() + config('deal_feedze', 0)
                    ]);
                
                if (!$updateOrderResult) {
                    throw new \Exception('更新订单状态失败');
                }
                
                // 6. 记录奖励日志
                Db::name('xy_reward_log')->insert([
                    'oid' => $orderId,
                    'uid' => $order['uid'],
                    'num' => $order['num'],
                    'addtime' => time(),
                    'type' => 2
                ]);
                
                // 7. 发放上级奖励（在事务外执行，避免复杂度）
                Db::commit();
                
                // 异步发放上级奖励 - 但不重复发放佣金给当前用户
                try {
                    $this->deal_reward_to_parent_only($order['uid'], $orderId, $order['num'], $order['commission']);
                } catch (\Exception $e) {
                    // 上级奖励失败不影响主流程
                    error_log("发放上级奖励失败: " . $e->getMessage());
                }
                
                return [
                    'code' => 0,
                    'info' => '手动结算成功',
                    'settle_type' => $settleType
                ];
                
            } elseif ($settleType == 'manual_settle') {
                // 手动派单的手动结算
                
                // 执行结算逻辑
                $orderAmount = $order['num'];
                $commission = $order['commission'];
                
                // 返还本金和佣金
                $settleAmount = $orderAmount + $commission;
                $updateBalanceResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update([
                        'balance' => Db::raw('balance + ' . $settleAmount),
                        'deal_status' => 1 // 恢复正常状态
                    ]);
                
                if (!$updateBalanceResult) {
                    throw new \Exception('结算余额失败');
                }
                
                // 记录结算日志
                $logData = [
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => $orderAmount + $commission, // 记录商品价格+佣金
                    'type' => 3, // 结算
                    'status' => 1,
                    'addtime' => time()
                ];
                
                $logResult = Db::name('xy_balance_log')->insert($logData);
                if (!$logResult) {
                    throw new \Exception('记录结算日志失败');
                }
                
                // 更新订单状态
                $updateOrderResult = Db::name('xy_convey')
                    ->where('id', $orderId)
                    ->update([
                        'status' => 1, // 交易完成
                        'dispatch_status' => 1, // 派单也完成
                        'c_status' => 1 // 佣金已发放
                    ]);
                
                if (!$updateOrderResult) {
                    throw new \Exception('更新订单状态失败');
                }
                
                Db::commit();
                
                // 异步发放上级奖励 - 但不重复发放佣金给当前用户
                try {
                    $this->deal_reward_to_parent_only($order['uid'], $orderId, $orderAmount, $commission);
                } catch (\Exception $e) {
                    // 上级奖励失败不影响主流程
                    error_log("发放上级奖励失败: " . $e->getMessage());
                }
                
                return [
                    'code' => 0,
                    'info' => '手动结算成功',
                    'settle_type' => $settleType
                ];
                
            } elseif ($settleType == 'already_paid_auto') {
                // 已扣款的自动派单订单结算（从D状态切换过来的特殊情况）
                
                // 直接执行结算：返还本金+佣金
                $orderAmount = $order['num'];
                $commission = $order['commission'];
                $settleAmount = $orderAmount + $commission;
                
                $updateBalanceResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update([
                        'balance' => Db::raw('balance + ' . $settleAmount),
                        'deal_status' => 1 // 恢复正常状态
                    ]);
                
                if (!$updateBalanceResult) {
                    throw new \Exception('结算余额失败');
                }
                
                // 记录结算日志
                $logData = [
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => $orderAmount + $commission, // 记录商品价格+佣金
                    'type' => 3, // 结算
                    'status' => 1,
                    'addtime' => time()
                ];
                
                $logResult = Db::name('xy_balance_log')->insert($logData);
                if (!$logResult) {
                    throw new \Exception('记录结算日志失败');
                }
                
                // 更新订单状态
                $updateOrderResult = Db::name('xy_convey')
                    ->where('id', $orderId)
                    ->update([
                        'status' => 1, // 交易完成
                        'dispatch_status' => 1, // 派单也完成
                        'c_status' => 1 // 佣金已发放
                    ]);
                
                if (!$updateOrderResult) {
                    throw new \Exception('更新订单状态失败');
                }
                
                Db::commit();
                
                // 异步发放上级奖励
                try {
                    $this->deal_reward_to_parent_only($order['uid'], $orderId, $orderAmount, $commission);
                } catch (\Exception $e) {
                    error_log("发放上级奖励失败: " . $e->getMessage());
                }
                
                return [
                    'code' => 0,
                    'info' => '手动结算成功（已扣款订单）',
                    'settle_type' => $settleType
                ];
            }
            
        } catch (\Exception $e) {
            Db::rollback();
            return ['code' => 1, 'info' => $e->getMessage()];
        }
    }

    /**
     * 删除订单并退回余额（支持所有状态）
     * @param string $orderId 订单ID
     * @param int $adminId 管理员ID
     * @return array
     */
    public function deleteOrderWithRefund($orderId, $adminId = 0)
    {
        $order = Db::name('xy_convey')->where('id', $orderId)->find();
        
        if (!$order) {
            return ['code' => 1, 'info' => '订单不存在'];
        }

        Db::startTrans();
        
        try {
            $refundAmount = 0;
            $refundMessage = '';
            
            // 根据订单状态和派单状态进行不同的余额处理
            if ($order['status'] == 0) {
                // 检查是否是手动派单且已扣费的订单
                if ($order['manual_dispatch'] == 1 && $order['dispatch_status'] == 2 && $order['num'] > 0) {
                    // 手动派单已扣费：需要退回订单金额
                    $refundAmount = $order['num'];
                    $refundMessage = "订单删除成功，退回手动派单金额 ¥{$order['num']}";
                } else {
                    // 真正的待付款状态：无需退款，只需恢复用户状态
                    $refundMessage = '订单删除成功（待付款状态，无需退款）';
                }
            } elseif ($order['status'] == 1) {
                // 已完成状态：扣除佣金（如果有）
                if ($order['c_status'] == 1 && $order['commission'] > 0) {
                    // 已发放佣金，需要扣除
                    $refundAmount = -$order['commission']; // 负数表示扣除
                    $refundMessage = "订单删除成功，扣除佣金 ¥{$order['commission']}";
                } else {
                    // 未发放佣金，无需处理
                    $refundAmount = 0;
                    $refundMessage = "订单删除成功（已完成状态，无佣金扣除）";
                }
            } elseif (in_array($order['status'], [2, 4])) {
                // 用户取消或系统取消：通常已经处理过退款，但为安全起见检查一下
                $refundMessage = '订单删除成功（取消状态）';
            } elseif ($order['status'] == 3) {
                // 强制付款状态：退回本金
                $refundAmount = $order['num'];
                $refundMessage = "订单删除成功，退回强制付款金额 ¥{$order['num']}";
            } elseif ($order['status'] == 5) {
                // 订单冻结：退回本金
                $refundAmount = $order['num'];
                $refundMessage = "订单删除成功，退回冻结金额 ¥{$order['num']}";
            }
            
            // 获取用户当前信息
            $user = Db::name('xy_users')->where('id', $order['uid'])->find();
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            
            // 执行余额操作（退款或扣除）
            if ($refundAmount != 0) {
                // 计算操作后的余额（refundAmount为正数是退款，负数是扣除）
                $newBalance = $user['balance'] + $refundAmount;
                
                $updateBalanceResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update([
                        'balance' => $newBalance,
                        'deal_status' => 1 // 恢复为可交易状态
                    ]);
                
                if (!$updateBalanceResult) {
                    throw new \Exception('退款失败');
                }
                
                // 记录余额变动日志
                $logResult = Db::name('xy_balance_log')->insert([
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => abs($refundAmount), // 记录绝对值
                    'type' => $refundAmount > 0 ? 5 : 6, // 5=订单删除退款, 6=订单删除扣除佣金
                    'status' => $refundAmount > 0 ? 1 : 2, // 1=收入, 2=支出
                    'addtime' => time()
                ]);
                
                if (!$logResult) {
                    throw new \Exception('记录退款日志失败');
                }
                
                // 如果用户原本余额为负数或操作后余额为负数，在信息中说明
                if ($user['balance'] < 0 || $newBalance < 0) {
                    $operation = $refundAmount > 0 ? '操作后' : '扣除后';
                    $refundMessage .= "（用户原余额 ¥{$user['balance']}，{$operation}余额 ¥{$newBalance}）";
                }
            } else {
                // 无需退款，只恢复用户状态（如果需要的话）
                if ($user['deal_status'] != 1) {
                    $userUpdateResult = Db::name('xy_users')
                        ->where('id', $order['uid'])
                        ->update(['deal_status' => 1]); // 恢复为可交易状态
                    
                    if (!$userUpdateResult) {
                        throw new \Exception('恢复用户状态失败');
                    }
                }
                // 如果用户状态已经是1（可交易），则无需更新
            }
            
            // 删除相关的余额日志记录
            Db::name('xy_balance_log')->where('oid', $orderId)->delete();
            
            // 删除相关的奖励日志记录
            Db::name('xy_reward_log')->where('oid', $orderId)->delete();
            
            // 删除订单
            $deleteResult = Db::name('xy_convey')->where('id', $orderId)->delete();
            
            if ($deleteResult) {
                Db::commit();
                
                // 记录删除日志
                if ($adminId > 0) {
                    Db::name('xy_message')->insert([
                        'uid' => $order['uid'],
                        'type' => 2,
                        'title' => '系统通知',
                        'content' => "订单 {$orderId} 已被管理员删除" . ($refundAmount > 0 ? "，退款 ¥{$refundAmount}" : ''),
                        'addtime' => time(),
                        'status' => 0
                    ]);
                }
                
                return ['code' => 0, 'info' => $refundMessage];
            } else {
                Db::rollback();
                return ['code' => 1, 'info' => '订单删除失败'];
            }
        } catch (\Exception $e) {
            Db::rollback();
            return ['code' => 1, 'info' => '删除失败: ' . $e->getMessage()];
        }
    }

    /**
     * 切换到自动派单模式
     * @param string $orderId 订单ID
     * @return array
     */
    public function switchToAutoDispatch($orderId)
    {
        $order = Db::name('xy_convey')->where('id', $orderId)->find();
        
        if (!$order) {
            return ['code' => 1, 'info' => '订单不存在'];
        }
        
        if ($order['status'] != 0) {
            return ['code' => 1, 'info' => '只能切换待付款状态的订单'];
        }
        
        // 获取用户信息，检查余额
        $user = Db::name('xy_users')->where('id', $order['uid'])->find();
        if (!$user) {
            return ['code' => 1, 'info' => '用户不存在'];
        }
        
        // 检查用户余额是否为负数
        if ($user['balance'] < 0) {
            return ['code' => 1, 'info' => '用户余额为负数，无法切换到自动派单模式。请先处理用户余额问题。'];
        }
        
        Db::startTrans();
        try {
            $coolingPeriod = get_dispatch_config('cooling_period_minutes', 1) * 60;
            
            // 检查是否已经付款（通过余额日志判断）
            $paymentLog = Db::name('xy_balance_log')
                ->where('oid', $orderId)
                ->where('type', 2) // 支出类型
                ->where('status', 2) // 支出状态
                ->find();
            
            $alreadyPaid = !empty($paymentLog);
            $hasGoods = !empty($order['goods_id']) && $order['goods_id'] > 0;
            
            if ($alreadyPaid) {
                // 已付款的订单：设置正常的冷却时间，等待自动结算
                $updateData = [
                    'auto_dispatch' => 1,
                    'manual_dispatch' => 0,
                    'dispatch_status' => 0, // 冷却中
                    'cooling_end_time' => time() + $coolingPeriod // 正常冷却期
                ];
                $message = '已切换为自动派单模式，订单已付款，等待冷却期结束后自动结算';
            } elseif ($hasGoods) {
                // 有商品但未付款：开始正常的自动派单流程（付款+冷却）
                $updateData = [
                    'auto_dispatch' => 1,
                    'manual_dispatch' => 0,
                    'dispatch_status' => 0, // 冷却中
                    'cooling_end_time' => time() + $coolingPeriod
                ];
                $message = '已切换为自动派单模式，开始冷却计时，等待自动结算';
            } else {
                // 无商品的订单：自动分发商品，然后开始冷却
                $autoDispatchResult = $this->autoAssignGoods($orderId);
                if ($autoDispatchResult['code'] != 0) {
                    throw new \Exception($autoDispatchResult['info']);
                }
                
                $updateData = [
                    'auto_dispatch' => 1,
                    'manual_dispatch' => 0,
                    'dispatch_status' => 0, // 冷却中
                    'cooling_end_time' => time() + $coolingPeriod
                ];
                $message = '已切换为自动派单模式，自动分发商品完成，开始冷却计时';
            }
            
            $result = Db::name('xy_convey')->where('id', $orderId)->update($updateData);
            
            if ($result !== false) {
                Db::commit();
                return ['code' => 0, 'info' => $message];
            } else {
                throw new \Exception('更新订单状态失败');
            }
        } catch (\Exception $e) {
            Db::rollback();
            return ['code' => 1, 'info' => '切换失败: ' . $e->getMessage()];
        }
    }

    /**
     * 自动分发商品到订单
     * @param string $orderId 订单ID
     * @return array
     */
    public function autoAssignGoods($orderId)
    {
        $order = Db::name('xy_convey')->where('id', $orderId)->find();
        
        if (!$order) {
            return ['code' => 1, 'info' => '订单不存在'];
        }
        
        if ($order['status'] != 0) {
            return ['code' => 1, 'info' => '订单状态不正确'];
        }
        
        // 查找可用商品（价格 <= 订单金额）
        $availableGoods = Db::name('xy_goods_list')
            ->where('status', 1)
            ->where('goods_price', '<=', $order['num'])
            ->order('goods_price desc') // 优先选择价格高的商品
            ->find();
        
        if (!$availableGoods) {
            return ['code' => 1, 'info' => '没有找到合适的商品'];
        }
        
        // 更新订单商品信息
        $updateData = [
            'goods_id' => $availableGoods['id'],
            'goods_count' => 1, // 固定数量为1
            'num' => $availableGoods['goods_price'] // 订单金额=商品价格
        ];
        
        $result = Db::name('xy_convey')->where('id', $orderId)->update($updateData);
        
        if ($result !== false) {
            return ['code' => 0, 'info' => '自动分发商品成功', 'goods' => $availableGoods];
        } else {
            return ['code' => 1, 'info' => '分发商品失败'];
        }
    }

    /**
     * 切换到手动派单模式
     * @param string $orderId 订单ID
     * @return array
     */
    public function switchToManualDispatch($orderId)
    {
        $order = Db::name('xy_convey')->where('id', $orderId)->find();
        
        if (!$order) {
            return ['code' => 1, 'info' => '订单不存在'];
        }
        
        if ($order['status'] != 0) {
            return ['code' => 1, 'info' => '只能切换待付款状态的订单'];
        }
        
        // 检查是否已经付款（通过余额日志判断）
        $paymentLog = Db::name('xy_balance_log')
            ->where('oid', $orderId)
            ->where('type', 2) // 支出类型
            ->where('status', 2) // 支出状态
            ->find();
        
        $alreadyPaid = !empty($paymentLog);
        
        if ($alreadyPaid) {
            // 已付款的订单：切换到D状态（手动派单+有商品+已付款）
            $updateData = [
                'auto_dispatch' => 0,
                'manual_dispatch' => 1,
                'dispatch_status' => 2, // 手动派单中，已付款
                'cooling_end_time' => 0 // 清除冷却时间
            ];
            $message = '已切换为手动派单模式，订单已付款，可以手动结算';
        } else {
            // 未付款的订单：无论是否有商品，都清除商品重新进入匹配流程（C状态）
            // 这包括：
            // - A状态：自动派单+无商品 -> C状态：手动派单+无商品
            // - B状态：自动派单+有商品（冷却中） -> C状态：手动派单+无商品（清除自动派的商品）
            $updateData = [
                'auto_dispatch' => 0,
                'manual_dispatch' => 1,
                'dispatch_status' => 0, // 等待匹配
                'cooling_end_time' => 0, // 清除冷却时间
                'goods_id' => null, // 清除商品，重新匹配
                'goods_count' => 0,
                'num' => 0, // 清除订单金额，等待重新匹配商品后设置
                'commission' => 0 // 清除佣金，等待重新匹配商品后计算
            ];
            
            // 根据原状态给出不同的提示
            if (!empty($order['goods_id']) && $order['goods_id'] > 0) {
                $message = '已切换为手动派单模式，已清除自动派单的商品和价格信息，请重新匹配商品';
            } else {
                $message = '已切换为手动派单模式，请重新匹配商品';
            }
        }
        
        $result = Db::name('xy_convey')->where('id', $orderId)->update($updateData);
        
        if ($result !== false) {
            return ['code' => 0, 'info' => $message];
        } else {
            return ['code' => 1, 'info' => '切换失败'];
        }
    }

    /**
     * 手动派单强制付款
     * @param string $orderId 订单ID
     * @param int $goodsId 商品ID
     * @param float $goodsPrice 商品价格
     * @param int $goodsCount 商品数量（固定为1）
     * @return array
     */
    public function manualDispatchPayment($orderId, $goodsId, $goodsPrice, $goodsCount = 1)
    {
        // 强制商品数量为1
        $goodsCount = 1;
        Db::startTrans();
        try {
            // 1. 获取订单信息
            $order = Db::name('xy_convey')->where('id', $orderId)->find();
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            
            // 支持C状态和D状态的手动派单
            // C状态：dispatch_status=0, manual_dispatch=1 (初次派单)
            // D状态：dispatch_status=2, manual_dispatch=1 (修改商品)
            if ($order['manual_dispatch'] != 1 || !in_array($order['dispatch_status'], [0, 2])) {
                throw new \Exception('订单状态不允许手动派单');
            }
            
            // 2. 获取用户信息
            $user = Db::name('xy_users')->where('id', $order['uid'])->find();
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            
            // 3. 获取商品信息并验证
            $goods = Db::name('xy_goods_list')->where('id', $goodsId)->find();
            if (!$goods) {
                throw new \Exception('商品不存在');
            }
            
            // 4. 计算订单金额和佣金（商品数量固定为1）
            $orderAmount = $goodsPrice * $goodsCount; // $goodsPrice * 1
            $commission = $this->calculateCommission($orderAmount, $user['level']);
            
            // 5. 设置用户状态为交易中（只在C状态初次派单时设置）
            $isFirstTimePayment = ($order['dispatch_status'] == 0); // C状态是初次扣费
            
            if ($isFirstTimePayment && $user['deal_status'] != 3) {
                // 只有在初次派单且用户不是交易中状态时才更新
                $updateUserResult = Db::name('xy_users')
                    ->where('id', $order['uid'])
                    ->update(['deal_status' => 3]); // 3=交易中
                
                if (!$updateUserResult) {
                    throw new \Exception('更新用户状态失败');
                }
            }
            
            // 6. 更新订单商品信息
            $updateOrderData = [
                'goods_id' => $goodsId,
                'goods_count' => $goodsCount,
                'num' => $orderAmount,
                'commission' => $commission,
                'dispatch_status' => 2 // 已手动派单，等待结算
                // status 保持为 0，等待后续结算时再更新为 1
            ];
            
            $updateOrderResult = Db::name('xy_convey')->where('id', $orderId)->update($updateOrderData);
            if (!$updateOrderResult) {
                throw new \Exception('更新订单信息失败');
            }
            
            // 7. 处理扣费逻辑
            $isModifyGoods = ($order['dispatch_status'] == 2); // D状态是修改商品
            
            if ($isFirstTimePayment) {
                // C状态：初次扣费
                $newBalance = $user['balance'] - $orderAmount;
                $updateBalanceResult = Db::name('xy_users')->where('id', $order['uid'])->update([
                    'balance' => $newBalance
                ]);
                
                if (!$updateBalanceResult) {
                    throw new \Exception('扣除用户余额失败');
                }
                
                // 记录余额变动日志
                $logData = [
                    'uid' => $order['uid'],
                    'oid' => $orderId,
                    'num' => $orderAmount,
                    'type' => 2, // 用户接单，支出状态
                    'status' => 2, // 支出状态
                    'addtime' => time()
                ];
                
                $logResult = Db::name('xy_balance_log')->insert($logData);
                if (!$logResult) {
                    throw new \Exception('记录余额日志失败');
                }
                
                $paymentMessage = '手动派单成功，已强制扣除用户余额';
            } else {
                // D状态：修改商品，需要处理差价
                $oldAmount = $order['num']; // 原订单金额
                $amountDiff = $orderAmount - $oldAmount; // 价格差异
                
                if ($amountDiff != 0) {
                    // 有价格差异，需要调整余额
                    $newBalance = $user['balance'] - $amountDiff;
                    $updateBalanceResult = Db::name('xy_users')->where('id', $order['uid'])->update([
                        'balance' => $newBalance
                    ]);
                    
                    if (!$updateBalanceResult) {
                        throw new \Exception('调整用户余额失败');
                    }
                    
                    // 记录差价变动日志
                    if ($amountDiff > 0) {
                        // 新商品更贵，需要补扣
                        $logData = [
                            'uid' => $order['uid'],
                            'oid' => $orderId,
                            'num' => $amountDiff,
                            'type' => 2, // 支出
                            'status' => 2,
                            'addtime' => time()
                        ];
                        $paymentMessage = "商品修改成功，补扣差价 ¥{$amountDiff}";
                    } else {
                        // 新商品更便宜，退回差价
                        $refundAmount = abs($amountDiff);
                        $logData = [
                            'uid' => $order['uid'],
                            'oid' => $orderId,
                            'num' => $refundAmount,
                            'type' => 1, // 收入
                            'status' => 1,
                            'addtime' => time()
                        ];
                        $paymentMessage = "商品修改成功，退回差价 ¥{$refundAmount}";
                    }
                    
                    $logResult = Db::name('xy_balance_log')->insert($logData);
                    if (!$logResult) {
                        throw new \Exception('记录差价日志失败');
                    }
                } else {
                    // 价格相同，无需调整余额
                    $paymentMessage = '商品修改成功，价格相同无需调整余额';
                }
            }
            
            Db::commit();
            
            return [
                'code' => 0, 
                'info' => $paymentMessage,
                'data' => [
                    'order_amount' => $orderAmount,
                    'commission' => $commission,
                    'user_balance' => isset($newBalance) ? $newBalance : $user['balance']
                ]
            ];
            
        } catch (\Exception $e) {
            Db::rollback();
            return ['code' => 1, 'info' => $e->getMessage()];
        }
    }

    /**
     * 计算佣金（只基于用户等级的佣金比例）
     * @param float $amount 订单金额
     * @param int $userLevel 用户等级
     * @return float
     */
    private function calculateCommission($amount, $userLevel)
    {
        // 确保用户等级为有效数字，默认为0（普通会员）
        $userLevel = is_numeric($userLevel) ? intval($userLevel) : 0;
        
        // 获取用户等级佣金比例
        $levelInfo = Db::name('xy_level')->where('level', $userLevel)->find();
        
        // 如果找到等级信息且佣金比例有效
        if ($levelInfo && isset($levelInfo['bili']) && $levelInfo['bili'] > 0) {
            return $amount * floatval($levelInfo['bili']);
        }
        
        // 如果没有找到等级信息，使用默认等级0（普通会员）的佣金比例
        $defaultLevel = Db::name('xy_level')->where('level', 0)->find();
        if ($defaultLevel && isset($defaultLevel['bili']) && $defaultLevel['bili'] > 0) {
            return $amount * floatval($defaultLevel['bili']);
        }
        
        // 最后的保底：使用2.5%的默认佣金比例
        return $amount * 0.025;
    }

    /**
     * 检查和修复订单状态一致性
     * 修复已完成派单但status仍为0的历史数据
     * @return array 修复结果
     */
    public function fixOrderStatusConsistency()
    {
        // 查找状态不一致的订单
        $inconsistentOrders = Db::name('xy_convey')
            ->where('status', 0) // 订单状态为待付款
            ->where(function($query) {
                $query->where(function($q) {
                    // 自动派单已完成但订单状态未更新
                    $q->where('auto_dispatch', 1)->where('dispatch_status', 1);
                })->whereOr(function($q) {
                    // 手动派单已完成但订单状态未更新
                    $q->where('manual_dispatch', 1)->where('dispatch_status', 1);
                });
            })
            ->select();

        $fixedCount = 0;
        $errors = [];

        foreach ($inconsistentOrders as $order) {
            try {
                // 修复状态不一致：将status设置为1（已完成）
                $updateResult = Db::name('xy_convey')
                    ->where('id', $order['id'])
                    ->update(['status' => 1]);

                if ($updateResult !== false) {
                    $fixedCount++;
                    error_log("修复订单状态一致性: 订单ID {$order['id']}, 用户ID {$order['uid']}");
                } else {
                    $errors[] = "修复订单 {$order['id']} 失败";
                }
            } catch (\Exception $e) {
                $errors[] = "修复订单 {$order['id']} 异常: " . $e->getMessage();
            }
        }

        return [
            'code' => 0,
            'total_found' => count($inconsistentOrders),
            'fixed_count' => $fixedCount,
            'errors' => $errors,
            'message' => "检查了 " . count($inconsistentOrders) . " 个不一致订单，修复了 {$fixedCount} 个"
        ];
    }
}