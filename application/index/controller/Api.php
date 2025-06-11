<?php

namespace app\index\controller;

use library\Controller;
use think\Db;

/**
 * 支付控制器
 */
class Api extends Controller
{

    public $BASE_URL = "https://bapi.app";
    public $appKey = '';
    public $appSecret = '';

    const POST_URL = "https://pay.bbbapi.com/";


    public function __construct()
    {
        $this->appKey = config('app.bipay.appKey');
        $this->appSecret = config('app.bipay.appSecret');
    }

    public function bipay()
    {

        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {
                $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
                $notifyUrl = $server_url.url('/index/api/bipay_notify');
                $returnUrl = $server_url.url('/index/api/bipay_return');
                $price = $r['num'] * 100;
                $res = $this->create_order($oid,$price,'用户充值',$notifyUrl, $returnUrl);

                if ($res && $res['code']==200) {
                    $url = $res['data']['pay_url'];
                    $this->redirect($url);
                }
            }
        }
    }

    public function bipay_return()
    {
        return $this->fetch();
    }


    public function bipay_notify()
    {

        $content = file_get_contents('php://input');
        $post    = (array)json_decode($content, true);

        if (!$post['order_id']) {
            die('fail');
        }
        $oid = $post['order_id'];
        $r = db('xy_recharge')->where('id',$oid)->find();
        if (!$r) {
            die('fail');
        }
        if ($post['order_state']!=1) {
            die('fail');
        }

        if ($r['status'] == 2){
            die('SUCCESS');
        }

        if ($post['order_state']) {
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
            /************* 发放推广奖励 *********/
            $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
            if($uinfo['active']===0){
                Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                //将账号状态改为已发放推广奖励
                $userList = model('Users')->parent_user($uinfo['id'],3);
                if($userList){
                    foreach($userList as $v){
                        if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                            Db::name('xy_reward_log')
                                ->insert([
                                    'uid'=>$v['id'],
                                    'sid'=>$uinfo['id'],
                                    'oid'=>$oid,
                                    'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                    'lv'=>$v['lv'],
                                    'type'=>1,
                                    'status'=>1,
                                    'addtime'=>time(),
                                ]);
                        }
                    }
                }
            }
            /************* 发放推广奖励 *********/
            die('SUCCESS');
        }
    }


    public function create_order(
        $orderId, $amount, $body, $notifyUrl, $returnUrl, $extra = '', $orderIp = '', $amountType = 'CNY', $lang = 'zh_CN')
    {
        $reqParam = [
            'order_id' => $orderId,
            'amount' => $amount,
            'body' => $body,
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
            'extra' => $extra,
            'order_ip' => $orderIp,
            'amount_type' => $amountType,
            'time' => time() * 1000,
            'app_key' => $this->appKey,
            'lang' => $lang
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/pay';

        return $this->http_request($url, 'POST', $reqParam);
    }

    /**
     * @return {
     * bapp_id: "2019081308272299266f",
     * order_id: "1565684838",
     * order_state: 0,
     * body: "php-sdk sample",
     * notify_url: "https://sdk.b.app/api/test/notify/test",
     * order_ip: "",
     * amount: 1,
     * amount_type: "CNY",
     * amount_btc: 0,
     * pay_time: 0,
     * create_time: 1565684842076,
     * order_type: 2,
     * app_key: "your_app_key",
     * extra: ""
     * }
     */
    public function get_order($orderId)
    {
        $reqParam = [
            'order_id' => $orderId,
            'time' => time() * 1000,
            'app_key' => $this->appKey
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/order';
        return $this->http_request($url, 'GET', $reqParam);
    }

    public function is_sign_ok($params)
    {
        $sign = $this->create_sign($params, $this->appSecret);
        return $params['sign'] == $sign;
    }

    public function create_sign($params, $appSecret)
    {
        $signOriginStr = '';
        ksort($params);
        foreach ($params as $key => $value) {
            if (empty($key) || $key == 'sign') {
                continue;
            }
            $signOriginStr = "$signOriginStr$key=$value&";
        }
        return strtolower(md5($signOriginStr . "app_secret=$appSecret"));
    }

    private function http_request($url, $method = 'GET', $params = [])
    {
        $curl = curl_init();

        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            $jsonStr = json_encode($params);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonStr);
        } else if ($method == 'GET') {
            $url = $url . "?" . http_build_query($params, '', '&');
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $output = curl_exec($curl);

        if (curl_errno($curl) > 0) {
            return [];
        }
        curl_close($curl);
        $json = json_decode($output, true);

        //var_dump($output,curl_errno($curl));die;

        return $json;
    }


    //----------------------------------------------------------------
    //  paysapi
    //----------------------------------------------------------------

    public function pay(){

        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {

                //var_dump($r);die;

                $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
                $notify_url = $server_url.url('/index/api/pay_notify');
                $return_url = $server_url.url('/index/api/bipay_return');
                $price = $r['num'] * 100;


                $uid   = config('app.paysapi.uid');    //"此处填写Yipay的uid";
                $token = config('app.paysapi.token');;     //"此处填写Yipay的Token";

                $orderid = $r['id'];
                $goodsname= '用户充值';
                $istype =  config('app.paysapi.istype');
                $orderuid = session('user_id');

                $key = md5($goodsname. $istype . $notify_url . $orderid . $orderuid . $price . $return_url . $token. $uid);

                $data = array(
                    'goodsname'=>$goodsname,
                    'istype'=>$istype,
                    'key'=>$key,
                    'notify_url'=>$notify_url,
                    'orderid'=>$orderid,
                    'orderuid'=>$orderuid,
                    'price'=>$price,
                    'return_url'=>$return_url,
                    'uid'=>$uid
                );
                $this->assign('data',$data);
                $this->assign('post_url',self::POST_URL);
                return $this->fetch();
            }
        }

    }


    /**
     * notify_url接收页面
     */
    public function pay_notify(){

        $paysapi_id = $_POST["paysapi_id"];
        $orderid = $_POST["orderid"];
        $price = $_POST["price"];
        $realprice = $_POST["realprice"];
        $orderuid = $_POST["orderuid"];
        $key = $_POST["key"];



        //校验传入的参数是否格式正确，略
        $d = $payType = array();
        if ($orderid) {
            $out_trade_no = $orderid;
            //$res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);

            //$d = M('recharge')->where(array('order_no'=>$out_trade_no))->find();
            //$payType = M('pay_type')->find($d['payment_type']);

        }
        $token = config('app.paysapi.token');;
        $temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);

        if ($temps != $key){
            return exit("key值不匹配");
        }else{
            //校验key成功
            $oid = $orderid;
            $r = db('xy_recharge')->where('id',$oid)->find();
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
            /************* 发放推广奖励 *********/
            $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
            if($uinfo['active']===0){
                Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                //将账号状态改为已发放推广奖励
                $userList = model('Users')->parent_user($uinfo['id'],3);
                if($userList){
                    foreach($userList as $v){
                        if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                            Db::name('xy_reward_log')
                                ->insert([
                                    'uid'=>$v['id'],
                                    'sid'=>$uinfo['id'],
                                    'oid'=>$oid,
                                    'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                    'lv'=>$v['lv'],
                                    'type'=>1,
                                    'status'=>1,
                                    'addtime'=>time(),
                                ]);
                        }
                    }
                }
            }
            /************* 发放推广奖励 *********/
            die('SUCCESS');

        }
    }


    public function webpay_notify()
    {
        
        
        $orderid = $_POST["order_id"];
        $key = $_POST["sign"];


        //校验传入的参数是否格式正确，略
        $d = $payType = array();
        if ($orderid) {
            $out_trade_no = $orderid;
            //$res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);

            //$d = M('recharge')->where(array('order_no'=>$out_trade_no))->find();
            //$payType = M('pay_type')->find($d['payment_type']);

        }
        //$token = config('app.paysapi.token');;
        //$temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);

        //if ($temps != $key){
         //   return exit("key值不匹配");
       // }else{
            //校验key成功
            $oid = $orderid;
            $r = db('xy_recharge')->where('id',$oid)->find();
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
            /************* 发放推广奖励 *********/
            $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
            if($uinfo['active']===0){
                Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                //将账号状态改为已发放推广奖励
                $userList = model('Users')->parent_user($uinfo['id'],3);
                if($userList){
                    foreach($userList as $v){
                        if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                            Db::name('xy_reward_log')
                                ->insert([
                                    'uid'=>$v['id'],
                                    'sid'=>$uinfo['id'],
                                    'oid'=>$oid,
                                    'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                    'lv'=>$v['lv'],
                                    'type'=>1,
                                    'status'=>1,
                                    'addtime'=>time(),
                                ]);
                        }
                    }
                }
            }
            /************* 发放推广奖励 *********/
            die('SUCCESS');

        //}
    }
    
    
         public function qeapay()
    {
        /** ORDER_AMT_SINGLE_PEN_LIMIT:100-200000  **/
        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        
    $bankname =isset($_REQUEST['bankname']) ? $_REQUEST['bankname']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {
                
                if($r['payInfo']&&$r['status']!=2){
                    header("Location:".$r['payInfo']);
                    exit();
                }else{
                    
                    $merchant_key ="imzdixz6zub3jc2whtcw47wxhsluuy5y";//支付秘钥
                    
                    $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
                    $page_url=$server_url.url('/index/my/index');
                    $notify_url = $server_url.url('/index/api/UPIpay_notify');
                    $return_url = $server_url.url('/index/api/UPIpay_return');
                    $version ="1.0";
                    $mch_id = "100999001";
                    $mch_order_no = $oid;//返回充值id
                    $pay_type =102; /** 101	Paytm原生一类 102	UPI原生一类 131	Paytm跑分一类 132	UPI跑分一类 **/
                    $trade_amount = $r['num'];
                    $order_date = date('Y-m-d H:i:s');
                    $bank_code = $bankname;
                    $goods_name = "用户充值";
                    $signStr = "";
                
                    $signStr = $signStr."goods_name=".$goods_name."&";
                    $signStr = $signStr."mch_id=".$mch_id."&";    
                    $signStr = $signStr."mch_order_no=".$mch_order_no."&";
                    $signStr = $signStr."notify_url=".$notify_url."&";    
                    $signStr = $signStr."order_date=".$order_date."&";
                    $signStr = $signStr."pay_type=".$pay_type."&";
                    $signStr = $signStr."trade_amount=".$trade_amount."&";
                    $signStr = $signStr."version=".$version;
                    $sign = $this->qeapaysign($signStr,$merchant_key);
                    
                    $postdata=array(
                    'goods_name'=>$goods_name,
                    'mch_id'=>$mch_id,
                    'mch_order_no'=>$mch_order_no,
                    'notify_url'=>$notify_url,
                    'order_date'=>$order_date,
                    'pay_type'=>$pay_type,
                    'trade_amount'=>$trade_amount,
                    'version'=>$version,
                    'sign_type'=>"MD5",
                    'sign'=>$sign);
                    $ch = curl_init();    
                    curl_setopt($ch,CURLOPT_URL,"https://payment.dzxum.com/pay/web"); //支付请求地址
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));  
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response=curl_exec($ch);
                    curl_close($ch);
                
              
            $json = json_decode($response,true);
            
                        if($json['respCode']=='SUCCESS'){
                            //更新支付url
    $res = Db::name('xy_recharge')->where('id',$oid)->update(['payInfo'=>$json['payInfo'],'orderNo'=>$json['orderNo'],'orderDate'=>$json['orderDate']]);
                            if($res){
                                header("Location:".$json['payInfo']);
                                exit();
                            }
                        }else{
                            
                            print_r($response);
                            //$this->redirect(url('index/ctrl/recharge'));
                        } 
                   
                }
                

          
            }
        }
        
        
    }

public function UPIpay_notify(){

file_put_contents('UPIpay_notify.log', date('m-d H:i:s').json_encode($_REQUEST)."\r\n",FILE_APPEND);
if(empty($_POST["mchOrderNo"])) exit("mchOrderNo empty");
$merchant_key ="imzdixz6zub3jc2whtcw47wxhsluuy5y";
$tradeResult = $_POST["tradeResult"];
$oriAmount = $_POST["oriAmount"];  
$amount = $_POST["amount"];    
$mchId = $_POST["mchId"];
$orderNo = $_POST["orderNo"];  
$mchOrderNo = $_POST["mchOrderNo"];
//$merRetMsg = $_POST["merRetMsg"];
$sign = $_POST["sign"];
$signType = "MD5";
$orderDate = $_POST["orderDate"];

$signStr = "";
$signStr = $signStr."amount=".$amount."&";
$signStr = $signStr."mchId=".$mchId."&";
$signStr = $signStr."mchOrderNo=".$mchOrderNo."&";
//$signStr = $signStr."merRetMsg=".$merRetMsg."&";
$signStr = $signStr."orderDate=".$orderDate."&";
$signStr = $signStr."orderNo=".$orderNo."&";
$signStr = $signStr."oriAmount=".$oriAmount."&";
$signStr = $signStr."tradeResult=".$tradeResult;

$flag = $this->validateSignByKey($signStr,$merchant_key,$sign);
    
   if (!$flag){
            return exit("key值不匹配");
        }else{
            
            //校验key成功
            $oid = $mchOrderNo;
            $r = db('xy_recharge')->where('id',$oid)->find();
            if($r['status']==2) die('SUCCESS');
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2,'notifyDate'=>date('Y-m-d H:i:s')]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
            /************* 发放推广奖励 *********/
              $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
            if($uinfo['active']===0){
                Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                //将账号状态改为已发放推广奖励
                $userList = model('Users')->parent_user($uinfo['id'],3);
                if($userList){
                    foreach($userList as $v){
                        if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                            Db::name('xy_reward_log')
                                ->insert([
                                    'uid'=>$v['id'],
                                    'sid'=>$uinfo['id'],
                                    'oid'=>$oid,
                                    'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                    'lv'=>$v['lv'],
                                    'type'=>1,
                                    'status'=>1,
                                    'addtime'=>time(),
                                ]);
                        }
                    }
                }
            }
            /************* 发放推广奖励 *********/
            die('SUCCESS');
        }
    }
public function UPIpay_return()
    {
        return $this->fetch();
    }
public  function qeapaysign($signSource,$key) {
        if (!empty($key)) {
             $signSource = $signSource."&key=".$key;
        }
        return md5($signSource);
    }
    
public function validateSignByKey($signSource, $key, $retsign) {
        if (!empty($key)) {
             $signSource = $signSource."&key=".$key;
        }
        $signkey = md5($signSource);
        if($signkey == $retsign){
            return true;
        }
        return false;
    }
 public  function http_post_res($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/x-www-form-urlencoded")); 
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);  
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);  
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; zh-CN) AppleWebKit/535.12 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/535.12");  
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
        $output = curl_exec($ch);  
        curl_close($ch);
        return $output;
    }
        public static function http_post($url, $data)
    {
        $options = array(    
            'http' => array(    
                'method' => 'POST',    
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'header' => 'Content-Encoding : gzip',
                'content' => $data,    
                'timeout' => 15 * 60 
            )    
        );         
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);  
        return $result;        
    }

public function disbursement_notify(){
//tradeResult=1&merTransferId=20201201113359&merNo=123456666&tradeNo=3000025&transferAmount=10000.00&sign=0f919e357c71c7013665e253cf1d4be7&signType=MD5&applyDate=2020-12-0111:33:59&version=1.0&respCode=SUCCESS
    
    
if(empty($_POST["merTransferId"])) exit("merTransferId empty");;

$merchant_key ="MVBVM7E0H8QGFWUHOYZ7VNENBJYJI36C"; //代付秘钥
$tradeResult = $_POST["tradeResult"];//订单状态
$merTransferId = $_POST["merTransferId"];//商家转账单号
$merNo = $_POST["merNo"];    //商户代码
$tradeNo = $_POST["tradeNo"];    //平台订单号
$transferAmount = $_POST["transferAmount"];
$sign= $_POST["sign"];
$signType = $_POST["signType"];
$applyDate = $_POST["applyDate"];
$version = $_POST["version"];
$respCode = $_POST["respCode"];
$r = db('xy_deposit')->where('id',$merTransferId)->where('qea_tradeNo',$tradeNo)->find();
   if (!$r){
           // file_put_contents('qeaout_notify.log', '订单不存在'.$merTransferId.json_encode($_REQUEST)."\r\n",FILE_APPEND);
           exit("订单不存在");
        }else{
            //订单存在
            if($r['qea_tradeResult']=='SUCCESS') die('SUCCESS');
            $res = Db::name('xy_deposit')->where('id',$merTransferId)->update(['qea_tradeResult'=>'SUCCESS','qea_respDate'=>date('Y-m-d H:i:s'),'qea_respCode'=>json_encode($_REQUEST)]);
           if(!$res){
            file_put_contents('qeaout_notify.log', '订单'.$merTransferId.'已代付,系统更改状态失败，请手动处理'."\r\n",FILE_APPEND);
           }
        }
        die('SUCCESS');

    }
    public function disbursement_check(){
        $version=$_POST["mch_id"];
        $mer_transferId=$_POST["mch_transferId"];
        $sign_type=$_POST["sign_type"];
        $signStr="";        
        $signStr=$signStr."mch_id=".$mch_id."&";
        $signStr=$signStr."mch_transferId=".$mch_transferId;
        $reqUrl = "";
        $merchant_key ="";
        $signAPI = new signapi();
        $sign = $signAPI->sign($signStr, $merchant_key);
        $postdata=array(
            'mch_id'=>$mch_id,
            'mch_transferId'=>$mch_transferId,
            'sign_type'=>$sign_type,
            'sign'=>$sign
            );
        $ch = curl_init();    
        curl_setopt($ch,CURLOPT_URL,$reqUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response=curl_exec($ch);
        curl_close($ch);
        echo $response;
    }
    

}