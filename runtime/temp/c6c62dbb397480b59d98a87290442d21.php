<?php /*a:3:{s:55:"D:\project\sd\application\admin\view\users\cs_list.html";i:1582016540;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;s:57:"D:\project\sd\application\admin\view\users\cs_search.html";i:1575918886;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"><?php if(auth("add_cs")): ?><button data-modal='<?php echo url("add_cs"); ?>' data-title="添加客服" class='layui-btn'>添加客服</button><?php endif; ?></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">手机号码</label><div class="layui-input-inline"><input name="tel" value="<?php echo htmlentities((app('request')->get('tel') ?: '')); ?>" placeholder="请输入手机号码" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">注册时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择注册时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><script>form.render()</script><table class="layui-table margin-top-15" lay-filter="tab" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th lay-data="{field:'id'}" class='text-left nowrap'>ID</th><th lay-data="{field:'username'}" class='text-left nowrap'>用户名</th><th lay-data="{field:'tel'}" class='text-left nowrap'>手机号</th><th lay-data="{field:'qq'}" class='text-left nowrap'>QQ</th><th lay-data="{field:'url'}" class='text-left nowrap'>链接</th><th lay-data="{field:'wechat'}" class='text-left nowrap'>微信</th><th lay-data="{field:'qr_code'}" class='text-left nowrap'>微信二维码</th><th lay-data="{field:'addtime',width:200}" class='text-left nowrap'>添加时间</th><th lay-data="{field:'btime',width:200}" class='text-left nowrap'>上班时间</th><th lay-data="{field:'etime',width:200}" class='text-left nowrap'>下班时间</th><th lay-data="{field:'edit',width:130,fixed: 'right'}" class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['username']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['tel']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['qq']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['url']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['wechat']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['qr_code']); ?></td><td class='text-left nowrap'><?php echo htmlentities(format_datetime($vo['addtime'])); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['btime']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['etime']); ?></td><td class='text-left nowrap'><?php if(auth("edit_cs")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/edit_cs"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>编 辑</a><?php endif; if(($vo['status'] == 1) and auth("edit_cs_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/users/edit_cs_status'); ?>" data-action="<?php echo url('edit_cs_status',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" style='background:red;'>禁用</a><?php elseif(($vo['status'] == 2) and auth("edit_cs_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/users/edit_cs_status'); ?>" data-action="<?php echo url('edit_cs_status',['status'=>1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" style='background:green;'>启用</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><script>
        var table = layui.table;
        //转换静态表格
        var limit = Number('<?php echo htmlentities(app('request')->get('limit')); ?>');
        if(limit==0) limit=20;
        table.init('tab', {
                cellMinWidth:120,
                skin: 'line,row',
                size: 'lg',
                limit: limit
            }); 
    </script><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
//        var element = layui.element;
//
//        element.tabAdd('demo', {
//            title: '选项卡的标题'
//            ,content: '选项卡的内容' //支持传入html
//            ,id: '选项卡标题的lay-id属性值'
//        });
//
//        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
//        var layid = location.hash.replace(/^#test1=/, '');
//        element.tabChange('test1', layid); //假设当前地址为：http://a.com#test1=222，那么选项卡会自动切换到“发送消息”这一项
//
//        //监听Tab切换，以改变地址hash值
//        element.on('tab(test1)', function(){
//            location.hash = ''+ this.getAttribute('lay-id');
//        });
//    });
    seeNum();
   function seeNum(){
        var seeNumUrl = "<?php echo url('deal/seeNum'); ?>";
        var rechargeState = 1;//充值声音开关，1开/0关
        $.ajax({
            type : "POST",
            url : seeNumUrl,
            data: {rechargeState:rechargeState},
            dataType : "json",
            success : function(result){
            	console.log(result);
                if(result.code=="000"){
                	
                    $("#ifr").attr("src",result.url);
                    if(result.urlneworder){
                    $("#ifrneworder").attr("src",result.urlneworder);
                    }
               //     alert(result.msg);
                }else{
                    $("#ifr").attr("src","");
                    $("#ifrneworder").attr("src","");
                }
            },
            error:function(){
                //alert();
            }
        });
    }
    setInterval(seeNum,15000);
</script>