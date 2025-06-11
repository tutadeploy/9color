<?php
namespace app\admin\controller;

use library\Controller;
use think\Db;
use think\exception\HttpResponseException;
use think\facade\Request;

/**
 * 系统参数配置
 * Class Config
 * @package app\admin\controller
 */
class Config extends Controller
{
    /**
     * 默认数据模型
     * @var string
     */
    protected $table = 'SystemConfig';

    /**
     * 阿里云OSS上传点
     * @var array
     */
    protected $ossPoints = [
        'oss-cn-hangzhou.aliyuncs.com'    => '华东 1 杭州',
        'oss-cn-shanghai.aliyuncs.com'    => '华东 2 上海',
        'oss-cn-qingdao.aliyuncs.com'     => '华北 1 青岛',
        'oss-cn-beijing.aliyuncs.com'     => '华北 2 北京',
        'oss-cn-zhangjiakou.aliyuncs.com' => '华北 3 张家口',
        'oss-cn-huhehaote.aliyuncs.com'   => '华北 5 呼和浩特',
        'oss-cn-shenzhen.aliyuncs.com'    => '华南 1 深圳',
        'oss-cn-hongkong.aliyuncs.com'    => '香港 1',
        'oss-us-west-1.aliyuncs.com'      => '美国西部 1 硅谷',
        'oss-us-east-1.aliyuncs.com'      => '美国东部 1 弗吉尼亚',
        'oss-ap-southeast-1.aliyuncs.com' => '亚太东南 1 新加坡',
        'oss-ap-southeast-2.aliyuncs.com' => '亚太东南 2 悉尼',
        'oss-ap-southeast-3.aliyuncs.com' => '亚太东南 3 吉隆坡',
        'oss-ap-southeast-5.aliyuncs.com' => '亚太东南 5 雅加达',
        'oss-ap-northeast-1.aliyuncs.com' => '亚太东北 1 日本',
        'oss-ap-south-1.aliyuncs.com'     => '亚太南部 1 孟买',
        'oss-eu-central-1.aliyuncs.com'   => '欧洲中部 1 法兰克福',
        'oss-eu-west-1.aliyuncs.com'      => '英国 1 伦敦',
        'oss-me-east-1.aliyuncs.com'      => '中东东部 1 迪拜',
    ];

    /**
     * 系统参数配置
     * @auth true
     * @menu true
     */
    public function info()
    {
        $this->title = '系统参数配置';
        $this->info=[];
        $this->fetch();
    }

    /**
     * 清理数据
     * @auth true
     * @menu true
     */
    public function clear()
    {
        $this->applyCsrfToken();
        $ids = input('data', '');

        $ids = json_decode($ids);
        $map[] ='1=1';
        Db::table('system_log')->where('id', '>', 0)->delete();
        Db::table('xy_deal_elog')->where('id', '>', 0)->delete();
        Db::table('xy_reads')->where('id', '>', 1)->delete();
        Db::table('xy_reward_log')->where('id', '>', 1)->delete();
        Db::table('xy_verify_msg')->where('tel', '>', 1)->delete();
        if (in_array(1,$ids)) {
            Db::table('xy_users')->where('id', '>', 91)->delete();
        }
        if (in_array(2,$ids)) {
            Db::table('xy_convey')->where('uid', '>', 1)->delete();
        }
        if (in_array(3,$ids)) {
            Db::table('xy_balance_log')->where('uid', '>', 1)->delete();
        }
        if (in_array(4,$ids)) {
            Db::table('xy_recharge')->where('uid', '>', 1)->delete();
        }
        if (in_array(5,$ids)) {
            Db::table('xy_deposit')->where('uid', '>', 1)->delete();
        }
        if (in_array(6,$ids)) {
            Db::table('xy_bankinfo')->where('uid', '>', 1)->delete();
        }
        if (in_array(7,$ids)) {
            Db::table('xy_member_address')->where('uid', '>', 1)->delete();
        }
        if (in_array(8,$ids)) {
            Db::table('xy_lixibao')->where('uid', '>', 1)->delete();
        }

        $this->success('清理成功！');
    }

    /**
     * 修改系统能数配置
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function config()
    {
        $this->applyCsrfToken();
        if (Request::isGet()) {
            $this->fetch('system-config');
        }
        foreach (Request::post() as $key => $value) {
            sysconf($key, $value);
        }
        $this->success('系统参数配置成功！');
    }

    /**
     * 文件存储引擎
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function file()
    {
        $this->applyCsrfToken();
        if (Request::isGet()) {
            $this->type = input('type', 'local');
            $this->fetch("storage-{$this->type}");
        }
        $post = Request::post();
        if (isset($post['storage_type']) && isset($post['storage_local_exts'])) {
            $exts = array_unique(explode(',', strtolower($post['storage_local_exts'])));
            sort($exts);
            if (in_array('php', $exts)) $this->error('禁止上传可执行文件到本地服务器！');
            $post['storage_local_exts'] = join(',', $exts);
        }
        foreach ($post as $key => $value) sysconf($key, $value);
        if (isset($post['storage_type']) && $post['storage_type'] === 'oss') {
            try {
                $local = sysconf('storage_oss_domain');
                $bucket = $this->request->post('storage_oss_bucket');
                $domain = \library\File::instance('oss')->setBucket($bucket);
                if (empty($local) || stripos($local, '.aliyuncs.com') !== false) {
                    sysconf('storage_oss_domain', $domain);
                }
                $this->success('阿里云OSS存储配置成功！');
            } catch (HttpResponseException $exception) {
                throw $exception;
            } catch (\Exception $e) {
                $this->error("阿里云OSS存储配置失效，{$e->getMessage()}");
            }
        } else {
            $this->success('文件存储配置成功！');
        }
    }
    
        public function indexconfig()
    {
        $this->title = '交易控制';
        if(request()->isPost()){

            setconfig(['indexscrollnew'],[input('post.indexscrollnew')]);
            setconfig(['enindexscrollnew'],[input('post.enindexscrollnew')]);
            setconfig(['krindexscrollnew'],[input('post.krindexscrollnew')]);
            setconfig(['jpindexscrollnew'],[input('post.jpindexscrollnew')]);
            //var_dump(input('post.bank'));die;
            //
            $fileurl = APP_PATH . "../config/indexscrollnew.txt";
            $enfileurl = APP_PATH . "../config/enindexscrollnew.txt";
            $krfileurl = APP_PATH . "../config/krindexscrollnew.txt";
            $jpfileurl = APP_PATH . "../config/jpindexscrollnew.txt";
            file_put_contents($fileurl, input('post.indexscrollnew')); // 写入配置文件
            file_put_contents($enfileurl, input('post.enindexscrollnew')); // 写入配置文件
            file_put_contents($krfileurl, input('post.krindexscrollnew')); // 写入配置文件
            file_put_contents($jpfileurl, input('post.jpindexscrollnew')); // 写入配置文件


            return $this->success('操作成功!');
        }

       // var_dump(config('master_name'));die;
        $fileurl = APP_PATH . "../config/indexscrollnew.txt";
        $this->indexscrollnew = file_get_contents($fileurl); // 读取配置文件
        $enfileurl = APP_PATH . "../config/enindexscrollnew.txt";
        $this->enindexscrollnew = file_get_contents($enfileurl); // 读取配置文件
        $krfileurl = APP_PATH . "../config/krindexscrollnew.txt";
        $this->krindexscrollnew = file_get_contents($krfileurl); // 读取配置文件
        $jpfileurl = APP_PATH . "../config/jpindexscrollnew.txt";
        $this->jpindexscrollnew = file_get_contents($jpfileurl); // 读取配置文件

        return $this->fetch();
    }

}
