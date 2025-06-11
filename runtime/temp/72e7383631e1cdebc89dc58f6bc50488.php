<?php /*a:3:{s:60:"D:\project\sd\application\admin\view\deal\user_recharge.html";i:1637718588;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;s:67:"D:\project\sd\application\admin\view\deal\user_recharge_search.html";i:1628678658;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">订单号</label><div class="layui-input-inline"><input name="oid" value="<?php echo htmlentities((app('request')->get('oid') ?: '')); ?>" placeholder="请输入订单号" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">手机号码</label><div class="layui-input-inline"><input name="tel" value="<?php echo htmlentities((app('request')->get('tel') ?: '')); ?>" placeholder="请输入手机号码" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">添加时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择添加时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">审核状态</label><div class="layui-input-inline"><select name="status" id="selectList"><option value="">所有状态</option><option value="1">待处理</option><option value="2">充值成功</option><option value="3">充值失败</option></select></div></div><script>    var test = <?php echo htmlentities((app('request')->get('status') ?: '0')); ?>;
    $("#selectList").find("option[value="+test+"]").prop("selected",true);
        </script><div class="layui-form-item layui-inline"><label class="layui-form-label">分类</label><div class="layui-input-inline"><select name="type" id="typeselectList"><option value="">所有状态</option><option value="1">会员升级</option><option value="2">会员充值</option></select></div></div><script>    var test = <?php echo htmlentities((app('request')->get('type') ?: '0')); ?>;
    $("#typeselectList").find("option[value="+test+"]").prop("selected",true);
        </script><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><script>form.render()</script><fieldset><legend>数据统计</legend><font color="red">充值总额：<?php echo htmlentities($user_recharge); ?>元</font></fieldset><table class="layui-table margin-top-15" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='text-left nowrap'>订单号</th><th class='text-left nowrap'>真实姓名</th><th class='text-left nowrap'>手机号</th><th class='text-left nowrap'>到账/手续费</th><th class='text-left nowrap'>打款凭证</th><th class='text-left nowrap'>支付方式</th><th class='text-left nowrap'>类型</th><th class='text-left nowrap'>处理时间</th><th class='text-left nowrap'>操作</th><th class='text-left nowrap'>　</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['username']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['tel']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['num']); ?></br><?php echo htmlentities($vo['charge']); ?></td><td class='text-left nowrap'><?php if($vo['pic']): ?><a data-dbclick data-title="查看图片" data-modal='<?php echo url("admin/users/picinfo"); ?>?pic=<?php echo htmlentities($vo['pic']); ?>'><img src="<?php echo htmlentities($vo['pic']); ?>" style="width:150px;height:100px;"></a><?php else: if($vo['orderNo']): ?>                订单号:<?php echo htmlentities($vo['orderNo']); ?><hr><?php endif; if($vo['orderDate']): ?>                下单时间:<?php echo htmlentities($vo['orderDate']); ?><hr>                回调时间:<?php echo htmlentities($vo['notifyDate']); ?><?php endif; ?><?php endif; ?></td><td class='text-left nowrap'><?php echo htmlentities((isset($vo['pay_name']) && ($vo['pay_name'] !== '')?$vo['pay_name']:"银行卡充值")); ?></td><td class='text-left nowrap'><?php if($vo['is_vip']): ?><button class="layui-btn layui-btn-xs layui-btn layui-btn-warm">会员升级</button><?php else: ?><button class="layui-btn layui-btn-xs layui-btn layui-btn-danger">会员充值</button><?php endif; ?></td><td class='text-left nowrap'><?php echo htmlentities(format_datetime($vo['addtime'])); ?><p><?php echo htmlentities(format_datetime($vo['endtime'])); ?></td><td class='text-left nowrap'><?php switch($vo['status']): case "0": ?>待付款<?php break; case "1": if(auth("edit_recharge")): ?><a data-csrf="<?php echo systoken('admin/deal/edit_recharge'); ?>" class="layui-btn layui-btn-xs layui-btn" data-action="<?php echo url('edit_recharge',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" >通过</a><a data-csrf="<?php echo systoken('admin/deal/edit_recharge'); ?>" class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_recharge',['status'=>3,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#3" >驳回</a><a data-dbclick class="layui-btn layui-btn-xs " data-title="赠送彩金" data-modal='<?php echo url("admin/deal/add_user_cj"); ?>?id=<?php echo htmlentities($vo['uid']); ?>'>赠送彩金</a><?php endif; break; case "2": ?>审核通过<?php break; case "3": ?>审核驳回<?php break; ?><?php endswitch; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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