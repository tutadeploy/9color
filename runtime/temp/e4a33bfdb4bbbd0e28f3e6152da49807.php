<?php /*a:2:{s:59:"D:\project\sd\application\admin\view\deal\deposit_list.html";i:1640156354;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><?php if(auth("do_deposit")): ?><div class="layui-form-item layui-inline" style="margin-right: 10px"><button data-action='<?php echo url("do_deposit2"); ?>' data-csrf="<?php echo systoken('do_deposit2'); ?>" data-rule="id#{key}" class='layui-btn layui-btn-sm layui-btn-danger'>批量通过</button><button data-action='<?php echo url("do_deposit3"); ?>' data-csrf="<?php echo systoken('do_deposit3'); ?>" data-rule="id#{key}" class='layui-btn layui-btn-sm layui-btn-warning'>批量拒绝</button></div><?php endif; ?><div class="layui-form-item layui-inline"><label class="layui-form-label">订单号</label><div class="layui-input-inline"><input name="oid" value="<?php echo htmlentities((app('request')->get('oid') ?: '')); ?>" placeholder="请输入订单号" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">发起时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择发起时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">审核状态</label><div class="layui-input-inline"><select name="status" id="selectList"><option value="">所有状态</option><option value="1">待处理</option><option value="2">审核通过</option><option value="3">审核不通过</option></select></div></div><script>    var test = <?php echo htmlentities((app('request')->get('status') ?: '0')); ?>;
    $("#selectList").find("option[value="+test+"]").prop("selected",true);
        </script><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button><?php if(auth("do_deposit")): ?><a href="/admin/deal/daochu.html" class="layui-btn layui-btn-danger" ><i class="layui-icon">&#xe615;</i> 导 出</a><?php endif; ?></div></form></fieldset><fieldset><legend>数据统计</legend><font color="red">提现总额：：<?php echo htmlentities($user_deposit); ?>元</font></fieldset><script>form.render()</script><table class="layui-table margin-top-15" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-check-td think-checkbox'><input data-auto-none data-check-target='.list-check-box' type='checkbox'></th><th class='text-left nowrap'>订单号</th><th class='text-left nowrap'>用户姓名</th><th class='text-left nowrap'>提现金额</th><th class='text-left nowrap'>实际到账</th><th class='text-left nowrap'>提现方式</th><th class='text-left nowrap'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			地址</th><th class='text-left nowrap'>处理时间</th><th class='text-left nowrap'>订单状态</th><?php if(auth('do_deposit')): ?><th class='text-left nowrap'>操作</th><?php endif; ?></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='list-table-check-td think-checkbox'><input class="list-check-box" value='<?php echo htmlentities($vo['id']); ?>' type='checkbox'></td><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'>用户名<?php echo htmlentities($vo['username']); ?><p>电话<?php echo htmlentities($vo['tel']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['num']); ?><p>手续费 <?php if(($vo['type'] == 'trc')): ?>1U <?php endif; if(($vo['type'] == 'erc')): ?> 10U<?php endif; ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['real_num']); if($vo['tradeNo']): ?><hr>                代付日期:<?php echo htmlentities($vo['applyDate']); ?><hr>                代付订单:<?php echo htmlentities($vo['tradeNo']); ?><hr>                代付状态:<?php echo htmlentities($vo['tradeResult']); else: ?><hr>                未创建代付
                <?php endif; ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['type']); ?></td><td class='text-left nowrap'><?php if(($vo['type'] == 'trc')): ?><p >TRC:   
    <?php if(!(empty($vo['trc20_address']) || (($vo['trc20_address'] instanceof \think\Collection || $vo['trc20_address'] instanceof \think\Paginator ) && $vo['trc20_address']->isEmpty()))): ?><?php echo htmlentities($vo['trc20_address']); else: ?>未填写<?php endif; ?></p><?php endif; if(($vo['type'] == 'erc')): ?><p >ERC:
    <?php if(!(empty($vo['erc20_address']) || (($vo['erc20_address'] instanceof \think\Collection || $vo['erc20_address'] instanceof \think\Paginator ) && $vo['erc20_address']->isEmpty()))): ?><?php echo htmlentities($vo['erc20_address']); else: ?>未填写<?php endif; ?></p><?php endif; if(($vo['type'] == 'card')): if(!(empty($vo['bk_id']) || (($vo['bk_id'] instanceof \think\Collection || $vo['bk_id'] instanceof \think\Paginator ) && $vo['bk_id']->isEmpty()))): ?><p >    银行:<?php echo model('Users')->get_bank_info($vo['bk_id'],'bankname');; ?><hr>    银行编码:<?php echo model('Users')->get_bank_info($vo['bk_id'],'bankcode');; ?><hr>    IFSC:<?php echo model('Users')->get_bank_info($vo['bk_id'],'ifsc');; ?><hr>    卡号:<?php echo model('Users')->get_bank_info($vo['bk_id'],'cardnum');; ?><hr>    用户名:<?php echo model('Users')->get_bank_info($vo['bk_id'],'username');; ?><hr>    备注:<?php echo model('Users')->get_bank_info($vo['bk_id'],'remark');; ?><hr></p><?php endif; ?><?php endif; ?></td><td class='text-left nowrap'>创建<?php echo htmlentities(format_datetime($vo['addtime'])); ?><p>处理<?php echo htmlentities(format_datetime($vo['endtime'])); ?></td><td class='text-left nowrap'><?php switch($vo['status']): case "1": ?>待审核<?php break; case "2": ?>审核通过<?php break; case "3": ?>审核驳回<?php break; ?><?php endswitch; ?></td><td class='text-left nowrap'><?php if(($vo['status'] == 1) and auth("do_deposit")): ?><a class="layui-btn layui-btn-xs" data-csrf="<?php echo systoken('admin/deal/do_deposit'); ?>" data-action="<?php echo url('do_deposit'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2">通过</a><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/deal/do_deposit'); ?>" data-action="<?php echo url('do_deposit'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#3;uid#<?php echo htmlentities($vo['uid']); ?>;num#<?php echo htmlentities($vo['num']); ?>">驳回</a><?php endif; if(($vo['status'] == 2) and auth("disbursement") and ($vo['tradeResult'] != 'SUCCESS') and (!$vo['tradeNo'])): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="银行卡信息" data-modal='<?php echo url("admin/users/edit_users_bk"); ?>?id=<?php echo htmlentities($vo['uid']); ?>'>修改银行卡</a><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/deal/disbursement'); ?>" data-action="<?php echo url('disbursement'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;uid#<?php echo htmlentities($vo['uid']); ?>;num#<?php echo htmlentities($vo['num']); ?>">发起代付</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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