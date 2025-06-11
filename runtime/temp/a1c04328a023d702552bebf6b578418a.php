<?php /*a:2:{s:51:"D:\project\sd\application\admin\view\pay\index.html";i:1629187978;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><!--<a class="layui-btn layui-btn layui-btn" data-open="<?php echo url('add_banner',['id'=>0]); ?>" data-value="id#0" style='background:green;'>新增</a>--><table class="layui-table margin-top-15" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='text-left nowrap'>排序</th><th class='text-left nowrap'>名称</th><th class='text-left nowrap'>图片</th><th class='text-left nowrap'>标识</th><th class='text-left nowrap'>最小金额</th><th class='text-left nowrap'>最大金额</th><th class='text-left nowrap'>手续费(按次)</th><th class='text-left nowrap'>状态</th><?php if(auth("edit_home_msg")): ?><th class='text-left nowrap'>操作</th><?php endif; ?></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='text-left nowrap'><?php echo htmlentities($vo['sort']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['name']); ?></td><td class='text-left nowrap'><img src="<?php echo htmlentities($vo['ico']); ?>" alt="" width="100"></td><td class='text-left nowrap'><?php echo htmlentities($vo['name2']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['min']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['max']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['charge']); ?></td><td class='text-left nowrap'><?php if($vo['status'] == '0'): ?><span class="color-red">已禁用</span><?php else: ?><span class="color-green">使用中</span><?php endif; ?></td><td class='text-left nowrap'><?php if(auth("edit_home_msg")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑" data-open='<?php echo url("admin/pay/edit"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>编 辑</a><?php if($vo['status'] == 1): ?><a class="layui-btn layui-btn-warm layui-btn-xs" data-action="<?php echo url('forbid'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#0" data-csrf="<?php echo systoken('admin/pay/forbid'); ?>">禁 用</a><?php elseif($vo['status'] == 0): ?><a class="layui-btn layui-btn-warm layui-btn-xs" data-action="<?php echo url('resume'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" data-csrf="<?php echo systoken('admin/apy/resume'); ?>">启 用</a><?php endif; ?><!--<a class="layui-btn layui-btn-xs layui-btn" onClick="del_message(<?php echo htmlentities($vo['id']); ?>)" style='background:red;'>删除</a>--><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><script>
    function del_message(id){
        layer.confirm("确认要删除吗，删除后不能恢复",{ title: "删除确认" },function(index){
            $.ajax({
                type: 'POST',
                url: "<?php echo url('del_banner'); ?>",
                data: {
                    'id': id,
                    '_csrf_': "<?php echo systoken('admin/help/del_banner'); ?>"
                },
                success:function (res) {
                    layer.msg(res.info,{time:2500});
                    location.reload();
                }
            });
        },function(){});
    }
</script></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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