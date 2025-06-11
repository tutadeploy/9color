<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 下单控制器
 */
class RotOrder extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
        ];
        $this->day_deal = Db::name('xy_convey')->where($where)->where('status','in',[1,3,5])->sum('commission');
//        $this->day_l_count = Db::name('xy_convey')->where($where)->where('status',5)->count('num');//交易冻结单数

        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        $this->price = Db::name('xy_users')->where('id',session('user_id'))->sum('balance');

        $this->day_d_count = Db::name('xy_convey')->where($where)->where('status','in',[0,1,3,5])->count('id');
        $this->lock_deal = Db::name('xy_users')->where('id',session('user_id'))->sum('freeze_balance');
        
         $uid5s = model('admin/Users')->child_user(session('user_id'),5);
        //$this->yes_team_num = Db::name('xy_reward_log')->where('uid',session('user_id'))->where('addtime','between',[$yes1,$yes2])->where('status',1)->sum('num');
        
      //  $this->yes_team_numold =  db('xy_convey')->where('status',1)->where('uid','in',$uid5s)->where('addtime','between',[$yes1,$yes2])->where('status',1)->sum('commission');
        
        $this->yes_team_num =  db('xy_balance_log')->where('status',1)->where('type',6)->where('uid',session('user_id'))->where('addtime','between',[$yes1,$yes2])->sum('num');
        
       // $this->today_team_numold = db('xy_convey')->where('status',1)->where('uid','in',$uid5s)->where('addtime','between',[strtotime(date("Y-m-d")),time()])->where('status',1)->sum('commission');;//获取下级返佣数额

        $this->today_team_num =  db('xy_balance_log')->where('status',1)->where('type',6)->where('uid',session('user_id'))->where('addtime','between',[strtotime(date("Y-m-d")),time()])->sum('num');
        //分类
        $type = input('get.type/d',1);
        $this->cate = Db::name('xy_goods_cate')->alias('c')
            ->leftJoin('xy_level u','u.id=c.level_id')
            ->field('c.name,c.cate_info,c.cate_pic,u.name as levelname,u.auto_vip_xu_num,u.num,u.pic,u.level,u.bili,u.order_num')
            ->find($type);;
        $this->beizhu = db('xy_index_msg')->where('id',9)->value('content');

        $this->yes_user_yongjin = db('xy_convey')->where('uid',session('user_id'))->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');
        $this->user_yongjin = db('xy_convey')->where('uid',session('user_id'))->where('status',1)->sum('commission');


        $member_level = db('xy_level')->order('level asc')->select();
        $order_num = $member_level[0]['order_num'];
        $uinfo = db('xy_users')->where('id', session('user_id'))->find();
        if (!empty($uinfo['level'])){
            $order_num = db('xy_level')->where('level',$uinfo['level'])->value('order_num');;
        }
        $this->order_num = $order_num;
        
        
        $goods_list_pic1 = db('xy_goods_list')->field('id,goods_pic')->orderRaw('rand()')->limit(30)->select();
        $goods_list_pic2 = db('xy_goods_list')->field('id,goods_pic')->orderRaw('rand()')->limit(30)->select();
        $goods_list_pic3 = db('xy_goods_list')->field('id,goods_pic')->orderRaw('rand()')->limit(30)->select();
        $goods_list_pic1[27]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic1[28]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic1[29]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic2[27]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic2[28]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic2[29]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic3[27]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic3[28]['goods_pic']="/static_new6/img/wenhao.png";
        $goods_list_pic3[29]['goods_pic']="/static_new6/img/wenhao.png";
        $this->assign('goods_list_pic1',$goods_list_pic1);
        $this->assign('goods_list_pic2',$goods_list_pic2);
        $this->assign('goods_list_pic3',$goods_list_pic3);


            $uid = session('user_id');
            $userinfo= db('xy_users')->field('balance,level')->find($uid);
            if($userinfo['level']==0&&$userinfo['balance']<30){$userinfo['level']=-1;}
            $this->info = $userinfo;
        $color = sysconf('app_color');
        if($color){
            return $this->fetch('index-'.$color);
        }else{

            return $this->fetch('index-blue');
        }
    }
  /**
    *提交抢单
    */
    public function submit_order()
    {
        $uid = session('user_id');
        
        if(!$uid)  return json(['code'=>3,'info'=>lang('请登陆后在抢单')]);
        $tmp = $this->check_deal();
        if($tmp) return json($tmp);
        $res = check_time(9,22);
        //if($res) return json(['code'=>1,'info'=>'禁止在9:00~22:00以外的时间段执行当前操作!']);

        $res = check_time(config('order_time_1'),config('order_time_2'));
        $str = config('order_time_1').":00  - ".config('order_time_2').":00";
        if($res) return json(['code'=>1,'info'=>lang('禁止在').$str.lang('以外的时间段执行当前操作')]);

        
        
        $add_id = db('xy_member_address')->where('uid',$uid)->value('id');//获取收款地址信息
        if(!$add_id) return json(['code'=>2,'info'=>lang('还没有设置收货地址')]);
        //检查交易状态
        // $sleep = mt_rand(config('min_time'),config('max_time'));
        $res = db('xy_users')->where('id',$uid)->update(['deal_status'=>2]);//将账户状态改为等待交易
        if($res === false) return json(['code'=>1,'info'=>lang('抢单失败,请稍后再试')]);
        // session_write_close();//解决sleep造成的进程阻塞问题
        // sleep($sleep);
        //
        
        
        $cid = input('post.cid/d',1);
        $count = db('xy_goods_list')->where('cid','=',$cid)->count();
        

        if($count < 1) return json(['code'=>1,'info'=>lang('抢单失败,商品库存不足')]);


        $res = model('admin/Convey')->create_order($uid,$cid);
        return json($res);
    }

    /**
     * 停止抢单
     */
    public function stop_submit_order()
    {
        $uid = session('user_id');
        $res = db('xy_users')->where('id',$uid)->where('deal_status',2)->update(['deal_status'=>1]);
        if($res){
            return json(['code'=>0,'info'=>lang('操作成功')]);
        }else{
            return json(['code'=>1,'info'=>lang('操作失败')]);
        }
    }
    
     public function check_order_time(){
         
            //return json(['code'=>0]);
            return json(['code'=>1]);
     }
     
public function firstdayorder($uid){
    $check = Db::name('xy_convey')->where('uid',$uid)->order('id asc')->field('addtime')->find();
        if($check){
             $fistday = strtotime(date('Y-m-d',$check['addtime'])."23:59:59");
             if($fistday>time()){
                return Db::name('xy_convey')->where('uid',$uid)->where('addtime','<=',$fistday)->count();
             }else{
                return 0;
             }
            
        }else{
                return 0;
        }
    }

}