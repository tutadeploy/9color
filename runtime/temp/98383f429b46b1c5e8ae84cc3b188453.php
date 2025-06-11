<?php /*a:2:{s:60:"D:\project\sd\application\admin\view\config\indexconfig.html";i:1640159112;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><form onsubmit="return false;" data-auto="true" method="post" class='layui-form layui-card' autocomplete="off"><div class="layui-tab layui-tab-card"><ul class="layui-tab-title"><li class="layui-this">首页滚动信息</li></ul><div class="layui-tab-content" style=""><div class="layui-tab-item layui-show"><div class="layui-card-body padding-40 layui-col-md8"><label class="layui-form-item block relative"><span class="color-green margin-right-10">首页滚动信息</span><textarea style="min-height: 230px;line-height:16px" name="indexscrollnew" required  class="layui-input"><?php echo htmlentities($indexscrollnew); ?></textarea><p class="help-block">一行一个</p></label></div><div class="layui-card-body padding-40 layui-col-md8"><label class="layui-form-item block relative"><span class="color-green margin-right-10">英文滚动信息</span><textarea style="min-height: 230px;line-height:16px" name="enindexscrollnew" required  class="layui-input"><?php echo htmlentities($enindexscrollnew); ?></textarea><p class="help-block">一行一个</p></label></div><div class="layui-card-body padding-40 layui-col-md8"><label class="layui-form-item block relative"><span class="color-green margin-right-10">韩文滚动信息</span><textarea style="min-height: 230px;line-height:16px" name="krindexscrollnew" required  class="layui-input"><?php echo htmlentities($krindexscrollnew); ?></textarea><p class="help-block">一行一个</p></label></div><div class="layui-card-body padding-40 layui-col-md8"><label class="layui-form-item block relative"><span class="color-green margin-right-10">日文滚动信息</span><textarea style="min-height: 230px;line-height:16px" name="jpindexscrollnew" required  class="layui-input"><?php echo htmlentities($jpindexscrollnew); ?></textarea><p class="help-block">一行一个</p></label></div></div></div><div class="layui-form-item text-center margin-top-20"><button class="layui-btn" type="submit">保存配置</button></div></div></form></div></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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