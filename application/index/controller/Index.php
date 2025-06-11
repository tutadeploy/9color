<?php
namespace app\index\controller;

use library\Controller;
use think\Db;

/**
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Index extends Base
{

    public function index()
    {
        $this->changelang();
            $this->redirect('home');
    }

    public function home()
    {
        
        $uid = session('user_id');
        
         if(!$uid) $this->redirect('User/login');
        
        $this->info = Db::name('xy_index_msg')->field('content')->select();
        $this->balance = Db::name('xy_users')->where('id',session('user_id'))->sum('balance');
        $this->banner = Db::name('xy_banner')->select();
        $this->notice = db('xy_index_msg')->where('id',1)->value('content');
        $this->hezuo = db('xy_index_msg')->where('id',4)->value('content');
        $this->jianjie = db('xy_index_msg')->where('id',2)->value('content');
        $this->guize = db('xy_index_msg')->where('id',3)->value('content');
        $this->gundong = db('xy_index_msg')->where('id',8)->find();
        $this->tanchunag = db('xy_index_msg')->where('id',11)->find();
      

        $this->assign('pic','/upload/qrcode/user/'.(session('user_id')%20).'/'.session('user_id').'-1.png');
        $this->cate = db('xy_goods_cate')->alias('c')
            ->leftJoin('xy_level u','u.id=c.level_id')
            ->field('c.name,c.id,c.cate_info,c.cat_ico,c.bili,u.order_num,u.num,u.auto_vip_xu_num,c.cate_pic,c.deal_min_num,u.name as levelname,u.pic,u.level')
            ->order('c.id asc')->select();


        //一天的
        $this->lixibao = db('xy_lixibao_list')->order('id asc')->find();

        //
        $uid = session('user_id');
        if(isset($uid)){
           $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
            $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
$this->tod_user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->where('addtime','between',[strtotime(date("Y-m-d")),time()])->sum('commission');
$this->yes_user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');
$this->user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->sum('commission');
            $userinfo= db('xy_users')->find($uid);
            if($userinfo['level']==0&&$userinfo['balance']<30){$userinfo['level']=-1;}
            $this->info = $userinfo;
        }else{
            $this->tod_user_yongjin = 0;
            $this->yes_user_yongjin = 0;
            $this->user_yongjin = 0;
            $info['username']=lang("未登录用户");
            $info['balance']="0";
            $info['headpic']="";
            $info['level']="";
            $this->info=$info;
        }
        
      
        $percent=$this->getpercent();
         $this->percent=$percent;
          /**
        $scrollarray=array();
                    $fileurl = APP_PATH . "../config/indexscrollnew.txt";
                    $text = file_get_contents($fileurl);
                    $text = explode("\r\n",$text);
                    if($text){
                       foreach( $text as $k=>&$_list ) {
                        $temp=explode(";",$_list);
                        $scrollarray[$k]['tel']=$temp[0];
                        $scrollarray[$k]['num']=intval($temp[1]*$percent);
                        } 
                    }
        $this->scrollarray=$scrollarray;
    **/

        $color = sysconf('app_color');
        if($color){
            return $this->fetch('home-'.$color);
        }else{

            return $this->fetch('home-blue');
        }
    }

    //获取首页图文
    public function get_msg()
    {
        $type = input('post.type/d',1);
        $data = Db::name('xy_index_msg')->find($type);
        if($data)
            return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$data]);
        else
            return json(['code'=>1,'info'=>lang('暂无数据')]);
    }




    //获取首页图文
    public function getTongji()
    {
        $percent=(time()-strtotime(date("Y-m-d"),time()))/(24*60*60);
        
        $xunimoney=intval(sysconf('index_money_num')*$percent);
        $xunimember=intval(sysconf('index_member_num')*$percent);
        
        $type = input('post.type/d',1);
        $data = array();

        $data['user'] = db('xy_users')->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d'))-24*3600,time()])->count('id');
        $data['user']=$data['user']+$xunimember;
        $data['goods'] = db('xy_goods_list')->count('id');;
        $data['price'] = db('xy_convey')->where('status',1)->where('endtime','between',[strtotime(date('Y-m-d'))-24*3600,strtotime(date('Y-m-d'))])->sum('num');
        $data['price']=$data['price']+$xunimoney;
        $user_order = db('xy_convey')->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->field('uid')->Distinct(true)->select();
        $data['num'] = count($user_order);

        if($data){
            return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$data]);
        } else {
            return json(['code' => 1, 'info' => lang('暂无数据')]);
        }
    }

    function getDanmu()
    {
        $barrages=    //弹幕内容
            array(
                array(
                    'info'   => '用户173***4985开通会员成功',
                    'href'   => '',

                ),
                array(
                    'info'   => '用户136***1524开通会员成功',
                    'href'   => '',
                    'color'  =>  '#ff6600'

                ),
                array(
                    'info'   => '用户139***7878开通会员成功',
                    'href'   => '',
                    'bottom' => 450 ,
                ),
                array(
                    'info'   => '用户159***7888开通会员成功',
                    'href'   => '',
                    'close'  =>false,

                ),array(
                'info'   => '用户151***7799开通会员成功',
                'href'   => '',

                )
            );

        echo   json_encode($barrages);
    }
    
     public function app()
    {
        
        return $this->fetch();
    }
        public function get_user_msg()
    {
        
        $uid = session('user_id');
        $data['uid']=$uid;
        $data['num']=Db::table('xy_message')->where('uid',$uid)->where('status',0)->count('id');
        $data['info']=Db::table('xy_message')->where('uid',$uid)->where('status',0)->find();
        
            return json(['code'=>1,'uid'=>$uid,'num'=>$data['num'],'info'=>$data['info']]);
    }
     public function set_user_msg()
    {
        
        if(!request()->isPost()) return json(['code'=>0,'info'=>'错误请求']);
        $id = input('post.id/s',0);
        if(!$id) return json(['code'=>0,'info'=>'消息id错误']);
        $check=Db::table('xy_message')->where('uid',session('user_id'))->where('id',$id)->find();
        if($check){
            
        Db::table('xy_message')->where('id',$id)->update(['status'=>1]);
        
            return json(['code'=>1,'info'=>"设置已读成功"]);
        }else{
            
            return json(['code'=>0,'info'=>"消息不属于当前用户"]);
        }
    }

  
}
