<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Users extends Model
{
    protected $table = 'xy_users';
    protected $rule = [
                    'username'      => 'require|length:2,15',
                    'pwd'           => 'require|length:6,16',
                    '__token__'     => 'token',
                    ];
      protected $info = [
                    'username.length'   => 'The user name is 3-100 characters long！',
                    'username.require'  => 'User name cannot be empty！',
                    'pwd.require'       => 'Password cannot be empty！',
                    'pwd.length'        => 'The password is 6-16 characters long！',
                    '__token__'         => 'The token has expired. Please refresh the page and try again！',
                    ];
    /**
     * 添加会员
     *
     * @param string $tel
     * @param string $user_name
     * @param string $pwd
     * @param int    $parent_id
     * @param string $token
     * @return array
     */
    public function add_users($tel,$user_name,$pwd,$parent_id,$token='',$pwd2='')
    {
        $tmp = Db::table($this->table)->where(['tel'=>$tel])->count();
        if($tmp){
            return ['code'=>1,'info'=>'手机号码已注册'];
        }
        $tmp = Db::table($this->table)->where(['username'=>$user_name])->count();
        if($tmp){
            return ['code'=>1,'info'=>lang('用户名重复')];
        }
        if(!$user_name) $user_name=get_username();
        $data = [
            'tel'           => $tel,
            'username'      => $user_name,
            'pwd'           => $pwd,
            'parent_id'     => $parent_id,
        ];
        if($token) $data['__token__'] = $token;

        //验证表单
        $validate   = \Validate::make($this->rule,$this->info);
        if (!$validate->check($data)) {
            return ['code'=>1,'info'=>$validate->getError()];
        }
        if($parent_id){
            $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
            if(!$parent_id){
                return ['code'=>1,'info'=>'上级ID不存在'];
            }  
        }
        
        $salt = rand(0,99999);  //生成盐
        $invite_code = self::create_invite_code();//生成邀请码

        $data['pwd'] = sha1($pwd.$salt.config('pwd_str'));
        $data['salt'] = $salt;
        $data['addtime'] = time();
        $data['invite_code'] = $invite_code;
        if($pwd2){
            $salt2 = rand(0,99999);  //生成盐
            $data['pwd2'] = sha1($pwd2.$salt2.config('pwd_str'));
            $data['salt2'] = $salt2;
        }
        //开启事务
        unset($data['__token__']);
        Db::startTrans();
        $res = Db::table($this->table)->insertGetId($data);
        if($parent_id){
            $res2 = Db::table($this->table)->where('id',$data['parent_id'])->update(['childs'=>Db::raw('childs+1'),'deal_reward_count'=>Db::raw('deal_reward_count+'.config('deal_reward_count'))]);
        }else{
            $res2 = true;
        }
        //生成二维码
        //self::create_qrcode($invite_code,$res);

        if($res && $res2){
            // 提交事务
            Db::commit();
            return ['code'=>0,'info'=>'操作成功'];
        }else
            // 回滚事务
            Db::rollback();
            return ['code'=>1,'info'=>'操作失败'];
    }
    public function add_users_email($tel,$user_name,$email,$pwd,$parent_id,$token='',$pwd2='')
    {
        
        $tmp = Db::table($this->table)->where(['username'=>$user_name])->count();
        if($tmp){
            return ['code'=>1,'info'=>lang('用户名重复')];
        }
        if(!$user_name) $user_name=get_username();
        $data = [
            'tel'           => $tel,
            'username'      => $user_name,
            'email'      => $email,
            'pwd'           => $pwd,
            'parent_id'     => $parent_id,
        ];
        if($token) $data['__token__'] = $token;

        //验证表单
        $validate   = \Validate::make($this->rule,$this->info);
        if (!$validate->check($data)) {
            return ['code'=>1,'info'=>$validate->getError()];
        }
        if($parent_id){
            $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
            if(!$parent_id){
                return ['code'=>1,'info'=>'上级ID不存在'];
            }  
        }
        
        $salt = rand(0,99999);  //生成盐
        $invite_code = self::create_invite_code();//生成邀请码

        $data['pwd'] = sha1($pwd.$salt.config('pwd_str'));
        $data['salt'] = $salt;
        $data['addtime'] = time();
        $data['invite_code'] = $invite_code;
        if($pwd2){
            $salt2 = rand(0,99999);  //生成盐
            $data['pwd2'] = sha1($pwd2.$salt2.config('pwd_str'));
            $data['salt2'] = $salt2;
        }
        //开启事务
        unset($data['__token__']);
        Db::startTrans();
        $res = Db::table($this->table)->insertGetId($data);
        if($parent_id){
            $res2 = Db::table($this->table)->where('id',$data['parent_id'])->update(['childs'=>Db::raw('childs+1'),'deal_reward_count'=>Db::raw('deal_reward_count+'.config('deal_reward_count'))]);
        }else{
            $res2 = true;
        }
        //生成二维码
        //self::create_qrcode($invite_code,$res);

        if($res && $res2){
            // 提交事务
            Db::commit();
            return ['code'=>0,'info'=>'操作成功'];
        }else
            // 回滚事务
            Db::rollback();
            return ['code'=>1,'info'=>'操作失败'];
    }
    /**
     * 编辑用户
     *
     * @param int       $id
     * @param string    $tel
     * @param string    $user_name
     * @param string    $pwd
     * @param int       $parent_id
     * @param string    $token
     * @return array
     */
    public function edit_users($id,$tel,$user_name,$pwd,$parent_id,$balance,$freeze_balance,$token,$pwd2='',$deal_min_num,$deal_max_num){
        $tmp = Db::table($this->table)->where(['tel'=>$tel])->where('id','<>',$id)->count();
        if($tmp){
            return ['code'=>1,'info'=>'手机号码已注册'];
        }
        $data = [
            'tel'               => $tel,
            'balance'           => $balance,
            'freeze_balance'    => $freeze_balance,
            'username'          => $user_name,
            'parent_id'         => $parent_id,
            'deal_min_num'         => $deal_min_num,
            'deal_max_num'         => $deal_max_num,
            '__token__'         => $token,
        ];
        if($pwd){
            //不提交密码则不改密码
            $data['pwd'] = $pwd;
        }else{
            $this->rule['pwd'] = '';
        }
        if($parent_id){
            $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
            if(!$parent_id){
                return ['code'=>1,'info'=>'上级ID不存在'];
            }  
            $data['parent_id'] = $parent_id;
        }

        $validate   = \Validate::make($this->rule,$this->info);//验证表单
        if (!$validate->check($data)) return ['code'=>1,'info'=>$validate->getError()];

        if($pwd){
            $salt = rand(0,99999); //生成盐
            $data['pwd']    = sha1($pwd.$salt.config('pwd_str'));
            $data['salt']   = $salt;
        }
        if($pwd2){
            $salt2 = rand(0,99999); //生成盐
            $data['pwd2']    = sha1($pwd2.$salt2.config('pwd_str'));
            $data['salt2']   = $salt2;
        }


        unset($data['__token__']);
        $res = Db::table($this->table)->where('id',$id)->update($data);
        if($res)
            return ['code'=>0,'info'=>'编辑成功'];
        else
            return ['code'=>1,'info'=>'操作失败'];
    }

    public function edit_users_status($id,$status)
    {
        $status = intval($status);
        $id = intval($id);

        if(!in_array($status,[1,2])) return ['code'=>1,'info'=>'参数错误'];

        if($status == 2){
            //查看有无未完成的订单
            // if($num > 0)$this->error('该用户尚有未完成的支付订单！');
        }

        $res = Db::table($this->table)->where('id',$id)->update(['status'=>$status]);
        if($res !== false)
            return ['code'=>0,'info'=>'操作成功'];
        else
            return ['code'=>1,'info'=>'操作失败'];
    }

    //生成邀请码
    public static function create_invite_code(){
        $str = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $rand_str = substr(str_shuffle($str),0,6);
        $num = Db::table('xy_users')->where('invite_code',$rand_str)->count();
        if($num)
            // return $this->create_invite_code();
            return self::create_invite_code();
        else
            return $rand_str;
    }

    //生成用户二维码
    public static function create_qrcode($invite_code,$user_id){ 
        $n = ($user_id%20);    
        
        $dir = './upload/qrcode/user/'.$n . '/' . $user_id . '.png';
        if(file_exists($dir)) {
            return;
        }

        $qrCode = new \Endroid\QrCode\QrCode(SITE_URL . url('@index/user/register/invite_code/'.$invite_code));
        //设置前景色
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' =>0, 'a' => 0]);
        //设置背景色
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        //设置二维码大小
        $qrCode->setSize(230);
        $qrCode->setPadding(5);
        $qrCode->setLogoSize(40);
        $qrCode->setLabelFontSize(14);
        $qrCode->setLabelHalign(100);

        $dir = './upload/qrcode/user/'.$n;
        if(!file_exists($dir)) {
            mkdir($dir, 0777,true);
        }
        $qrCode->save($dir . '/' . $user_id . '.png');

        $qr = \Env::get('root_path').'public/upload/qrcode/user/' . $n . '/' . $user_id . '.png';  
        $bgimg1 = \Env::get('root_path').'public/public/img/userqr1.png';

        $image = \think\Image::open($bgimg1);  
        $image->water($qr,[255,743])->text($invite_code,\Env::get('root_path').'public/public/fz.TTF',22,'#ffffff',[(678-(24*strlen($user_id)))/2,685])->save(\Env::get('root_path').'public/upload/qrcode/user/'.$n.'/'.$user_id.'-1.png');
    }

    /**
     * 重置密码
     */
    public function reset_pwd($tel,$pwd,$type=1)
    {
        $data = [
            'tel'   => $tel,
            'pwd'   => $pwd,
        ];
        unset($this->rule['username']);
        $validate   = \Validate::make($this->rule,$this->info);//验证表单
        if (!$validate->check($data)) return ['code'=>1,'info'=>$validate->getError()];

        $user_id = Db::table($this->table)->where(['tel'=>$tel])->value('id');
        if(!$user_id){
            return ['code'=>1,'info'=>'用户不存在'];
        }
        
        $salt = mt_rand(0,99999);  
        if($type == 1){
            $data = [
                'pwd'       => sha1($pwd.$salt.config('pwd_str')),
                'salt'      => $salt,
            ];
        }elseif($type == 2){
            $data = [
                'pwd2'       => sha1($pwd.$salt.config('pwd_str')),
                'salt2'      => $salt,
            ];
        }

        $res = Db::table($this->table)->where('id',$user_id)->data($data)->update();

        if($res)
            return ['code'=>0,'info'=>lang('修改密码成功')];
        else
            return ['code'=>1,'info'=>lang('修改密码失败')];

    }
  public function reset_pwd_byid($id,$pwd,$type=1)
    {
       
        
        $user_id = Db::table($this->table)->where(['id'=>$id])->value('id');
        if(!$user_id){
            return ['code'=>1,'info'=>'用户不存在'];
        }
        
        $salt = mt_rand(0,99999);  
        if($type == 1){
            $data = [
                'pwd'       => sha1($pwd.$salt.config('pwd_str')),
                'salt'      => $salt,
            ];
        }elseif($type == 2){
            $data = [
                'pwd2'       => sha1($pwd.$salt.config('pwd_str')),
                'salt2'      => $salt,
            ];
        }

        $res = Db::table($this->table)->where('id',$user_id)->data($data)->update();

        if($res)
            return ['code'=>0,'info'=>lang('修改密码成功')];
        else
            return ['code'=>1,'info'=>lang('修改密码失败')];

    }
    //获取上级会员
    public function parent_user($uid,$num=1,$lv=1)
    {
        $pid = db($this->table)->where('id',$uid)->value('parent_id');
        $uinfo = db($this->table)->where('id',$pid)->find();
        if($uinfo){
            if($uinfo['parent_id']&&$num>1) $data = self::parent_user($uinfo['id'],$num-1,$lv+1);
            $data[] = ['id'=>$uinfo['id'],'pid'=>$uinfo['parent_id'],'lv'=>$lv,'status'=>$uinfo['status']];
            return $data;
        }
        return false;
    }

 public function can_vip_info($uid)
    {       
        //一代数据查询
            //$data['child_num'] = db('xy_users')->where('parent_id', $uid)->where('balance','gt',0)->count('id');
            
        //三代数据查询
            $data['child_num'] = $this->child_chongzhi_userin10dai($uid,1);//
            $data['balance'] = db('xy_users')->where('id', $uid)->value('balance');
            $data['level']=db('xy_users')->where('id', $uid)->value('level');
            return $data;
            
    }
      public function child_chongzhi_userin3dai($uid,$lv=1)
    {

        $data=[];
        $where = [];
            $ids1 = db('xy_users')->where('parent_id', $uid)->where('balance','gt',0)->field('id')->column('id');
            $ids1 ? $where[] =  ['parent_id','in',$ids1] : $where[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($where)->where('balance','gt',0)->field('id')->column('id');
            $ids2 ? $where[] = ['parent_id','in',$ids2] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->where('balance','gt',0)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$ids2, $data) : $data;

        return count($data);
    }
          public function child_chongzhi_userin10dai($uid,$lv=1)
    {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where5[] = ['parent_id','in',$ids5] : $where5[] = ['parent_id','in',[-1]];
            $ids6 = db('xy_users')->where($where5)->field('id')->column('id');
            $ids6 ? $where6[] = ['parent_id','in',$ids6] : $where6[] = ['parent_id','in',[-1]];
            $ids7 = db('xy_users')->where($where6)->field('id')->column('id');
            $ids7 ? $where7[] = ['parent_id','in',$ids7] : $where7[] = ['parent_id','in',[-1]];
            $ids8 = db('xy_users')->where($where7)->field('id')->column('id');
            $ids8 ? $where8[] = ['parent_id','in',$ids8] : $where8[] = ['parent_id','in',[-1]];
            $ids9 = db('xy_users')->where($where8)->field('id')->column('id');
            $ids9 ? $where[] = ['parent_id','in',$ids9] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $idsAll = array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$ids6,$ids7,$ids8,$ids9,$data);
            
            $mapchck[]=['id','in',$idsAll];
            $check = db('xy_users')->where($mapchck)->where('balance','gt',30)->column('balance');
        return count($check);
    }
    //获取下级会员
    public function child_user($uid,$num=1,$lv=1)
    {

        $data=[];
        $where = [];
        if ($num ==1) {
            $where[] = ['parent_id','=',$uid];
            $data = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

        }else if ($num == 2) {
            //二代
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $where[] = ['parent_id','in',$ids1] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$data) : $data;
        }else if ($num == 3) {
            //三代

            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where[] = ['parent_id','in',$ids2] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$ids2, $data) : $data;
        }else if ($num == 4) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where[] = ['parent_id','in',$ids3] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$ids2,$ids3, $data): $data;

        }else if ($num == 5) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where[] = ['parent_id','in',$ids4] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4, $data) :$data;
        }else if ($num == 6) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where[] = ['parent_id','in',$ids5] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$data) :$data;
        }else if ($num == 7) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where5[] = ['parent_id','in',$ids5] : $where5[] = ['parent_id','in',[-1]];
            $ids6 = db('xy_users')->where($where5)->field('id')->column('id');
            $ids6 ? $where[] = ['parent_id','in',$ids6] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$ids6,$data) :$data;
        }else if ($num == 8) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where5[] = ['parent_id','in',$ids5] : $where5[] = ['parent_id','in',[-1]];
            $ids6 = db('xy_users')->where($where5)->field('id')->column('id');
            $ids6 ? $where6[] = ['parent_id','in',$ids6] : $where6[] = ['parent_id','in',[-1]];
            $ids7 = db('xy_users')->where($where6)->field('id')->column('id');
            $ids7 ? $where[] = ['parent_id','in',$ids7] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$ids6,$ids7,$data) :$data;
        }else if ($num == 9) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where5[] = ['parent_id','in',$ids5] : $where5[] = ['parent_id','in',[-1]];
            $ids6 = db('xy_users')->where($where5)->field('id')->column('id');
            $ids6 ? $where6[] = ['parent_id','in',$ids6] : $where6[] = ['parent_id','in',[-1]];
            $ids7 = db('xy_users')->where($where6)->field('id')->column('id');
            $ids7 ? $where7[] = ['parent_id','in',$ids7] : $where7[] = ['parent_id','in',[-1]];
            $ids8 = db('xy_users')->where($where7)->field('id')->column('id');
            $ids8 ? $where[] = ['parent_id','in',$ids8] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$ids6,$ids7,$ids8,$data) :$data;
        }else if ($num == 10) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where4[] = ['parent_id','in',$ids4] : $where4[] = ['parent_id','in',[-1]];
            $ids5 = db('xy_users')->where($where4)->field('id')->column('id');
            $ids5 ? $where5[] = ['parent_id','in',$ids5] : $where5[] = ['parent_id','in',[-1]];
            $ids6 = db('xy_users')->where($where5)->field('id')->column('id');
            $ids6 ? $where6[] = ['parent_id','in',$ids6] : $where6[] = ['parent_id','in',[-1]];
            $ids7 = db('xy_users')->where($where6)->field('id')->column('id');
            $ids7 ? $where7[] = ['parent_id','in',$ids7] : $where7[] = ['parent_id','in',[-1]];
            $ids8 = db('xy_users')->where($where7)->field('id')->column('id');
            $ids8 ? $where8[] = ['parent_id','in',$ids8] : $where8[] = ['parent_id','in',[-1]];
            $ids9 = db('xy_users')->where($where8)->field('id')->column('id');
            $ids9 ? $where[] = ['parent_id','in',$ids9] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4,$ids5,$ids6,$ids7,$ids8,$ids9,$data) :$data;
        }


        return $data;
    }
 public function getUserField($id,$field){
        $res=db('xy_users')->find($id);
        return $res[$field]; 
    }
  function getLevel($level,$field){
      if(!$level) $level=0;
        $res=db('xy_level')->where('level',$level)->find();
        return $res[$field]; 
    }
     public function get_user_order_num($uid)
    {
        
        $where['uid']=$uid;
        $user_order = db('xy_convey')->where($where)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->field('id')->select();
        return count($user_order);
    }
   public function auto_check_up_vip($uid)
    {
        
            $levelinfo = db('xy_level')->field('level,num,auto_vip_xu_num')->order('level desc')->select();
        
            $can_vip_info = model('admin/Users')->can_vip_info($uid);
            $newlevel=0;
            foreach ($levelinfo as $key => $info) {
if($can_vip_info['child_num']>=$info['auto_vip_xu_num']&&$can_vip_info['balance']>=$info['num']){
                    $newlevel=$info['level'];
                    break;
                }
            }
            
            if($can_vip_info['level']<$newlevel){
                Db::name('xy_users')->where('id',$uid)->update(['level'=>$newlevel]); 
                Db::name('xy_message')->insert(['uid'=>$uid,'type'=>2,'title'=>lang('系统通知'),'content'=>lang('您已达到升级标准，已自动升级'),'addtime'=>time()]);
                return true;
            }else{
                return false;;
            }
    }
 public function auto_check_down_vip($uid)
    {
        return true;//取消自动降级
            $levelinfo = db('xy_level')->field('level,num,auto_vip_xu_num')->order('level desc')->select();
            $can_vip_info = model('admin/Users')->can_vip_info($uid);
            $newlevel=0;
            foreach ($levelinfo as $key => $info) {
                if($can_vip_info['balance']>=$info['num'] && $can_vip_info['child_num']>=$info['auto_vip_xu_num']){
                    $newlevel=$info['level'];
                    break;
                }
            }
            if($can_vip_info['level']>$newlevel){
                Db::name('xy_users')->where('id',$uid)->update(['level'=>$newlevel]); 
                Db::name('xy_message')->insert(['uid'=>$uid,'type'=>2,'title'=>lang('系统通知'),'content'=>lang('由于余额变化已降级'),'addtime'=>time()]);
            }
    }
     public function today_commission($uid)
    {
        $commission=db('xy_convey')->where('uid',$uid)->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('commission');
           return $commission;
    }
     public function get_grouping_user($gid)
    {
         $where = [];
         $user = session('admin_user');
        if($user['authorize']== 2 && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

            $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $where[] = ['id','in',$idsAll];
        }
            $userinfo = db('xy_users')->where($where)->where('pipei_grouping',$gid)->field('id,username,pipei_grouping')->select();
            foreach ($userinfo as $u) {
                echo "<a class=' layui-btn-xs'>".$u['username']."</a>";
            }
            
    }
      public function get_user_pipei_num_config($uid)
    {
        $uinfo = db($this->table)->where('id',$uid)->field('deal_min_num,deal_max_num,pipei_type,pipei_dan,pipei_grouping')->find();
        $where['uid']=$uid;
        $where['grouping_id']=$uinfo['pipei_grouping'];
        $user_order = db('xy_convey')->where($where)->field('id')->Distinct(true)->select();
        $data['num'] = count($user_order)+1;
        $pipei_dan=json_decode($uinfo['pipei_dan'], true);
        
        $data['deal_min_num']=$uinfo['deal_min_num']?$uinfo['deal_min_num']:0;
        $data['deal_max_num']=$uinfo['deal_max_num']?$uinfo['deal_min_num']:0;
        $data['pipei_type']=$uinfo['pipei_type'];
        if(!$uinfo['pipei_dan']){
            $uinfo['pipei_dan']='[{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"},{"pipei_dan":"0","pipei_min":"0","pipei_max":"0"}]';
        }
        
         $temp=json_decode($uinfo['pipei_dan'], true);
         
           $array=array();
            foreach($temp as $key => $value){
                if($value['pipei_dan']==0){
                    continue;
                }else{
                    $array[$key]=$value;
                }
            }
        $pipei_danss= array_column($array, 'pipei_dan');
        if(in_array($data['num'], $pipei_danss)){
            $pipei_min=array_column($array,'pipei_min','pipei_dan');
            $pipei_max=array_column($array,'pipei_max','pipei_dan');
            $data['pipei_max']=$pipei_max[$data['num']];
            $data['pipei_min']=$pipei_min[$data['num']];
        }else{
            $data['pipei_max']=0;
            $data['pipei_min']=0;
        }
        return $data;
    }
      public function get_user_bank($uid,$field)
    {
       
        $res = db('xy_bankinfo')->where('uid',$uid)->find();
        if($res){
                if($field=="bankname"){
                    $title="银行:";
                }else if($field=='cardnum'){
                    $title="卡号:";
                    
                }else if($field=='username'){
                    $title="用户名:";
                    
                }
                
            return $title.$res[$field];
        }else{
            return '';
        }
   
            
    }
         public function get_bank_info($id,$field)
    {
       
        $res = db('xy_bankinfo')->find($id);
        if($res){
            return $res[$field];
        }else{
            return '';
        }
   
            
    }

public function getChild($parent_id)
{
	static $arr=array();  //第一次调用时初始化
	//通过邀请码查询当前邀请码下级用户
	$data=Db::table($this->table)->where('parent_id',$parent_id)->field('id,parent_id')->select();
	//遍历获取信息，调用当前方法直至没有下级用户
	foreach ($data as $key => $value) {
		$arr[] = $value;
		$this->getChild($value['parent_id']);  //返回查询
	   }
	return $arr;
}
public function get_child_ids($pid){
    return $this->__get_ids($pid,'','id');
}
public function __get_ids($pid,$childids,$find_column = 'id'){
    if(!$pid || $pid<=0 || strlen($pid)<=0 || !in_array($find_column,array('id','parent_id'))) return 0;
    if(!$childids || strlen($childids)<=0) $childids = $pid;
    $column = ($find_column =='id'? "parent_id":"id");//id跟pid为互斥
    $ids = Db::table($this->table)->where("$column in($pid)")->field("$find_column")->select();
    $ids1=array_column($ids, 'id');
    
    $ids = implode(",",$ids1);
     
    //未找到,返回已经找到的
    if($ids<=0) return $childids;
    //添加到集合中
    $childids .= ','.$ids;
    //递归查找
    return $this->__get_ids($ids,$childids,$find_column);
}
public function disbursement($oid)
    {
            $r = db('xy_deposit')->where('id',$oid)->find();
            if($r){
                $bankinfo=db('xy_bankinfo')->where('uid',$r['uid'])->find();
                
                
                if(!$bankinfo['bankcode']) return ['code'=>0,'info'=>'bankcode为空'];
                if(!$bankinfo['username']) return ['code'=>0,'info'=>'bankusername为空'];
                if(!$bankinfo['cardnum']) return ['code'=>0,'info'=>'cardnum为空'];
                if(!$bankinfo['tel']) return ['code'=>0,'info'=>'tel为空'];
                if(!$bankinfo['ifsc']) return ['code'=>0,'info'=>'ifsc为空'];
                
            $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
            
            $mch_id = "100999002";
            $merchant_key ="4AZQ1RE1ZPNJTHO0ZG3T8M6MKV16II0U"; //代付秘钥
            
            $mch_transferId=$r['id'];//转账订单号
            $transfer_amount=$r['real_num'];//转账金额
            $apply_date=date('Y-m-d H:i:s');
            $bank_code=$bankinfo['bankcode'];//收款银行代码 
            $receive_name=$bankinfo['username'];//收款银行户名
            $receive_account=$bankinfo['cardnum'];//收款银行账号
            $receiver_telephone=$bankinfo['tel'];//收款人手机号码
            $back_url=$server_url.url('/index/api/disbursement_notify');//异步通知地址
            $remark=$bankinfo['ifsc'];//'CNRB0006625';
                $signStr = "";
                $signStr = $signStr."apply_date=".$apply_date."&";
                $signStr = $signStr."back_url=".$back_url."&";
                if($bank_code != ""){
                    $signStr = $signStr."bank_code=".$bank_code."&";
                }
                $signStr = $signStr."mch_id=".$mch_id."&";    
                $signStr = $signStr."mch_transferId=".$mch_transferId."&";   
                $signStr = $signStr."receive_account=".$receive_account."&"; 
                $signStr = $signStr."receive_name=".$receive_name."&";
                $signStr = $signStr."remark=".$remark."&";  
                $signStr = $signStr."transfer_amount=".$transfer_amount; 
                
            $reqUrl = "https://payment.dzxum.com/pay/transfer";
            
            $sign = $this->qeapaysign($signStr,$merchant_key);
            
            $postdata=array(
                'apply_date'=>$apply_date,
                'back_url'=>$back_url,
                'bank_code'=>$bank_code,
                'mch_id'=>$mch_id,
                'mch_transferId'=>$mch_transferId,
                'receive_account'=>$receive_account,
                'receive_name'=>$receive_name,
                'remark'=>$remark,
                'sign'=>$sign,
                'sign_type'=>"MD5",
                'transfer_amount'=>$transfer_amount
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
            
                            $json = json_decode($response,true);
                                    if($json['respCode']=='SUCCESS'){
                                        //更新支付url
                                            $res = Db::name('xy_deposit')->where('id',$oid)->update(['applyDate'=>$json['applyDate'],'tradeNo'=>$json['tradeNo']]);
                                        if($res){
                                            return ['code'=>0,'info'=>'操作成功,等待代付回调'];
                                        }else{
                                            return ['code'=>0,'info'=>'操作失败'];
                                        }
                                    }else{
                                        file_put_contents('qeaout_create.log', json_encode($response)."\r\n",FILE_APPEND);
                                        
                                            return ['code'=>0,'info'=>'创建代付失败'.$json['errorMsg']];
                                    } 
            }else{
                 return ['code'=>0,'info'=>'未查询到订单'];
            }
    }

public  function qeapaysign($signSource,$key) {
        if (!empty($key)) {
             $signSource = $signSource."&key=".$key;
        }
        return md5($signSource);
    }

}
