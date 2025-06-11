<?php /*a:2:{s:55:"D:\project\sd\application\index\view\my\index-pink.html";i:1667519080;s:59:"D:\project\sd\application\index\view\public\floor-pink.html";i:1667518698;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0035)http://qiang6-www.baomiche.com/#/My --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('我的'); ?></title><link href="/static_new6/css/app.pink.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css">`
    <script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><style type="text/css" title="fading circle style">
        .circle-color-8 > div::before {
            background-color: #ccc;
        }
        .login_nav li a{
            display: block;
            /*width: 100%;*/
            /*margin-top: 0.20rem;*/
            margin: .2rem;
            text-align: center;
        }
         .login_nav li bottom{
            display: block;
            width: 100%;
            margin-top: 0.20rem;
            text-align: center;
        }
        .login_nav[data-v-d5f15326]{
            background: none;
            /*display: flex;*/
        }
        .pink_jiantou{
            width: .3rem;
            height: .4rem;
        }
        /*.title{*/
        /*    width: 100%;*/
        /*    height: 100px;*/
        /*    border: 1px solid red;*/
        /*}*/
    </style></head><body style="font-size: 12px;"><div id="app" style="margin-top:-2.51rem;"><div data-v-d5f15326="" class="main"><div class="my-index-header"><div data-v-d5f15326="" class="header"><div data-v-d5f15326="" class="info"><img data-v-d5f15326="" src="/web/img/USER_item.png" onerror="this.src='/public/img/head.png'" alt="" class="headerImg"><div data-v-d5f15326="" class="name"><strong data-v-d5f15326=""><?php echo htmlentities($info['username']); ?></strong><small data-v-d5f15326=""><?php echo lang('邀请码'); ?>:<?php echo htmlentities($info['invite_code']); ?></small></div><div data-v-d5f15326="" style="font-size: 12px ;width: 70px;height: 80px;position: absolute;left:300px; text-align:center;line-height: 90px; background: url(/web/img/pink_huizhang.png) no-repeat;background-size:100% 100%; color: #8E5164 "><?php echo htmlentities($level_name); ?></div><!--                    <div style="" ><?php echo htmlentities($level_name); ?></div>--></div><div data-v-d5f15326="" class="balance"><p data-v-d5f15326=""><!--<img data-v-d5f15326="" src="/web/img/yuehuizhang.png" style="width:28px;height:28px;margin-right:10px;">--><small data-v-d5f15326="" style=" "><?php echo lang('余额'); ?>：</small><small data-v-d5f15326="" style="font-weight: 600;"><?php echo lang('￥'); ?><?php echo htmlentities($info['balance']); ?></small></p><div data-v-d5f15326="" class="youjian"><button data-v-d5f15326="" onclick="window.location.href=`/index/ctrl/deposit`"  style="background-color:rgba(0,0,0,0);margin-right:20px" class="" ><?php echo lang('提现'); ?></button><button data-v-d5f15326=""  onclick="window.location.href=`/index/ctrl/recharge`" class="" style="background-color:#FEB1C8;color:#fff"><?php echo lang('充值'); ?></button></div></div><!--                <div data-v-d5f15326="" class="yh"></div>--></div></div><div class="content_item" ><ul data-v-d5f15326="" class="login_nav" style="display:flex;border-radius: 15px;"><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/recharge_admin`"   class=""><img data-v-d5f15326=""
                             src="/web/img/pink_chongzhijilu.png"
                             alt=""
                             style="  margin-bottom: 0.2rem;"><p data-v-d5f15326=""><?php echo lang('充值记录'); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/order/index`" class=""><img data-v-d5f15326=""
                             src="/web/img/pink_qiangdanjilu.png"
                             alt="" style="  margin-bottom: 0.2rem;"><p data-v-d5f15326=""><?php echo lang('抢单记录'); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/deposit_admin`"  class=""><img data-v-d5f15326=""
                             src="/web/img/pink_tixianjilu.png"
                             alt="" style="  margin-bottom: 0.2rem;"><p data-v-d5f15326=""><?php echo lang('提现记录'); ?></p></a></li></ul><ul data-v-d5f15326="" class="login_nav" style="border-radius: 5px;  
    "><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/set`" class="user_item"><!--<img data-v-d5f15326=""--><!--     src="/web/img/c_gerenxinxi.png"--><!--     alt="">--><p data-v-d5f15327=""><?php echo lang('个人信息'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt=""  style=" width: .2rem; height: .3rem;"></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/caiwu`" class="user_item"><!--<img data-v-d5f15326=""--><!--     src="/web/img/c_zhanghumingxi.png"--><!--     alt="">--><p data-v-d5f15327=""><?php echo lang('账户明细'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt="" style=" width: .2rem; height: .3rem;" ></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/invite`" class="user_item"><!--<img data-v-d5f15326=""--><!--     src="/web/img/c_yaoqinghaoyou.png"--><!--     alt="">--><p data-v-d5f15327=""><?php echo lang('邀请好友'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt="" style=" width: .2rem; height: .3rem;" ></a></li><li data-v-d5f15327=""><!--                display: flex;--><!--flex-wrap: wrap;--><!--justify-content: center;--><!--align-items: center;--><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/msg`" class="user_item" ><!--<img data-v-d5f15326=""--><!--     src="/web/img/c_xinxigonggao.png"--><!--     alt="">--><p data-v-d5f15327=""><?php echo lang('信息公告'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt="" style=" width: .2rem; height: .3rem;" ></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/junior`" class="user_item"><!--<img data-v-d5f15326=""--><!--     src="/web/img/c_tuanduibaobiao.png"--><!--     alt="">--><p data-v-d5f15327=""><?php echo lang('团队报表'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt="" style=" width: .2rem; height: .3rem;" ></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`<?php if(sysconf('app_url')): ?><?php echo sysconf('app_url'); else: ?>/download<?php endif; ?>`" class="user_item"><!--<img data-v-d5f15326="" src="/web/img/appxiazai.png" alt="">--><p data-v-d5f15327=""><?php echo lang('APP下载'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt=""  style=" width: .2rem; height: .3rem;"></a></li><li data-v-d5f15327=""><a data-v-d5f15326="" class="tabs_btn1 user_item"><!--<img data-v-d5f15326="" src="/web/img/tuichudenglu.png" alt="" >--><p data-v-d5f15327=""><?php echo lang('退出登陆'); ?></p><img data-v-d5f15326="" src="/web/img/pink_jiantou.png" alt="" style=" width: .2rem; height: .3rem;"></a></li><!--              <li data-v-d5f15327="">--><!--                <a data-v-d5f15326="" class="">--><!--                    <div data-v-d5f15326=""style="    height: 0.6rem;--><!--margin-bottom: 0.2rem;"></div>--><!--                    <p data-v-d5f15327="" style="color:#fff">123456</p>--><!--                </a>--><!--            </li>--></ul><!--        <ul data-v-d5f15326="" class="login_nav" style="background-color: white ;border-radius: 5px; margin-top:1rem;  display: flex;--><!--flex-wrap: wrap;--><!--align-items: center;--><!--">--><!--            <li data-v-d5f15327="">--><!--                <a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`<?php if(sysconf('app_url')): ?><?php echo sysconf('app_url'); else: ?>/download<?php endif; ?>`" class="">--><!--                    <img data-v-d5f15326="" src="/web/img/c_appxiazai.png" alt="">--><!--                    <p data-v-d5f15327=""><?php echo lang('APP下载'); ?></p>--><!--                </a>--><!--            </li>--><!--            <li data-v-d5f15327="">--><!--                <a data-v-d5f15326="" class="tabs_btn1">--><!--                    <img data-v-d5f15326="" src="/web/img/c_tuichu.png" alt="" >--><!--                                            <p data-v-d5f15327=""><?php echo lang('退出登陆'); ?></p>--><!--<?php echo lang('退出登陆'); ?>--><!--                </a>--><!--            </li>--><!--              <li data-v-d5f15327="">--><!--                <a data-v-d5f15326="" class="">--><!--                    <div data-v-d5f15326=""style="    height: 0.6rem;--><!--margin-bottom: 0.2rem;"></div>--><!--                    <p data-v-d5f15327="" style="color:#fff">123456</p>--><!--                </a>--><!--            </li>--><!--        </ul>--></div><div class="title" ></div><link href="/web/css/pink.css" rel="stylesheet" /><div data-v-59e03106="" data-v-43c1797b="" class="footer" style="color:red"><ul data-v-59e03106=""><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('index/home'); ?>'"><img data-v-59e03106="" src="/web/img/pink_shouye.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_shouye1.png" alt="" /><p data-v-59e03106=""><?php echo lang('首页'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('order/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_jilu.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_jilu1.png" alt="" /><p data-v-59e03106=""><?php echo lang('记录'); ?></p></li><li data-v-59e03106="" class="t" onclick="window.location.href='<?php echo url('rot_order/index'); ?>'"><img data-v-59e03106="" src="<?php echo lang('/web/img/pink_jia1png.png'); ?>" alt="" /><img data-v-59e03106="" src="<?php echo lang('/web/img/pink_jia.png'); ?>" alt="" /></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('support/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_zaixian.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_zaixian1.png" alt="" /><p data-v-59e03106=""><?php echo lang('在线'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('my/index'); ?>'"><img data-v-59e03106="" src="/web/img/pink_wode.png" alt="" /><img data-v-59e03106="" src="/web/img/pink_wode1.png" alt="" /><p data-v-59e03106=""><?php echo lang('我的'); ?></p></li></ul></div><script>    function updateactivetime(){
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