<?php
namespace app\index\controller;

use library\Controller;
use think\facade\Request;
use think\Db;

/**
 * 验证登录控制器
 */
class Base extends Controller
{
    protected $rule = ['__token__' => 'token'];
    protected $msg  = ['__token__'  => 'error token！'];

    function __construct() {
        
        parent::__construct();
        
           if(!cookie('think_var')){
             $url=$this->getUrl();
             cookie('think_var', 'jp-jp', time()+3600*24);
             $this->redirect($url);
             
        } 
        
        $uid = session('user_id');
        if (!$uid) {
            $uid = cookie('user_id');
        }
        if($uid){
            model('admin/Users')->auto_check_up_vip($uid);
        }
   if (sysconf('isopenpcindex')) {
        $dev = new \org\Mobile();
        $t = $dev->isMobile();
        if (!$t) {
                 $this->redirect('/download');
            }
            
        }
     /**if(sysconf('allowindex')){
        $request = Request::instance();
       if(!($request->module()=="index"&&$request->controller()=="Index"&&$request->action()=="home")||!($request->module()=="index"&&$request->controller()=="RotOrder"&&$request->action()=="index")){
        
       }else{
            if(!$uid && request()->isPost()){
                $this->error(lang('请先登录'));
            }
            if(!$uid) $this->redirect('User/login'); 
       }
            
        }else{
            
        if(!$uid && request()->isPost()){
            $this->error(lang('请先登录'));
        }
        if(!$uid) $this->redirect('User/login');
        }
       
        $request = Request::instance();
       if($request->module()=="index"&&$request->controller()=="Index"&&$request->action()=="home"){
         echo "当前模块名称是" . $request->module(); 
         echo "当前控制器名称是" . $request->controller(); 
         echo "当前操作名称是" . $request->action(); 
         die;
       }**/
        /***实时监测账号状态***/
        $uinfo = db('xy_users')->find($uid);
        if($uinfo['status']!=1){
            \Session::delete('user_id');
             $this->redirect('User/login');
        }
        $this->console = db('xy_script')->where('id',1)->value('script');
    }



    //图片上传为base64为的图片
    public function upload_base64($type,$img){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
            $type_img = $result[2];  //得到图片的后缀
            //上传 的文件目录

            $App = new \think\App();
            $new_files = $App->getRootPath() . 'upload'. DIRECTORY_SEPARATOR . $type. DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m-d') . DIRECTORY_SEPARATOR ;

            if(!file_exists($new_files)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                //服务器给文件夹权限
                mkdir($new_files, 0777,true);
            }
            //$new_files = $new_files.date("YmdHis"). '-' . rand(0,99999999999) . ".{$type_img}";
            $new_files = check_pic($new_files,".{$type_img}");
            if (file_put_contents($new_files, base64_decode(str_replace($result[1], '', $img)))){
                //上传成功后  得到信息
                $filenames=str_replace('\\', '/', $new_files);
                $file_name=substr($filenames,strripos($filenames,"/upload"));
                return $file_name;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 检查交易状态
     */
    public function check_deal()
    {
        $uid = session('user_id');
        $uinfo = db('xy_users')->field('deal_status,status,balance,level,deal_count,deal_time,deal_reward_count dc')->find($uid);
        if($uinfo['status']==2) return ['code'=>1,'info'=>lang('该账户已被禁用')];
        if($uinfo['deal_status']==0) return ['code'=>1,'info'=>lang('该账户交易功能已被冻结')];
        
        $ordercheck = Db::name('xy_convey')->where('uid',session('user_id'))->where('status',0)->find();
        if($ordercheck){
            return ['code'=>4,'info'=>lang('该账户存在未完成订单,无法继续抢单')];
        }
        //if($uinfo['deal_status']==3) return ['code'=>4,'info'=>'该账户存在未完成订单，无法继续抢单！'];
        if($uinfo['balance']<config('deal_min_balance')) return ['code'=>1,'info'=>lang('余额低于').config('deal_min_balance').lang('无法继续交易')];
        //$count = db('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->where('uid',session('user_id'))->where('status',2)->count('id');//统计当天完成交易的订单
        // if($count>=config('deal_count')) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        if($uinfo['deal_time']==strtotime(date('Y-m-d'))){
            //交易次数限制
            $level = $uinfo['level'];
            !$uinfo['level'] ? $level = 0 : '';
            $ulevel = Db::name('xy_level')->where('level',$level)->find();
            if ($uinfo['deal_count'] >= $ulevel['order_num']) {
                return ['code'=>1,'info'=>lang('会员等级交易次数不足')];
            }

            //if($uinfo['deal_count'] >= config('deal_count')+$uinfo['dc']) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        }else{
            //重置最后交易时间
            db('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0]);
        }

        return false;
    }

    
        /**
     * 入口跳转链接
     */
    public function changelang() {
            $lang=input('lang');
            switch ($lang) {
                case 'jp':
                    cookie('think_var', 'jp-jp', time()+3600*24);
                    break;
                case 'kr':
                    cookie('think_var', 'kr-kr', time()+3600*24);
                    break;
                case 'en':
                    cookie('think_var', 'en-us', time()+3600*24);
                    break;
                case 'zn':
                    cookie('think_var', 'zh-cn', time()+3600*24);
                    break;
                case 'fr':
                    cookie('think_var', 'fr-fr', time()+3600*24);
                    break;
                case 'es':
                    cookie('think_var', 'es-es', time()+3600*24);
                    break;
                case 'pt':
                    cookie('think_var', 'pt-pt', time()+3600*24);
                    break;
                case 'ind':
                    cookie('think_var', 'ind-ind', time()+3600*24);
                    break;
                case 'th':
                    cookie('think_var', 'th-th', time()+3600*24);
                    break;
                default:
                    cookie('think_var','th-th' ,time()+3600*24);
                    break;
            }
    }
     public function getpercent(){
         $today = strtotime(date("Y-m-d 00:00:00"));
        $percent=(time()-$today)/(60*60*24);
        return $percent;
    }
        function getUrl() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
  

}
