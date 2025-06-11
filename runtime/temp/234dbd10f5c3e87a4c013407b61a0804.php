<?php /*a:3:{s:57:"D:\project\sd\application\admin\view\deal\order_list.html";i:1637640454;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;s:64:"D:\project\sd\application\admin\view\deal\order_list_search.html";i:1575918886;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><style>::-webkit-scrollbar{  height: 8px; } 
</style><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">订单号</label><div class="layui-input-inline"><input name="oid" value="<?php echo htmlentities((app('request')->get('oid') ?: '')); ?>" placeholder="请输入订单号" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">下单时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择添加时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button></div></form></fieldset><script>form.render()</script><?php
        $level = Db::table('xy_level')->field('name,level')->select();
        $levelname = array_column($level, 'name','level');
    ?><fieldset><legend>数据统计</legend><font color="red">交易总额：<?php echo htmlentities($order_sum); ?>元</font> &nbsp;&nbsp;&nbsp;<font color="red">佣金总额：<?php echo htmlentities($commission_sum); ?>元</font></fieldset><table class="layui-table margin-top-15" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='text-left nowrap'>订单号</th><th class='text-left nowrap'>真实姓名</th><th class='text-left nowrap'>会员等级</th><th class='text-left nowrap'>余额</th><th class='text-left nowrap'>今日单数</th><th class='text-left nowrap'>单价数量</th><th class='text-left nowrap'>总价佣金</th><th class='text-left nowrap'>下单时间 解冻时间</th><th class='text-left nowrap'>交易状态</th><th class='text-left nowrap'>操作</th><th class='text-left nowrap'>商品名称</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='text-left nowrap J_id'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['username']); ?></td><td class='text-left nowrap'><?php echo model('Users')->getLevel($vo['level'],'name');; ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['ubalance']); ?></td><td class='text-left nowrap '><?php echo htmlentities(format_datetime($vo['addtime'],'m-d')); ?><p class="J_today_num">排序</p></td><td class='text-left nowrap'>单价&yen;&nbsp; <?php echo htmlentities($vo['goods_price']); ?><p>数量:&nbsp; <?php echo htmlentities($vo['goods_count']); ?></td><td class='text-left nowrap'><font color="red">总价&yen;<?php echo htmlentities($vo['num']); ?></font><p>佣金&yen;<?php echo htmlentities($vo['commission']); ?></td><td class='text-left nowrap'><?php echo htmlentities(format_datetime($vo['addtime'])); ?><p><?php echo htmlentities(format_datetime($vo['endtime'])); ?></td><td class='text-left nowrap'><?php switch($vo['status']): case "0": ?><!-- {if auth("edit_recharge")}
                                <a data-csrf="{:systoken('admin/deal/edit_recharge')}" class="layui-btn layui-btn-xs layui-btn" data-action="{:url('edit_recharge',['status'=>2,'id'=>$vo.id])}" data-value="id#{$vo.id};status#2" >确认付款</a><a data-csrf="{:systoken('admin/deal/edit_recharge')}" class="layui-btn layui-btn-xs layui-btn-warm" data-action="{:url('edit_recharge',['status'=>3,'id'=>$vo.id])}" data-value="id#{$vo.id};status#3" >取消订单</a>                            {/if} -->                            等待付款
                        
                    <?php break; case "1": ?>完成付款<?php break; case "2": ?>用户取消<?php break; case "3": ?>强制付款<?php break; case "4": ?>系统取消<?php break; case "5": ?>订单冻结<?php break; ?><?php endswitch; ?></td><td class='text-left nowrap'><?php if($vo['status']==0): ?><!--
<a data-dbclick class="layui-btn layui-btn-xs " data-title="修改订单" data-modal='<?php echo url("admin/deal/edit_order"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>修改订单</a>--><a data-csrf="<?php echo systoken('admin/deal/do_user_order'); ?>" class="layui-btn layui-btn-xs layui-btn" data-action="<?php echo url('do_user_order'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#3" >强制付款</a><a data-csrf="<?php echo systoken('admin/deal/do_user_order'); ?>" class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('do_user_order'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#4" >取消订单</a><?php endif; ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['goods_name']); ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div><script type="text/javascript" charset="utf-8">$(function() {
        $(".J_id").each(function () {
            var id = $(this).text();
            console.log(id);
            var _this = $(this);
            $.ajax({
                dataType: "json",
                type: "POST",
                // async: false,
                url: "/admin/deal/get_order_info",
                data: { id: id, time: new Date().getTime() },
                success: function(data) {
                    console.log(data);
                    if (data.code == 1) {
                      //  _this.parent().find(".J_today_num").html(data.date + ' 第' + data.today_num + '单');
                        _this.parent().find(".J_today_num").html(' 第' + data.today_num + '单');
                        _this.parent().find(".J_parent_username").html(data.parent_username);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax error");
                },
                complete: function(XMLHttpRequest, textStatus) {
                    this; // 调用本次AJAX请求时传递的options参数
                }
            });
        });
    });
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