<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 

// +----------------------------------------------------------------------

use app\admin\service\CaptchaService;
use app\admin\service\NodeService;
use app\admin\service\OplogService;
use library\File;
use think\Console;
use think\Db;
use think\facade\Middleware;
use think\facade\Route;
use think\Request;

if (!function_exists('auth')) {
    /**
     * 节点访问权限检查
     * @param string $node 需要检查的节点
     * @return boolean
     * @throws ReflectionException
     */
    function auth($node)
    {
        return NodeService::checkAuth($node);
    }
}

if (!function_exists('sysdata')) {
    /**
     * JSON 数据读取与存储
     * @param string $name 数据名称
     * @param array|null $value 数据内容
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function sysdata($name, array $value = null)
    {
        if (is_null($value)) {
            $data = json_decode(Db::name('SystemData')->where(['name' => $name])->value('value'), true);
            return empty($data) ? [] : $data;
        } else {
            return data_save('SystemData', ['name' => $name, 'value' => json_encode($value, JSON_UNESCAPED_UNICODE)], 'name');
        }
    }
}

if (!function_exists('sysoplog')) {
    /**
     * 写入系统日志
     * @param string $action 日志行为
     * @param string $content 日志内容
     * @return boolean
     */
    function sysoplog($action, $content)
    {
        return OplogService::write($action, $content);
    }
}

if (!function_exists('sysqueue')) {
    /**
     * 创建异步处理任务
     * @param string $title 任务名称
     * @param string $loade 执行内容
     * @param integer $later 延时执行时间
     * @param array $data 任务附加数据
     * @param integer $double 任务多开
     * @return boolean
     * @throws \think\Exception
     */
    function sysqueue($title, $loade, $later = 0, $data = [], $double = 1)
    {
        $map = [['title', 'eq', $title], ['status', 'in', [1, 2]]];
        if (empty($double) && Db::name('SystemQueue')->where($map)->count() > 0) {
            throw new \think\Exception('该任务已经创建，请耐心等待处理完成！');
        }
        $result = Db::name('SystemQueue')->insert([
            'title'  => $title, 'preload' => $loade,
            'data'   => json_encode($data, JSON_UNESCAPED_UNICODE),
            'time'   => $later > 0 ? time() + $later : time(),
            'double' => intval($double), 'create_at' => date('Y-m-d H:i:s'),
        ]);
        return $result !== false;
    }
}

if (!function_exists('local_image')) {
    /**
     * 下载远程文件到本地
     * @param string $url 远程图片地址
     * @param boolean $force 是否强制重新下载
     * @param integer $expire 强制本地存储时间
     * @return string
     */
    function local_image($url, $force = false, $expire = 0)
    {
        $result = File::down($url, $force, $expire);
        if (isset($result['url'])) {
            return $result['url'];
        } else {
            return $url;
        }
    }
}

if (!function_exists('base64_image')) {
    /**
     * base64 图片上传接口
     * @param string $content 图片base64内容
     * @param string $dirname 图片存储目录
     * @return string
     */
    function base64_image($content, $dirname = 'base64/')
    {
        try {
            if (preg_match('|^data:image/(.*?);base64,|i', $content)) {
                list($ext, $base) = explode('|||', preg_replace('|^data:image/(.*?);base64,|i', '$1|||', $content));
                $info = File::save($dirname . md5($base) . '.' . (empty($ext) ? 'tmp' : $ext), base64_decode($base));
                return $info['url'];
            } else {
                return $content;
            }
        } catch (\Exception $e) {
            return $content;
        }
    }
}

// 访问权限检查中间键
Middleware::add(function (Request $request, \Closure $next) {
    if (NodeService::forceAuth()) {
        return $next($request);
    } elseif (NodeService::islogin()) {
        return json(['code' => 0, 'msg' => '抱歉，没有访问该操作的权限！']);
    } else {
        return json(['code' => 0, 'msg' => '抱歉，需要登录获取访问权限！', 'url' => url('@sgcpj/login')]);
    }
});

// 自定义admin模块URL生成函数
if (!function_exists('admin_url')) {
    /**
     * 生成admin模块的URL，自动转换为sgcpj路径
     * @param string $url URL地址
     * @param array $vars URL参数
     * @param string $suffix URL后缀
     * @param string $domain 域名
     * @return string
     */
    function admin_url($url = '', $vars = [], $suffix = true, $domain = false)
    {
        // 调用原生url函数生成URL
        $generatedUrl = url($url, $vars, $suffix, $domain);
        
        // 如果生成的URL以/admin/开头，替换为/sgcpj/
        if (strpos($generatedUrl, '/admin/') === 0) {
            $generatedUrl = str_replace('/admin/', '/sgcpj/', $generatedUrl);
        }
        
        return $generatedUrl;
    }
}

// 重写url函数，在admin模块中自动使用sgcpj路径
if (!function_exists('admin_url_helper')) {
    /**
     * Admin模块专用的URL助手函数
     * 自动将admin路径转换为sgcpj路径
     */
    function admin_url_helper($url = '', $vars = [], $suffix = true, $domain = false)
    {
        // 获取当前模块
        $currentModule = \think\facade\Request::module();
        
        // 如果当前在admin模块，使用admin_url函数
        if ($currentModule === 'admin') {
            return admin_url($url, $vars, $suffix, $domain);
    }
        
        // 否则使用原生url函数
        return \think\facade\Url::build($url, $vars, $suffix, $domain);
    }
}

// 在admin模块中重新定义url函数
if (\think\facade\Request::module() === 'admin' && !function_exists('url_admin_override')) {
    /**
     * 在admin模块中重写url函数
     */
    function url_admin_override($url = '', $vars = [], $suffix = true, $domain = false)
    {
        return admin_url($url, $vars, $suffix, $domain);
    }
    
    // 注册为url函数的别名（如果可能的话）
    if (!function_exists('url')) {
        function url($url = '', $vars = [], $suffix = true, $domain = false) {
            return url_admin_override($url, $vars, $suffix, $domain);
        }
    }
}

// 注册系统服务指令
Console::addDefaultCommands([
    'app\admin\queue\task\Stop',
    'app\admin\queue\task\Work',
    'app\admin\queue\task\Start',
    'app\admin\queue\task\State',
    'app\admin\queue\task\Query',
    'app\admin\queue\task\Listen',
]);

// ThinkAdmin 图形验证码
Route::get('/think/admin/captcha', function () {
    $image = new CaptchaService();
    return json(['code' => '1', 'info' => '生成验证码', 'data' => [
        'uniqid' => $image->getUniqid(), 'image' => $image->getData()
    ]]);
});