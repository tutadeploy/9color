<?php /*a:2:{s:53:"D:\project\sd\application\admin\view\users\caiwu.html";i:1582454656;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span>
        当前用户: <?php echo htmlentities($uinfo['username']); ?>(<?php echo htmlentities($uinfo['tel']); ?>)
        <div class="pull-right"></div></div><?php if(auth("open")): ?><div class="layui-form-item layui-inline" style="margin-right: 10px"><a class="layui-btn layui-btn-sm layui-btn-normal" data-open="<?php echo url('index'); ?>" data-reload="true"  data-csrf="<?php echo systoken('index'); ?>">返回会员列表</a></div><?php endif; ?><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">类型</label><div class="layui-input-inline"><select name="type" id="selectList"><option value="">所有状态</option><option value="-1">系统</option><option value="1">充值</option><option value="2">交易</option><option value="3">返佣</option><option value="4">强制交易</option><option value="5">推广返佣</option><option value="6">下级交易返佣</option><option value="7">利息宝</option></select></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">发生时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择注册时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><div class="layui-tab"><ul class="layui-tab-title"><li class="layui-this">Ta的财务记录</li></ul><div class="layui-tab-content"><div class="layui-tab-item layui-show"><table id="demo0" lay-filter="test1"></table></div></div></div><script>
        $(function () {
            var table = layui.table;

            //第一个实例
            table.render({
                elem: '#demo0'
                ,where: {
                    iasjax:1,
                    level:1,
                    addtime:"<?php echo htmlentities(app('request')->get('addtime')); ?>",
                    type:"<?php echo htmlentities(app('request')->get('type')); ?>",
                    id:<?php echo htmlentities($uid); ?>
                }
                ,height: 512
                ,totalRow: true
                ,url: '/admin/users/caiwu' //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', width:100, sort: true, fixed: 'left'}
                    ,{field: 'tel', title: '账号',totalRowText: '合计' }
                    ,{field: 'oid', title: '订单编号',sort: true}
                    ,{field: 'num', title: '金额',sort: true,totalRow: true}
                    ,{field: 'type', title: '类型'}
                    ,{field: 'status', title: '状态' }
                    ,{field: 'addtime', title: '发生时间',width:150 }
                ]]
            });

        })
    </script><script>
        var test = "<?php echo htmlentities((app('request')->get('type') ?: '0')); ?>";
        $("#selectList").find("option[value="+test+"]").prop("selected",true);
        form.render()
    </script></div></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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