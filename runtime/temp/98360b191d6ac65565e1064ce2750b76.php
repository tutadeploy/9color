<?php /*a:2:{s:56:"D:\project\sd\application\index\view\my\index-black.html";i:1657652148;s:60:"D:\project\sd\application\index\view\public\floor-black.html";i:1660177632;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0035)http://qiang6-www.baomiche.com/#/My --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('我的'); ?></title><link href="/static_new6/css/app.black.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><style type="text/css" title="fading circle style">
        .circle-color-8 > div::before {
            background-color: #ccc;
        }
        .login_nav li a{
            display: block;
            width: 100%;
        }
        .login_nav[data-v-d5f15326]{
            background: none;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div data-v-d5f15326="" class="main"><div class="my-index-header"><div data-v-d5f15326="" class="header"><span data-v-d5f15326="" class="title"><?php echo lang('我的'); ?></span><div data-v-d5f15326="" class="img"><a data-v-d5f15326="" href="" class=""><img data-v-d5f15326=""
			                 src="/web/img/black-msg.png"
                             alt=""></a><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/set`" class=""><img data-v-d5f15326=""
                             src="/web/img/black-sz.png"
			                 alt=""></a></div><div data-v-d5f15326="" class="balance"><div data-v-d5f15326="" class="info"><img data-v-d5f15326="" src="<?php echo htmlentities($info['headpic']); ?>" onerror="this.src='/public/img/black-tx.png'" alt="" class="headerImg"><div data-v-d5f15326="" class="name"><strong data-v-d5f15326=""><?php echo htmlentities($info['username']); ?></strong><small data-v-d5f15326=""><?php echo lang('邀请码'); ?>:<?php echo htmlentities($info['invite_code']); ?></small></div></div><br><br><br><br><br><p data-v-d5f15326=""><span data-v-d5f15326=""><?php echo lang('账户金额'); ?></span><em data-v-d5f15326=""><small data-v-d5f15326=""><?php echo htmlentities($info['balance']); ?><span><?php echo lang('￥'); ?></span></small><br></em><br><button data-v-d5f15326=""  onclick="window.location.href=`/index/ctrl/deposit`"  class="" style="width:1.3rem;"><?php echo lang('提现'); ?></button><button data-v-d5f15327="" onclick="window.location.href=`/index/ctrl/recharge`"  class="" style="width:1.3rem;"><?php echo lang('充值'); ?></button></p><div data-v-d5f15327=""><span><?php echo htmlentities($level_name); ?></span></div></div><div data-v-d5f15326="" class="yh"></div></div></div><ul data-v-d5f15326="" class="login_nav"><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/recharge_admin`"   class=""><img data-v-d5f15326=""
                         src="/web/img/black-tkjl.png"
                         alt=""><p data-v-d5f15326=""><?php echo lang('充值记录'); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/order/index`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-ddls.png"
                         alt=""><p data-v-d5f15326=""><?php echo lang('抢单记录'); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/deposit_admin`"  class=""><img data-v-d5f15326=""
                         src="/web/img/black-ls.png"
                         alt=""><p data-v-d5f15326=""><?php echo lang('提现记录'); ?></p></a></li></ul><ul data-v-d5f15326="" class="login_nav"><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/set`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-grxx.png"
                         alt=""><p data-v-d5f15327=""><?php echo lang('个人信息'); ?></p></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/caiwu`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-khxx.png"
                         alt=""><p data-v-d5f15327=""><?php echo lang('账户明细'); ?></p></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/invite`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-yqpy.png"
                         alt=""><p data-v-d5f15327=""><?php echo lang('邀请好友'); ?></p></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/msg`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-xxgg.png"
                         alt=""><p data-v-d5f15327=""><?php echo lang('信息公告'); ?></p></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/junior`" class=""><img data-v-d5f15326=""
                         src="/web/img/black-tdbg.png"
                         alt=""><p data-v-d5f15327=""><?php echo lang('团队报表'); ?></p></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`<?php if(sysconf('app_url')): ?><?php echo sysconf('app_url'); else: ?>/download<?php endif; ?>`" class=""><img data-v-d5f15326="" src="/web/img/black-xz.png" alt=""><p data-v-d5f15327=""><?php echo lang('APP下载'); ?></p></a></li></ul><div data-v-d5f15326="" class="LoginOut"><button data-v-d5f15326="" class="tabs_btn1"><?php echo lang('退出登陆'); ?></button></div><link href="/web/css/black.css" rel="stylesheet" /><div data-v-59e03106="" data-v-43c1797b="" class="footer"><ul data-v-59e03106=""><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('index/home'); ?>'"><img data-v-59e03106="" src="/public/black-tb2.png" alt="" /><img data-v-59e03106="" src="/public/black-tb22.png" alt="" /><p data-v-59e03106=""><?php echo lang('首页'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('order/index'); ?>'"><img data-v-59e03106="" src="/public/black-tb3.png" alt="" /><img data-v-59e03106="" src="/public/black-tb33.png" alt="" /><p data-v-59e03106=""><?php echo lang('记录'); ?></p></li><li data-v-59e03106="" class="t" onclick="window.location.href='<?php echo url('rot_order/index'); ?>'"><img data-v-59e03106="" src="<?php echo lang('/public/footico.png'); ?>" alt="" /><img data-v-59e03106="" src="<?php echo lang('/public/footico.png'); ?>" alt="" /></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('support/index'); ?>'"><img data-v-59e03106="" src="/public/black-tb4.png" alt="" /><img data-v-59e03106="" src="/public/black-tb44.png" alt="" /><p data-v-59e03106=""><?php echo lang('在线'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('my/index'); ?>'"><img data-v-59e03106="" src="/public/black-tb1.png" alt="" /><img data-v-59e03106="" src="/public/black-tb11.png" alt="" /><p data-v-59e03106=""><?php echo lang('我的'); ?></p></li></ul></div><script>    function updateactivetime(){
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
</script><style></style></div></div><script>
    $(function() {
        $('.footer li').eq(4).addClass("on");

    })

    $('.tabs_btn1').click(function () {
        $(document).dialog({
            type: 'confirm',
            titleText: "<?php echo lang('确认退出吗'); ?>?",
            autoClose: 0,
            onClickConfirmBtn: function () {
                window.location.href="<?php echo url('user/logout'); ?>";
            },
            onClickCancelBtn: function () {

            }
        });
    });
        

</script></body></html>