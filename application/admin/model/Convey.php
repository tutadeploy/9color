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
     
      $commission=0;
      $order_num=0;
      //分组配置
      if(($uinfo['pipei_type']==2)&&($uinfo['pipei_grouping']>0)){
        $group_pipei = $this->get_group_pipei_config($uid);
        if($group_pipei['cancontinue']==0)  return ['code'=>1,'info'=>lang('如需继续刷单请联系派单员')];
        
                    $max = $group_pipei['pipei_max'];
                    $min = $group_pipei['pipei_min'];
                    $commission=$group_pipei['pipei_dan_run'];
                    $order_num=$group_pipei['leftorder'];
  //  return ['code'=>1,'info'=>'grouping:'.$uinfo['pipei_grouping'].'xu:'.$group_pipei['order_xu'].'max:'.$max.'min:'.$min.'run:'.$group_pipei['pipei_dan_run']];
                    
      }else{
          
        //获取个人独立订单配置信息
        $u_pipei = model('admin/Users')->get_user_pipei_num_config($uid);
            if(($u_pipei['pipei_max']>0)&&($u_pipei['pipei_max']>$u_pipei['pipei_min'])){
                    if($u_pipei['pipei_type']==0){
                        
                    //匹配为百分比
                    $min = $uinfo['balance']*$u_pipei['pipei_min']/100;
                    $max = $uinfo['balance']*$u_pipei['pipei_max']/100;
                    }else{
                    //匹配为金额
                    $min = $u_pipei['pipei_min'];
                    $max = $u_pipei['pipei_max'];
                    }
                
            }else{
                //获取个人总体订单区间信息
               if($uinfo['deal_max_num']!=0){
                    $min = $uinfo['balance']*$uinfo['deal_min_num']/100;
                    $max = $uinfo['balance']*$uinfo['deal_max_num']/100;
                }else{
                    //调用栏目区间信息
                    if($cidinfo['deal_max_num']!=0){
                        $min = $uinfo['balance']*$cidinfo['deal_min_num']/100;
                        $max = $uinfo['balance']*$cidinfo['deal_max_num']/100;
                    }else{
                        
                        //调用系统总区间信息
                        $min = $uinfo['balance']*config('deal_min_num')/100;
                        $max = $uinfo['balance']*config('deal_max_num')/100;
                    }
                }  
            } 
      }

        
         
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
        $res = Db::name('xy_users')->where('id',$uid)->update(['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')]);//将账户状态改为交易中
        //通过商品id查找 佣金比例
     //$cate = Db::name('xy_goods_cate')->find($goods['cid']);
          //  if($goods['num'] > $uinfo['balance']) return ['code'=>1,'info'=>lang('抢到').$goods['num'].lang('的订单,余额不足,请重新抢单')];

        if(!$commission) $commission=$goods['num']*$ulevel['bili'];
            $grouping_id=$uinfo['pipei_grouping'];
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
                    //'commission'    => $goods['num']*config('vip_1_commission'),
                    //'commission'    => $goods['num']*$cate['bili'],  //交易佣金按照分类
                    //'commission'    => $goods['num']*$ulevel['bili'],  //交易佣金按照会员等级
                    
                    'commission'    => $commission,  
                    'order_num'=>$order_num,
                    'grouping_id'=>$grouping_id,
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
        
        $num = mt_rand($min,$max);//随机交易额
        
        $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$num/10,$num])
                ->where('cid','=',$cid)
                ->find();

        
          if (!$goods) {
            $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$num/20,$num])
                ->where('cid','=',$cid)
                ->find();
                
                if (!$goods) {
                $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                ->where('goods_price','between',[$num/50,$num])
                ->where('cid','=',$cid)
                ->find();
                    if(!$goods){
                        $goods = Db::name('xy_goods_list')
                        ->orderRaw('rand()')
                        ->where('goods_price','between',[$num/100,$num])
                        ->where('cid','=',$cid)
                        ->find();
                        if(!$goods){ 
                            $goods = Db::name('xy_goods_list')
                            ->orderRaw('rand()')
                            ->where('goods_price','between',[$num/500,$num])
                            ->where('cid','=',$cid)
                            ->find();
                            if(!$goods){
                                 $goods = Db::name('xy_goods_list')
                                ->orderRaw('rand()')
                                ->where('goods_price','between',[$num/1000,$num])
                                ->where('cid','=',$cid)
                                ->find();
                                if(!$goods){
                                     $goods = Db::name('xy_goods_list')
                                    ->orderRaw('rand()')
                                    ->where('goods_price','between',[$num/5000,$num])
                                    ->where('cid','=',$cid)
                                    ->find();
                                    if(!$goods){
                                         $goods = Db::name('xy_goods_list')
                                            ->orderRaw('rand()')
                                            ->where('goods_price','between',[$num/10000,$num])
                                            ->where('cid','=',$cid)
                                            ->find();
                                            if(!$goods){
                                                 $goods = Db::name('xy_goods_list')
                                                    ->orderRaw('rand()')
                                                    ->where('goods_price','between',[$num/50000,$num])
                                                    ->where('cid','=',$cid)
                                                    ->find();
                                                    if(!$goods){
                                                         $goods = Db::name('xy_goods_list')
                                                            ->orderRaw('rand()')
                                                            ->where('goods_price','between',[$num/100000,$num])
                                                            ->where('cid','=',$cid)
                                                            ->find();
                                                            if(!$goods){ 
                                                                $goods = Db::name('xy_goods_list')
                                                                    ->orderRaw('rand()')
                                                                    ->where('goods_price','between',[$num/500000,$num])
                                                                    ->where('cid','=',$cid)
                                                                    ->find();
                                                                    if(!$goods){
                                                                        $goods = Db::name('xy_goods_list')
                                                                        ->orderRaw('rand()')
                                                                        ->where('goods_price','between',[$num/1000000,$num])
                                                                        ->where('cid','=',$cid)
                                                                        ->find();
                                                                        if(!$goods){
                                                                            return ['code'=>1,'info'=>lang('抢单失败,该分类下价格区间库存不足')];die;
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

        $count = round($num/$goods['goods_price']);
        //强行匹配在区间，商品价格按计算出的单价显示
         //return ['code'=>0,'count'=>$count,'id'=>$goods['id'],'num'=>$num,'cid'=>$goods['cid'],'price'=>sprintf("%.2f",$num/$count)];
       if($count*$goods['goods_price']<$min||$count*$goods['goods_price']>$max){
            self::rand_order($min,$max,$cid);
        }
       return ['code'=>0,'count'=>$count,'id'=>$goods['id'],'num'=>$count*$goods['goods_price'],'cid'=>$goods['cid'],'price'=>$goods['goods_price']];
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
            if($status==3) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制付款，如有疑问请联系客服'),'addtime'=>time()]);
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
            if($status==4) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制取消，如有疑问请联系客服'),'addtime'=>time()]);
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
        //$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res2 = Db::name('xy_users')->where('id',$uid)->where('status',1)->setDec('freeze_balance',$num+$cnum);

        if($res){
                $res1 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $oid,
                    //'num'       => $num+$cnum,
                    'num'       => $cnum,
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
            $data['cancontinue']=0;
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
}