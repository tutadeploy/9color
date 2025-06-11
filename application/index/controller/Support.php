<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Support extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        
        $uid = session('user_id');
        if(!$uid) $this->redirect('User/login'); 
        $this->info = db('xy_cs')->where('status',1)->select();
        $this->assign('list',$this->info);

        $this->msg = db('xy_index_msg')->where('status',1)->select();
        $color = sysconf('app_color');
        if($color){
            return $this->fetch('index-'.$color);
        }else{

            return $this->fetch('index-blue');
        }
    }


    public function index2()
    {
        $this->url = isset($_REQUEST['url']) ? $_REQUEST['url'] :'';
        return $this->fetch();
    }

    /**
     * 首页
     */
    public function detail()
    {
        $id = input('get.id/d',1);
        $this->info = db('xy_index_msg')->where('id',$id)->find();


        return $this->fetch();
    }




    /**
     * 换一个客服
     */
    public function other_cs()
    {
        $data = db('xy_cs')->where('status',1)->where('id','<>',$id)->find();
        if($data) return json(['code'=>0,'info'=>lang('请求成功'),'data'=>$data]);
        return json(['code'=>1,'info'=>lang('暂无数据')]);
    }
}