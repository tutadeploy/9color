<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | www.soku.cc搜库资源网
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// |

// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Db;

/**
 * 支付方式管理
 * Class Pay
 * @package app\admin\controller
 */
class Pay extends Controller
{

    /**
     * 指定当前数据表
     * @var string
     */
    protected $table = 'xy_pay';

    /**
     * 支付方式
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->title = '支付方式';

        $query = $this->_query($this->table)->alias('u');
        $where = [];
        if(input('tel/s',''))$where[] = ['u.tel','like','%' . input('tel/s','') . '%'];
        if(input('username/s',''))$where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['u.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $query->field('*')
            ->where($where)
            ->order('u.sort desc')
            ->page();
    }



    /**
     * 编辑支付
     * @auth true
     * @menu true
     */
    public function edit()
    {
        $id = input('get.id',0);

        if(request()->isPost()){
            $id = input('post.id/d',0);
            $sort = input('post.sort/d',0);
            $tuijian = input('post.tuijian/d',0);
            $address = input('post.address/s','');
            
            $name = input('post.name/s','');
            $min = input('post.min/f',0);
            $max = input('post.max/f','');
            $charge = input('post.charge/f',0);
            
            
            $ewm = input('post.ewm/s','');
            $token = input('__token__');

            $data =array(
                'name'=>$name,
                'sort'=>$sort,
                'tuijian'=>$tuijian,
                'min'=>$min,
                'max'=>$max,
                'charge'=>$charge,
                'ewm'=>$ewm,
                'address'=>$address,
            );
            $res = Db::table($this->table)->where('id',$id)->update($data);
            if(!$res){
                return $this->error($res['info']);
            }
            
                sysoplog('系统管理', '管理员:'.session('admin_user.username').'编辑支付地址:['.$address.']');
            $this->success('编辑成功',admin_url('admin/pay/index'));
        }
        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);

        //var_dump($this->info);die;
        return $this->fetch();
    }


    /**
     * 禁用系统权限
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        //$this->applyCsrfToken();
        $this->_save($this->table, ['status' => '0']);
    }

    /**
     * 启用系统权限
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        //$this->applyCsrfToken();
        $this->_save($this->table, ['status' => '1']);
    }


}