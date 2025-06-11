<?php /*a:2:{s:58:"D:\project\sd\application\index\view\order\index-pink.html";i:1667505872;s:59:"D:\project\sd\application\index\view\public\floor-pink.html";i:1667518698;}*/ ?>
<!DOCTYPE html><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('抢单记录'); ?></title><link href="/static_new6/css/app.pink.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><link rel="stylesheet" href="/public/js/layer_mobile/need/layer.css"><script src="/public/js/layer_mobile/layer.js"></script><style type="text/css" title="fading circle style">
        .circle-color-9 > div::before {
            background-color: #ccc;
        }

        /* 通用分页 */
        .pagination-container {
            line-height: 40px;
            text-align: right;
        }
        .pagination-container > span {
            color: #666;
            font-size: 9pt;
        }
        .pagination-container > ul {
            float: right;
            display: inline-block;
            margin: 0;
            padding: 0;
        }
        .pagination-container > ul > li {
            z-index: 1;
            display: inline-block;
        }
        .pagination-container > ul > li > a, .pagination-container > ul > li > span {
            color: #333;
            width: 33px;
            height: 30px;
            border: 1px solid #dcdcdc;
            display: inline-block;
            margin-left: -1px;
            text-align: center;
            line-height: 28px;
        }
        .pagination-container > ul > li > span {
            background: #dcdcdc;
            cursor: default;
        }
		.van-tab--active{
			border-bottom: 2px solid  #FC89AE;
		}
        .van-tab--active span{
         color:#FC89AE/*主题修改*/;
        }
        .circle-color-23 > div::before {
            background-color: #ccc;
        }
        .dialog {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 10001;
            width: 100%;
            height: 100%;
        }
        /*.mint-loadmore[data-v-35ad745e]{*/
        /*    background: none;*/
        /*}*/
        .circle {
            font-size:.5rem
            width: .4em;
            height: .4em;
            border-radius: 4em;
            background: #E83338;
            color:#fff;
        }
        .surplus{
            display: flex;
            flex-direction:column ;
            margin: 0.266667rem;
            height:3rem;
            justify-content: center;
            align-items: center;
            color: #fff;
            background: linear-gradient(to bottom right, #FAC1C7,#F28390);
            border-radius: 10px;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div class="mint-indicator" id="load" style="display: block;"><div class="mint-indicator-wrapper" style="padding: 20px;z-index:999"><span class="mint-indicator-spin"><div class="mint-spinner-fading-circle circle-color-23" style="width: 32px; height: 32px;"><div class="mint-spinner-fading-circle-circle is-circle2"></div><div class="mint-spinner-fading-circle-circle is-circle3"></div><div class="mint-spinner-fading-circle-circle is-circle4"></div><div class="mint-spinner-fading-circle-circle is-circle5"></div><div class="mint-spinner-fading-circle-circle is-circle6"></div><div class="mint-spinner-fading-circle-circle is-circle7"></div><div class="mint-spinner-fading-circle-circle is-circle8"></div><div class="mint-spinner-fading-circle-circle is-circle9"></div><div class="mint-spinner-fading-circle-circle is-circle10"></div><div class="mint-spinner-fading-circle-circle is-circle11"></div><div class="mint-spinner-fading-circle-circle is-circle12"></div><div class="mint-spinner-fading-circle-circle is-circle13"></div></div></span><span class="mint-indicator-text"style=""><?php echo lang('加载中'); ?>...</span></div><div class="mint-indicator-mask"></div></div><div data-v-35ad745e="" class="main" style="padding-bottom: 55px;"><div data-v-35ad745e="" class="records"><div data-v-35ad745e="" class="jianbian" ></div><div data-v-35ad745e="" class="records_top"style="justify-content:center;"><h2 style="margin-left: .10rem;font-size: 20px;font-weight: 800"><?php echo lang('抢单记录'); ?></h2><p data-v-35ad745e="" style="    position: absolute;
    top: 22px;
    left: 50%;    transform: translate(-50%, 0);"><?php echo lang('本数据由'); ?><?php echo sysconf('app_name'); ?><?php echo lang('官方提供'); ?></p></div><div data-v-35ad745e="" class="records_box"><div data-v-35ad745e="" class="mint-loadmore" ><div class="mint-loadmore-content"><div class="mint-loadmore-top"><!----><span class="mint-loadmore-text"><?php echo lang('下拉刷新'); ?></span></div><div data-v-35ad745e="" class="van-tabs van-tabs--line"><div data-v-35ad745e=""><div data-v-35ad745e="" class="van-sticky"><div data-v-35ad745e="" class="van-tabs__wrap van-hairline--top-bottom"><div data-v-35ad745e="" role="tablist" class="van-tabs__nav van-tabs__nav--line" ><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo !$status ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('全部'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == -1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=-1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('待处理'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == 1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('已完成'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == 5 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=5'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('冻结中'); ?></span></div><!-- <div data-v-35ad745e="" class="van-tabs__line"
                                                  style="background-color: rgb(255, 154, 44); transition-duration: 0.3s;"></div> --></div></div></div></div><div data-v-35ad745e="" class="surplus" style=""><div style="font-size:18px;font-weight:600;"><?php echo lang('剩余可用资产'); ?>(<?php echo lang('美元'); ?>)</div><div style="font-weight:900;font-size:40px;"><?php echo htmlentities($balance); ?></div><!-- <div style="position: relative;margin-left: .48rem">--><!--    <p style="font-size: 20px;font-weight: 800"></p>--><!--    <p style="color: white;position: absolute;right: 20px;top: 20px;text-align: center"></p>--><!--</div>--></div><div data-v-35ad745e="" class="van-tabs__content"><div data-v-35ad745e="" role="tabpanel" class="van-tab__pane"><div data-v-35ad745e="" role="feed" class="van-list"><?php if($list): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;                                        $img= '/static_new6/img/succ.png';
                                        if($v['status']==0) $img= '/static_new6/img/en_dai.png';
                                        if($v['status']==1) $img= '/static_new6/img/en_succ.png';
                                        if($v['status']==4) $img= '/static_new6/img/en_dongjie.png';
                                        if($v['status']==5) $img= '/static_new6/img/en_dongjie.png';
                                        
                                        ?><!--item--><div data-v-35ad745e=""><div data-v-35ad745e="" class="records_tabs"><div data-v-35ad745e="" class="records_tabs_box"><div data-v-35ad745e="" class="records_tabs_box_top"><?php echo lang('抢单时间'); ?><?php echo htmlentities(format_datetime($v['addtime'],'Y-m-d H:i:s')); ?><!--                                                        <span data-v-35ad745e="" >已完成</span>--></div><img data-v-35ad745e="" src="<?php echo htmlentities($img); ?>" class="records_tabs_box_img"><div data-v-35ad745e="" class="records_tabs_box_des"><div data-v-35ad745e="" class="tabs_box_des_img"><img data-v-35ad745e="" src="<?php echo htmlentities($v['goods_pic']); ?>"></div><div data-v-35ad745e="" class="tabs_box_des_r"><p data-v-35ad745e="" class="tabs_box_des_r_tit"><?php echo htmlentities($v['goods_name']); ?></p><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['goods_price']); ?></p><p data-v-35ad745e="">x <?php echo htmlentities($v['goods_count']); ?></p></div></div></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo lang('订单总额'); ?></p><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['num']); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo lang('佣金'); ?></p><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['commission']); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="txt txt2"><?php echo lang('预计返还'); ?></p><p data-v-35ad745e="" class="txt1"><?php if($v['status']==4||$v['status']==5): ?>0<?php else: ?><?php echo lang('￥'); ?><?php echo htmlentities($v['commission']+$v['num']); ?><?php endif; ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic" ><button data-v-35ad745e="" onclick="tijiao('<?php echo htmlentities($v['id']); ?>')" class="redb" style="display: <?php echo $v['status']==0 ? 'block':'none'; ?>;padding: 3px 10px;border-radius: 5px;width: 40%;float:left;"><?php echo lang('提交订单'); ?></button><?php if($balance<$v['num']): ?><p data-v-35ad745e="" class="txt" style="display: <?php echo $v['status']==0 ? 'block':'none'; ?>;width:50%;float:right;color:red" onclick="jump('<?php echo htmlentities($v['num']-$balance); ?>')"><?php echo lang('账户可用余额不足'); ?></p><?php endif; ?></div></div></div></div><!--item--><?php endforeach; endif; else: echo "" ;endif; else: ?><?php endif; if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata"><?php echo lang('没有记录哦'); ?></span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?><div data-v-35ad745e="" class="van-list__placeholder"><div data-v-35ad745e="" class="no_more" style="display: none;"><?php echo lang('没有数据'); ?></div></div></div></div></div></div></div></div></div></div><div data-v-4c2dcb20="" class="" style="" id="orderDetail"><div data-v-4c2dcb20="" class="van-overlay" style="z-index: 1000;padding: 0 10px;display:none ;"><div data-v-4c2dcb20="" class="grabSuccess" style="width: 100%;;"><div data-v-4c2dcb20="" class="title" style="height: 1.586667rem;"><?php if(app('cookie')->get('think_var') == "zh-cn"): ?><img data-v-4c2dcb20="" src="/static_new6/img/ordersucesstitle.png"><?php else: ?><img data-v-4c2dcb20="" src="/static_new6/img/en_ordersucesstitle.png"><?php endif; ?></div><div data-v-4c2dcb20="" class="records_tabs_box"><div data-v-4c2dcb20="" class="records_tabs_box_top"><?php echo lang('抢单时间'); ?>：<span id="otime">0</span><?php echo lang('抢单编号'); ?>：<span id="oid">0</span></div><?php if(app('cookie')->get('think_var') == "zh-cn"): ?><img data-v-4c2dcb20="" src="/static_new6/img/ordersucess.png" class="records_tabs_box_img"><?php else: ?><img data-v-4c2dcb20="" src="/static_new6/img/en_ordersucess.png" class="records_tabs_box_img"><?php endif; ?><div data-v-4c2dcb20="" class="records_tabs_box_des"><div data-v-4c2dcb20="" class="tabs_box_des_img"><img data-v-4c2dcb20="" alt="" id="oimg" src="/static_new6/img/wenhao.png"></div><div data-v-4c2dcb20="" class="tabs_box_des_r"><p data-v-4c2dcb20="" class="tabs_box_des_r_tit" id="otitle">
                                    描述
                                </p><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" id="oprice"><?php echo lang('￥'); ?> 0</p><p data-v-4c2dcb20="" id="onum">x 1</p></div></div></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo lang('订单总额'); ?></p><p data-v-4c2dcb20="" id="ototal"><?php echo lang('￥'); ?> 0</p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo lang('佣金'); ?></p><p data-v-4c2dcb20=""><?php echo lang('￥'); ?><span id="yongjin">8</span></p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="txt"><?php echo lang('预计返还'); ?></p><p data-v-4c2dcb20="" class="txt1 "><?php echo lang('￥'); ?><span id="yuji">0</span></p></div></div><div data-v-4c2dcb20="" class="tabs_btn"><div data-v-4c2dcb20="" class="tabs_btn1"><?php echo lang('暂不提交'); ?></div><div data-v-4c2dcb20="" class="tabs_btn2"><?php echo lang('立即提交'); ?></div></div></div><img data-v-4c2dcb20=""
                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFXRSTlMAEFN/o8rX6/sxkf9z88Q9XSlotgUhojDzAAABp0lEQVR4AaSSVwKzIBgEkfYBS1vN/a/690oitnlK1Bmq2mPRxjovIYh31uhFXSImjwGf4ll7yR5AqSnr1mJsTedUCwCflzO6EUBsj/yP2K0AYg4TXQC38iOrA6RPdVbAde7SHVC57+sCyZySBUXv+QnYIg+IG5A+6i+LYHgCE2BfH/wNpfMUvWB7L1iI5km0wL6vv2ieRpdxHzRC5wV6gP7XZ4HhJQwK/wlUbLzIhqr+0CGRF4mCP7d6EWReJkMW9RMDxxs4mD8T6LxB/z2FDMdbOGT1HY91vCfOrxxYvdPjI/jvfoS8pxHysGUB7xMVxB+X2HLA43th9OE5YJF+rKBzYA0/CoMfVg7072tYUP5cokHI49+RWLAojUpOChOfrNDKIHFSmPpMMMoic1qY+DSwykFzVpj51HDKo3FWmPls8ErQOCvMfDaICoicFiY+I8LjwOMlPN7EZ8fY4Z5dpAz7/Cp/Lb0OBAAAABiE+VsPYhB/tWPqOe+h9KX1qe6tN1gebQ/Xx/sLxivOS9Zr3ovmq+7L9uv+B8cnz0fXZ9+HZ6ZvxHfkP9pUwubvmZnSAAAAAElFTkSuQmCC"
                     alt="" class="close"></div></div><link href="/web/css/pink.css" rel="stylesheet" /><div data-v-59e03106="" data-v-43c1797b="" class="footer" style="color:red"><ul data-v-59e03106=""><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('index/home'); ?>'"><img data-v-59e03106="" src="/web/img/pink_shouye.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_shouye1.png" alt="" /><p data-v-59e03106=""><?php echo lang('首页'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('order/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_jilu.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_jilu1.png" alt="" /><p data-v-59e03106=""><?php echo lang('记录'); ?></p></li><li data-v-59e03106="" class="t" onclick="window.location.href='<?php echo url('rot_order/index'); ?>'"><img data-v-59e03106="" src="<?php echo lang('/web/img/pink_jia1png.png'); ?>" alt="" /><img data-v-59e03106="" src="<?php echo lang('/web/img/pink_jia.png'); ?>" alt="" /></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('support/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_zaixian.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_zaixian1.png" alt="" /><p data-v-59e03106=""><?php echo lang('在线'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('my/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_wode.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_wode1.png" alt="" /><p data-v-59e03106=""><?php echo lang('我的'); ?></p></li></ul></div><script>    function updateactivetime(){
        var url = "<?php echo url('user/updateactivetime'); ?>";
        var isopenoline = 1;
        $.ajax({
            type : "POST",
            url : url,
            data: isopenoline,
            dataType : "json",
            success : function(result){
                console.log(result);
                if(result.code=="0"){

                    alert(result.msg);
                }
            },
            error:function(){
                //alert();
            }
        });
    }
    updateactivetime();

    setInterval(updateactivetime,1000*30);

    (function(doc,win){
        var docEl = doc.documentElement,  //文档根标签
            resizeEvent =  'orientationchange' in window ? 'orientationchang' :'resize'; //viewport变化事件源
        var rescale = function(){
            //重置方法
            win.clientWidth = docEl.clientWidth;
            if (!win.clientWidth) return;
            // 改变DOM根节点fontSize大小的值;
            // (屏幕宽度/设计图宽度) = 缩放或扩大的比例值;
            docEl.style.fontSize = 70 * (win.clientWidth / 750) + 'px';
        }
        if (!doc.addEventListener) return;
        win.addEventListener(resizeEvent, rescale, false);
        doc.addEventListener('DOMContentLoaded', rescale, false);
    })(document, window);
</script><style></style></div></div><script type="text/javascript" src="/static_new6/js/manifest.3ad1d5771e9b13dbdad2.js"></script><!--<script type="text/javascript" src="/static_new6/js/vendor.832c1df07e64f8264dc6.js"></script>--><!--<script type="text/javascript" src="/static_new6/js/app.1ef0c51eb4b45b9f8d06.js"></script>--><script>
    var oid,add_id='';
    $(function () {
        $('#load').hide();
    });
	// $(function(){
	// 	let info=document.querySelector('.van-tab--active').getBoundingClientRect()
	// 	$('.van-tabs__line').css('margin-left',info.left)
	// })
    $('.pagination li').click(function () {
        var class2= $(this).attr('class');
        if( class2 == 'active' || class2 == 'disabled' ) {

        }else{
            var url = $(this).find('a').attr('href');
            window.location.href = url;
        }
    });
    $(function () {
        $('.pagination-container select').attr('disabled','disabled');
    })

    $('.tabs_btn1').click(function () {
        $('#orderDetail .van-overlay').hide();
    });
    $('.close').click(function () {
        $('#orderDetail .van-overlay').hide();
    });
    function tijiao(id) {
        oid = id;
        $.ajax({
            url: "/index/order/order_info",
            type: "POST",
            dataType: "JSON",
            data: { id: id },
            beforeSend: function () {
                loading = $(document).dialog({
                    type: 'notice',
                    infoIcon: '/static_new/img/loading.gif',
                    infoText: '<?php echo lang('正在加载中'); ?>',
                    autoClose: 0
                });
            },
            success: function(res) {
                console.log(res);
                loading.close();
                var data = res.data;
                if (res.code == 0) {
                    $('#otime').html(data.addtime)
                    $('#oid').html(data.oid)
                    $('#otitle').html(data.goods_name)
                    $('#oimg').attr('src',data.goods_pic)
                    $('#oprice').html(data.goods_price)
                    $('#onum').html(data.goods_count)
                    $('#ototal').html('<?php echo lang('￥'); ?> '+data.num)
                    $('#yongjin').html(''+data.commission)
                    var yuji = ( data.commission * 1 +  data.num * 1 );
                    yuji = yuji.toFixed(2);
                    $('#yuji').html(yuji)
                    oid = data.oid;
                    add_id = data.add_id;
                    
        $('#orderDetail .van-overlay').show();
                }

            },
            error: function(err) {
                loading.close();
                console.log(err) }
        })
    }
    var zhujiTime = "<?php echo config('deal_zhuji_time'); ?>";
    var shopTime = "<?php echo config('deal_shop_time'); ?>";

    zhujiTime = zhujiTime *1000;
    shopTime = shopTime *1000;

    //提交
    $('.tabs_btn2').click(function () {
        //--------------------------------
        var i = 0;
        layer.open({
            type: 2
            , content: '<?php echo lang('订单提交中'); ?>',
            time: zhujiTime,
            shadeClose: false,
        });

        //--------------------------------
        var i = 0;
        layer.open({
            type: 2
            , content: '<?php echo lang('订单提交中'); ?>',
            time: zhujiTime,
            shadeClose: false,
        });
        var timer = setInterval(function() {
            i++;
            if (i == 1) {
                layer.open({
                    type: 2
                    , content: '<?php echo lang('远程主机正在分配'); ?>',
                    time: zhujiTime,
                    shadeClose: false,
                })
            } else if (i == 2) {
                layer.open({
                    type: 2
                    , content: '<?php echo lang('等待商家系统响应'); ?>',
                    time: shopTime,
                    shadeClose: false,
                })
                var ajaxT = setTimeout(function(){
                    $.ajax({
                        url: "/index/order/do_order",
                        type: "POST",
                        dataType: "JSON",
                        data: { oid:oid, add_id:add_id },
                        success: function(res) {
                            layer.closeAll();
                            console.log(res)
                            if (res.code == 0) {
                                $(document).dialog({
                                    infoText: "<?php echo lang('订单提交成功'); ?>!",
                                    autoClose: 2000
                                });
                                clearInterval(timer);
                                var linkTime = setTimeout(function() {
                                    location.reload()
                                }, 1800);
                            }else  if (res.code == 1) {
                                $(document).dialog({
                                    infoText: res.info,
                                    autoClose: 2000
                                });
                                clearInterval(timer);
                                var linkTime = setTimeout(function() {
                                    jump(res.need);
                                }, 1800);
                            }else{
                                
                                $(document).dialog({
                                    infoText: res.info,
                                    autoClose: 2000
                                });
                            }
                            sumbit = true;
                        },
                        error: function(err) {
                            layer.closeAll();
                            console.log(err); sumbit = true;
                        }
                    })
                },shopTime)
            }
        }, zhujiTime)


    });
    
    $(function() {
        $('.footer li').eq(1).addClass("on");

    })
    function jump(need){
       window.location.href="/index/ctrl/recharge?need="+need;

    }
</script></body></html>