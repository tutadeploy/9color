<?php /*a:2:{s:56:"D:\project\sd\application\admin\view\grouping\index.html";i:1635057070;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"><?php if(auth("edit_grouping")): ?><button data-modal='<?php echo url("edit_grouping"); ?>' data-title="添加分组" class='layui-btn'>添加分组</button><?php endif; ?></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">分类</label><div class="layui-input-inline"><select name="type" id="selectList"><option value="" >所有</option><option value="1" <?php if($type==1): ?>selected<?php endif; ?>>绝对值</option><option value="2" <?php if($type==2): ?>selected<?php endif; ?>>百分比</option></select></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form><script>
        var test = "<?php echo htmlentities((app('request')->get('cid') ?: '0')); ?>";
        $("#selectList").find("option[value="+test+"]").prop("selected",true);

        form.render()
    </script></fieldset><table class="layui-table margin-top-15" lay-filter="tab" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th lay-data="{field:'id'}" class='text-left nowrap'>ID</th><th lay-data="{field:'title'}" class='text-left nowrap'>名称</th><th lay-data="{field:'type'}" class='text-left nowrap'>类型</th><th lay-data="{field:'add'}" class='text-left nowrap'>分组用户</th><th lay-data="{field:'content'}" class='text-left nowrap'>配置</th><th lay-data="{field:'edit',width:130,fixed: 'right'}" class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['title']); ?></td><td class='text-left nowrap'><?php if($vo['type']==1): ?><a class="layui-btn layui-btn-xs">绝对值</a><?php else: ?><a class="layui-btn layui-btn-xs" style="background:red;">百分比</a><?php endif; ?></td><td class='text-left nowrap'><a data-dbclick class="layui-btn layui-btn-xs" data-title="【<?php echo htmlentities($vo['title']); ?>】 编辑用户" data-modal='<?php echo url("admin/grouping/add_user"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>编 辑</a><?php echo model('admin/Users')->get_grouping_user($vo['id']);; ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['content']); ?></td><td class='text-left nowrap'><?php if(auth("edit_grouping")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/grouping/edit_grouping"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>编 辑</a><?php endif; if(($vo['status'] == 1) and auth("edit_grouping_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/grouping/edit_grouping_status'); ?>" data-action="<?php echo url('edit_grouping_status',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" style='background:red;'>禁用</a><?php elseif(($vo['status'] == 2) and auth("edit_grouping_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/grouping/edit_grouping_status'); ?>" data-action="<?php echo url('edit_grouping_status',['status'=>1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" style='background:green;'>启用</a><?php endif; ?><a class="layui-btn layui-btn-xs layui-btn" onClick="del_group(<?php echo htmlentities($vo['id']); ?>)" style='background:red;'>删 除</a></td></tr><?php endforeach; ?></tbody></table><script>
        function del_group(id){
            layer.confirm("确认要删除吗，删除后不能恢复",{ title: "删除确认" },function(index){
                $.ajax({
                    type: 'POST',
                    url: "<?php echo url('delete_grouping'); ?>",
                    data: {
                        'id': id,
                        '_csrf_': "<?php echo systoken('admin/grouping/delete_grouping'); ?>"
                    },
                    success:function (res) {
                        layer.msg(res.info,{time:2500});
                        location.reload();
                    }
                });
            },function(){});
        }
    </script><script>
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