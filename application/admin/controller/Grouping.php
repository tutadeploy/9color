<?php

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Db;

/**
 * 分组配置
 * Class Users
 * @package app\admin\controller
 */
class Grouping extends Controller
{
    
    protected $table = 'xy_grouping';

    /**
     * 分组配置列表
     * @auth true
     * @menu true
     */
    public function index()
    {
        $this->title = '分组配置';
        
        $where =[];
        $type=input('type/d','');
        if($type==1){
            $where['type'] = 1;
        }else if($type==2){
            $where['type'] = 0;
        }
        $this->type=$type;
        
        $this->_query('xy_grouping')->where($where)->page();
    }
    
    /**
    * 编辑分组
    * @auth true  # 表示需要验证权限
    * @menu true  # 在菜单编辑的节点可选项
    */
      public function edit_grouping()
    {
        $id = input('get.id',0);
        if(request()->isPost()){
            $this->applyCsrfToken();
            
            $groupid=input('post.id/d',0);
            $pipei_dan_run=input('post.pipei_dan_run');
           
            $datapipei_min=input('post.pipei_min');
            $datapipei_max=input('post.pipei_max');
             $array=array();
            
            foreach ($pipei_dan_run as $k => $v) {
                $array[$k]['pipei_dan_num']=$k+1;
                $array[$k]['pipei_dan_run']=$v;
                $array[$k]['pipei_min']=$datapipei_min[$k];
                $array[$k]['pipei_max']=$datapipei_max[$k];
            }
            $data['content']=json_encode($array);
            $data['title']=input('post.title/s','');
            $data['type']=input('post.type/d',0);
            if($groupid){
            $res = Db::table($this->table)->where('id',$groupid)->update($data);
            }else{
               $res = Db::table($this->table)->insert($data);
            }
                if($res){
                        return $this->success('编辑成功');
                }else{
                   return $this->error('编辑失败');
                }
            
        }
        
        if($id){
        $this->info = Db::table($this->table)->find($id);
        $this->pipei_dan = json_decode($this->info['content'],true);
            
        }else{
            $this->info['title']="";
            $this->info['type']=0;
        }
        return $this->fetch();
    }
    public function edit_grouping_status()
    {
    
    }
    /**
* 分组用户编辑
* @auth true  # 表示需要验证权限
* @menu true  # 在菜单编辑的节点可选项
*/
     public function add_user()
    {
        $id = input('get.id',0);
         if(!$id) return $this->error('参数有误');
         
        $this->info = Db::table($this->table)->find($id);
        $this->pipei_dan = json_decode($this->info['content'],true);
        
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
        $this->daili = 1;
            
        }else{
            
        $this->daili = 0;
        }
        $this->_query('xy_users')->where($where)
            ->field('id,username,pipei_grouping')
            ->order('addtime desc')
            ->page();
        return $this->fetch();
    }
       public function do_add_user()
    {
        $ids = input('data', '');
        $grouping_id = input('id', '');

        $ids = json_decode($ids);
        
         foreach ($ids as $id) {
                $t = Db::name('xy_users')->where('id',$id)->find();
                if ($t['status'] == 1) {
                    Db::name('xy_users')->where('id',$id)->update(['pipei_type'=>2,'pipei_grouping'=>$grouping_id]);
                }
            }
        
        $this->success('添加成功');
    }
      /**
     * 删除分组
     * @auth true
     * @menu true
     */
    public function delete_grouping()
    {
        $this->applyCsrfToken();
        $id = input('post.id/d',0);
        $res = Db::table($this->table)->where('id',$id)->delete();
        if($res)
            $this->success('删除成功!');
        else
            $this->error('删除失败!');
    }
    
    

}