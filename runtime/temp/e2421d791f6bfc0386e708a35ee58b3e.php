<?php /*a:2:{s:57:"D:\project\sd\application\index\view\order\index-red.html";i:1657417232;s:58:"D:\project\sd\application\index\view\public\floor-red.html";i:1657417180;}*/ ?>
<!DOCTYPE html><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('抢单记录'); ?></title><link href="/static_new6/css/app.red.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><link rel="stylesheet" href="/public/js/layer_mobile/need/layer.css"><script src="/public/js/layer_mobile/layer.js"></script><style type="text/css" title="fading circle style">
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
			border-bottom: 2px solid #f12020;
		}
        .van-tab--active span{
            color: #f12020/*主题修改*/;
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
        .mint-loadmore[data-v-35ad745e]{
            background: none;
        }
        .circle {
            font-size:.5rem
            width: .4em;
            height: .4em;
            border-radius: 4em;
            background: #E83338;
            color:#fff;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div class="mint-indicator" id="load" style="display: block;"><div class="mint-indicator-wrapper" style="padding: 20px;z-index:999"><span class="mint-indicator-spin"><div class="mint-spinner-fading-circle circle-color-23" style="width: 32px; height: 32px;"><div class="mint-spinner-fading-circle-circle is-circle2"></div><div class="mint-spinner-fading-circle-circle is-circle3"></div><div class="mint-spinner-fading-circle-circle is-circle4"></div><div class="mint-spinner-fading-circle-circle is-circle5"></div><div class="mint-spinner-fading-circle-circle is-circle6"></div><div class="mint-spinner-fading-circle-circle is-circle7"></div><div class="mint-spinner-fading-circle-circle is-circle8"></div><div class="mint-spinner-fading-circle-circle is-circle9"></div><div class="mint-spinner-fading-circle-circle is-circle10"></div><div class="mint-spinner-fading-circle-circle is-circle11"></div><div class="mint-spinner-fading-circle-circle is-circle12"></div><div class="mint-spinner-fading-circle-circle is-circle13"></div></div></span><span class="mint-indicator-text"style=""><?php echo lang('加载中'); ?>...</span></div><div class="mint-indicator-mask"></div></div><div data-v-35ad745e="" class="main" style="padding-bottom: 55px;"><div data-v-35ad745e="" class="records"><div data-v-35ad745e="" class="jianbian" ></div><div data-v-35ad745e="" class="records_top"><div data-v-35ad745e="" class="records_top1"><p data-v-35ad745e=""><?php echo lang('抢单记录'); ?></p><p data-v-35ad745e="" style="font-size: 0.56rem;"><?php echo htmlentities($balance); ?></p></div><div data-v-35ad745e="" class="records_top1"><p data-v-35ad745e=""><?php echo lang('本数据由'); ?><?php echo sysconf('app_name'); ?><?php echo lang('官方提供'); ?></p><p data-v-35ad745e=""><?php echo lang('剩余可用资产'); ?>(<?php echo lang('美元'); ?>)</p></div></div><div data-v-35ad745e="" class="records_box"><div data-v-35ad745e="" class="mint-loadmore" ><div class="mint-loadmore-content"><div class="mint-loadmore-top"><!----><span class="mint-loadmore-text"><?php echo lang('下拉刷新'); ?></span></div><div data-v-35ad745e="" class="van-tabs van-tabs--line"><div data-v-35ad745e=""><div data-v-35ad745e="" class="van-sticky"><div data-v-35ad745e="" class="van-tabs__wrap van-hairline--top-bottom"><div data-v-35ad745e="" role="tablist" class="van-tabs__nav van-tabs__nav--line" ><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo !$status ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('全部'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == -1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=-1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('待处理'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == 1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('已完成'); ?></span></div><div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo $status == 5 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=5'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('冻结中'); ?></span></div><!-- <div data-v-35ad745e="" class="van-tabs__line"
                                                 style="background-color: rgb(255, 154, 44); transition-duration: 0.3s;"></div> --></div></div></div></div><div data-v-35ad745e="" class="van-tabs__content"><div data-v-35ad745e="" role="tabpanel" class="van-tab__pane"><div data-v-35ad745e="" role="feed" class="van-list"><?php if($list): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;                                        $img= '/static_new6/img/succ.png';
                                        if($v['status']==0) $img= '/static_new6/img/en_dai.png';
                                        if($v['status']==1) $img= '/static_new6/img/en_succ.png';
                                        if($v['status']==4) $img= '/static_new6/img/en_dongjie.png';
                                        if($v['status']==5) $img= '/static_new6/img/en_dongjie.png';
                                        
                                        ?><!--item--><div data-v-35ad745e=""><div data-v-35ad745e="" class="records_tabs"><div data-v-35ad745e="" class="records_tabs_box"><div data-v-35ad745e="" class="records_tabs_box_top"><?php if($v['order_num']!=0): ?><span class="circle"><?php echo htmlentities($v['order_num']); ?></span><?php endif; ?><?php echo lang('抢单时间'); ?>：<?php echo htmlentities(format_datetime($v['addtime'],'Y-m-d H:i:s')); ?><?php echo lang('抢单编号'); ?>：<?php echo htmlentities($v['id']); ?></div><!----><img data-v-35ad745e="" src="<?php echo htmlentities($img); ?>" class="records_tabs_box_img"><div data-v-35ad745e="" class="records_tabs_box_des"><div data-v-35ad745e="" class="tabs_box_des_img"><img data-v-35ad745e="" src="<?php echo htmlentities($v['goods_pic']); ?>"></div><div data-v-35ad745e="" class="tabs_box_des_r"><p data-v-35ad745e="" class="tabs_box_des_r_tit"><?php echo htmlentities($v['goods_name']); ?></p><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['goods_price']); ?></p><p data-v-35ad745e="">x <?php echo htmlentities($v['goods_count']); ?></p></div></div></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo lang('订单总额'); ?></p><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['num']); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo lang('佣金'); ?></p><p data-v-35ad745e=""><?php echo lang('￥'); ?><?php echo htmlentities($v['commission']); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="txt"><?php echo lang('预计返还'); ?></p><p data-v-35ad745e="" class="txt1"><?php if($v['status']==4||$v['status']==5): ?>0<?php else: ?><?php echo lang('￥'); ?><?php echo htmlentities($v['commission']+$v['num']); ?><?php endif; ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic" ><button data-v-35ad745e="" onclick="tijiao('<?php echo htmlentities($v['id']); ?>')" class="redb" style="display: <?php echo $v['status']==0 ? 'block':'none'; ?>;padding: 3px 10px;border-radius: 5px;width: 40%;float:left;"><?php echo lang('提交订单'); ?></button><?php if($balance<$v['num']): ?><p data-v-35ad745e="" class="txt" style="display: <?php echo $v['status']==0 ? 'block':'none'; ?>;width:50%;float:right;color:red" onclick="jump('<?php echo htmlentities($v['num']-$balance); ?>')"><?php echo lang('账户可用余额不足'); ?></p><?php endif; ?></div></div><!----></div></div><!--item--><?php endforeach; endif; else: echo "" ;endif; else: ?><?php endif; if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata"><?php echo lang('没有记录哦'); ?></span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?><div data-v-35ad745e="" class="van-list__placeholder"><div data-v-35ad745e="" class="no_more" style="display: none;"><?php echo lang('没有数据'); ?></div></div></div></div></div></div></div></div></div></div><div data-v-4c2dcb20="" class="" style="" id="orderDetail"><div data-v-4c2dcb20="" class="van-overlay" style="z-index: 1000;padding: 0 10px;display:none ;"><div data-v-4c2dcb20="" class="grabSuccess" style="width: 100%;;"><div data-v-4c2dcb20="" class="title" style="height: 1.586667rem;"><?php if(app('cookie')->get('think_var') == "zh-cn"): ?><img data-v-4c2dcb20="" src="/static_new6/img/ordersucesstitle.png"><?php else: ?><img data-v-4c2dcb20="" src="/static_new6/img/en_ordersucesstitle.png"><?php endif; ?></div><div data-v-4c2dcb20="" class="records_tabs_box"><div data-v-4c2dcb20="" class="records_tabs_box_top"><?php echo lang('抢单时间'); ?>：<span id="otime">0</span><?php echo lang('抢单编号'); ?>：<span id="oid">0</span></div><?php if(app('cookie')->get('think_var') == "zh-cn"): ?><img data-v-4c2dcb20="" src="/static_new6/img/ordersucess.png" class="records_tabs_box_img"><?php else: ?><img data-v-4c2dcb20="" src="/static_new6/img/en_ordersucess.png" class="records_tabs_box_img"><?php endif; ?><div data-v-4c2dcb20="" class="records_tabs_box_des"><div data-v-4c2dcb20="" class="tabs_box_des_img"><img data-v-4c2dcb20="" alt="" id="oimg" src="/static_new6/img/wenhao.png"></div><div data-v-4c2dcb20="" class="tabs_box_des_r"><p data-v-4c2dcb20="" class="tabs_box_des_r_tit" id="otitle">
                                    描述
                                </p><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" id="oprice"><?php echo lang('￥'); ?> 0</p><p data-v-4c2dcb20="" id="onum">x 1</p></div></div></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo lang('订单总额'); ?></p><p data-v-4c2dcb20="" id="ototal"><?php echo lang('￥'); ?> 0</p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo lang('佣金'); ?></p><p data-v-4c2dcb20=""><?php echo lang('￥'); ?><span id="yongjin">8</span></p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="txt"><?php echo lang('预计返还'); ?></p><p data-v-4c2dcb20="" class="txt1"><?php echo lang('￥'); ?><span id="yuji">0</span></p></div></div><div data-v-4c2dcb20="" class="tabs_btn"><div data-v-4c2dcb20="" class="tabs_btn1"><?php echo lang('暂不提交'); ?></div><div data-v-4c2dcb20="" class="tabs_btn2"><?php echo lang('立即提交'); ?></div></div></div><img data-v-4c2dcb20=""
                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFXRSTlMAEFN/o8rX6/sxkf9z88Q9XSlotgUhojDzAAABp0lEQVR4AaSSVwKzIBgEkfYBS1vN/a/690oitnlK1Bmq2mPRxjovIYh31uhFXSImjwGf4ll7yR5AqSnr1mJsTedUCwCflzO6EUBsj/yP2K0AYg4TXQC38iOrA6RPdVbAde7SHVC57+sCyZySBUXv+QnYIg+IG5A+6i+LYHgCE2BfH/wNpfMUvWB7L1iI5km0wL6vv2ieRpdxHzRC5wV6gP7XZ4HhJQwK/wlUbLzIhqr+0CGRF4mCP7d6EWReJkMW9RMDxxs4mD8T6LxB/z2FDMdbOGT1HY91vCfOrxxYvdPjI/jvfoS8pxHysGUB7xMVxB+X2HLA43th9OE5YJF+rKBzYA0/CoMfVg7072tYUP5cokHI49+RWLAojUpOChOfrNDKIHFSmPpMMMoic1qY+DSwykFzVpj51HDKo3FWmPls8ErQOCvMfDaICoicFiY+I8LjwOMlPN7EZ8fY4Z5dpAz7/Cp/Lb0OBAAAABiE+VsPYhB/tWPqOe+h9KX1qe6tN1gebQ/Xx/sLxivOS9Zr3ovmq+7L9uv+B8cnz0fXZ9+HZ6ZvxHfkP9pUwubvmZnSAAAAAElFTkSuQmCC"
                     alt="" class="close"></div></div><link href="/web/css/red.css" rel="stylesheet" /><div data-v-59e03106="" data-v-43c1797b="" class="footer"><ul data-v-59e03106=""><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('index/home'); ?>'"><img data-v-59e03106="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAkCAYAAAAHKVPcAAACrElEQVRYhc2XTWgUMRTHf24r7aX40W5bPQRBxFtEEUQIKli8iV5qwQ8UFD1U0aNWvSjiRehFsCBSwUux+IHgSSsWA+pNAyIiIkSE4k16UOvHSsZk3RZ1M9mt2/9lmJm8937zXpI3mVcqlYiVNnYBcBQ4BiyaYfcaGFRSXIp26BUNoY3tA0YqAt4F3gPzgfXAVv/uLbBTSfGkrhDa2OPAeeAVsFdJ8fQPY9zlEDDkH21RUtyrC4Q2djtwC7ijpNgWAbwCMEArIJQU76rZFKo4bAJuAhMxAE5KCleqNf52NMbmnxDAQZctYHeMswqQl8AwsE4bK2uFcCvhq5JiLA+E11l/3VUrxEogBcBlw60SN+FWVxs7bWL6ZbgZaPKA+4A3wHgCx3dgPzAJ3Kh49kBJMVI5sAyhjXUv+hKCpWhUSbFjGoQ21n39/f8EEFTeR8KcqFq3WdCq4DJALGwARLn3BIiuBkCUYwaIJQ2AKMcMEJ11dP4tclyxDOG73+I6BP/st+pYiHYfO8uEm5T1yMRawPruGaNiWBAFn4W2GgE2KCleAKdz2LSFChQqa5Mgt932KCkeaWMHInrRTGWxm2tYnlPAJiXFY3/fn+CjuxaISV+CZ/zqO0eApQl+stiFBIgPbpsPAF7uF+4EsAe4lsNXtiDyZmJMSdEz86GS4ja/jwSHc/jrChBZXSI1oY11Z47lwCklxcdgpo3tBa7nACDELuSEcL9qg8AB4FMFQH8CQAahjW12EB0JxieVFFMe4AxwMcEHPnaHK0d7gvEVD3DZZyVVLnZnCsRVNxe0sS79vTUA4GMXCzl3OVeCcW3scB0A8LGzcuTRD+BCYgn/pu68EK05umQ0RN6GMxtaNhcgWuYCxJCbExuBc/4c0OKParMpd8T8Ajx3P0FKioc/ARmyuDI8zEVHAAAAAElFTkSuQmCC" alt="" /><img data-v-59e03106="" src="data:image/png;base64,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UmVhZHlxyWU8AAADdmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjFlMmE3MTU1LWVjN2UtZDg0OS05Zjg5LTI0NjgzNzM0YWMwYiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBOEQwMTgyMUQwMTUxMUVDODM3Rjk2NjkzOTQxMDgwMiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBOEQwMTgyMEQwMTUxMUVDODM3Rjk2NjkzOTQxMDgwMiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOCAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNTAxYmU0OC03M2UyLWIwNGUtYWE2Ny03NmQwYzUyMDRkNjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MWUyYTcxNTUtZWM3ZS1kODQ5LTlmODktMjQ2ODM3MzRhYzBiIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Jol0UAAAAohJREFUeNrMmE1IVEEcwNd1Qw+J2rp+hEIn61QYgXjRoPAWdVEhE4OkDhV57IMuRngJOiikSBh0kSSRoIu6UnSwbhZEVIhgBUunwiC1bP0N/IPHgvtm5s3bfX/4Mfp2581v5+M/b15JNpuNRTnisYhHIkjlX82NlRTXYBCqcz7+DPfhQb577P30NW8bJbZDjFwPxZRH5jl8gz3QBqfks1U4C69tBBOWctcphuEj9MObXb56CcZgCTphPvQ5iNwZkXvGrz+UR07FODTDBsxBU6iCyJVSzEAGudOa1dTwH5W/p8PuwYtq3sI5w3ofYBJa4XCYgmrF/qH30hZT946UvWEKHoS0ZVZSq1mljBaTSnnTjKSSE1AqP+Y8rMBLC8FtuADr8NRzbZERmTIWRE5V6inQhjGNZLe2IHKq1xYKvKt1IjmvOwdbYoWPIyaLpKoIgtUmgnVFEKwzEWwogmCDiWCtw4b/an4vZSK4z4HYhmxvuoJJLUFSTJWjHjwGa1Cu24PStu/zoOq9ioBy7fAe3hnUqZC2f/gNcSqAmMr6J+EV3LTY61M6PWibYrbguDw9q7hscY/6sATXZViX5f+rsN9FLow7EPwuW+Oy59oXuAF98NjgXrWuezAtcy43ZqVUx9Irrnuw3uCGGTkTj4iMN7pkRbYGmYNBBXvlcD4Avz3X1QJ5YrNIyIUJP8EaixvfklWsYghGLTNBTW77Cd0txyceSjkhvWkbSVkoGZeCj+CnDGlXwB0omZus4wFPeltygJp0IPe/bd8hNol/cM9yWmit5KCC5QZPK1aCUXyBeSDqgmVRFxzzm4MdcFfOqWXyeiLMUK9VNuEt3Obw/kL73UwUIvJv+XcEGACoc42x3MScJwAAAABJRU5ErkJggg==" alt="" /><p data-v-59e03106=""><?php echo lang('首页'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('order/index'); ?>'"><img data-v-59e03106="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACIAAAAiCAYAAAA6RwvCAAAD10lEQVRYhc2Y32tbZRjHv8973qS/YulwUhActN1msbNFBG3TdWOONeQP8B/wYlUU1itvrXgnXgliZq+8miB4obg2Ti2oS+KPi3VUDWOJgjoYTju3dEt7znm/ctKka5tzktQToV8IJHnf8z6f8z7f5zk/hCQa6bNsscOQcxCMAbAAmIYHPJAC4AJYFpG5xPjgeqPJDUEWswUtkCWCx1sMHhBFvlbC56afHXKCpuhKwO+K3XD5OoDTILsIWUnGh543xowrpcJBeCKnCHccwDcLmcKHAh6DyH1CvoAlc8lnBtb0R1d/7uk20SyAJ6v0EKLD+6qUskJD1Figpbr804AMVP9+CsZM535Yjquue9FZsApRk8DePBFmAPzUBooVMchWf22lR0AoYnTV7jmnFXHCxyVHFrO/HEvGD6+kM9dPEupNgqN7NatIzax4NTEx6KSzBW+NIzsZK0gnNT2wegnofpDOFaYS8cO3ALzwn3ejqvS3hQMkLvhFE0B0tcR8RmWExJXFXHEe5DLA8uYxvuB+4uZHOgGMQXAWgscC5rq6yWKHQL6xxR1GjdtVpensCwWDyF6y0IoarxeQGoEY5xoh50VZJYCRkBA2jRsTcIaij/rlqR5EALrqT7VxfyJxauzvcAA7tfTV1ffXI715wD24e6w+NZ7PLefmdJshPJ06MfoX4N70G/NPjWDkUq4wYxw1nzg+0GoDa6iLmV+VLZjpAEf8vOIPYkQcSkosM7uYLawBCOkR2EoQ6wQeZ4BhA/uISMVQwyEBdqhRK9k3faRBZ/W2kHkAbUkNgB4KhiVgW3xBqEht8BKNmp+ebI9Z05miitg8a0f4butmJX48MzF0vh0ANSXig94JpRay118RYGT3eL1HpHIt7L+0tHygnSCePv8+/7BL3e9XN/U74jU0ZR4xHV25dPbaexB9N3yLhy0wD4HWjBZz0M8mAWYlqPRREG+hyeNGqzLYvP2VgCIOrpo2AdQUBFDT/u8jBC4r4B2CdwBEQ8bZEEgvgZcBTO4BRN1ITgyEf7Cq14WFbPEPAR+ti1g/V2BvlG//DxAVade+41++Qg1uH/LqV564kv9t7m5Z3l79p7RmWSpU+SrX2Ou9sdhazJrtpwzX21a0pqttsRxsh4lGo/j91vprSnAuGo2UG1+TmouW5XTfK3fGSuyztd5VQZVbQlu75c5Por23k8Z5cNLeGwJtVcD6wgBsCwWjpPLZXcYUC9qUPlZWtJQyTuQiRbX5rr0JmtDr4IBxPj0zOZraej+Svpx/EaJOU3Sft1fNH4nCUNAybmRVKF8mpw6lAOBfbgtu9h5byeoAAAAASUVORK5CYII=" alt="" /><img data-v-59e03106="" src="data:image/png;base64,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UmVhZHlxyWU8AAADdmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjFlMmE3MTU1LWVjN2UtZDg0OS05Zjg5LTI0NjgzNzM0YWMwYiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQUVBNEIzM0QwMTUxMUVDODQ1NzhFMThEMTk0MjgzQSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQUVBNEIzMkQwMTUxMUVDODQ1NzhFMThEMTk0MjgzQSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOCAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNTAxYmU0OC03M2UyLWIwNGUtYWE2Ny03NmQwYzUyMDRkNjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MWUyYTcxNTUtZWM3ZS1kODQ5LTlmODktMjQ2ODM3MzRhYzBiIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+RRpsSAAAAnxJREFUeNrsmM1vTFEYh+dOS9GpTKMiESQW/YiWRqzQkpBI/AH+AQslFqxsEYmFWEmafq2sSOwIK2y0WJJMRbpGIj5TGkVnrueNV1I350xn7nmHyWRO8uRMp/ee+7vv55wTxXGcqeeRzdT5qHuBrdVc/LVnSxvTBRiEFihVYYgiPNf7v//5R27ulY1AxMm192EowCBHYT8cgqVKboiSSYKQdUwX4TCshQJveYzvRdgjI88NwzTcggH4Bg/EujxrwWtBRLQzPYGdy75u07nFMLQinffAdv28G46gYd9ykckkOZsQJ+Onzo/hhYG4ghoh43DzLjhTLgYPOBbs5q0GeKsC80H+vqILpU2ScypM1uh2XCvPuOwTGHnccRNxw4h8z+fjibBIY8VOuLGC+50Ci56b+uEZYqbUCou6UFShoFhZoyXqBGz1XFtMWwe3waVmJ2nUVjcHE5ITsCrwmVK2cjACPRYC38FeMvijpwWmFXodXkJXqIvf+sQFjg+ytkUM9mOlEcgax/4pLV/BLpZaNy5tEJELhjHYa/17sK9ZZgItKBln5eL2Sj1SicBYA3qKbC4ZlZms9uMxCxfPImzCJS5glDTxZi0EbsJKnTUIrw2ytoWLN8JTRE4yfzGKwQ5tdV1WSSI982qzzDRyHZyBUZiH1YHP/AHr4bRu4oMFvqHEDJU5cUgrVDZNr2FzqIs/19CD89Va0GXRHVhJDnyuGba6nB4S9K2kqdVzipAc53XHv1hl/3aNJd1+5su8gFfgHT2Bco38P0rc2+ViUPrjvf9YVe6SkON//Vp2nVETcyczv4/f8rrTr9VBdqRnPJ/gYVKcV2CzkzSSwF8CDADozqbU1+G99QAAAABJRU5ErkJggg==" alt="" /><p data-v-59e03106=""><?php echo lang('记录'); ?></p></li><li data-v-59e03106="" class="t" onclick="window.location.href='<?php echo url('rot_order/index'); ?>'"><img data-v-59e03106="" src="<?php echo lang('/public/footico.png'); ?>" alt="" /><img data-v-59e03106="" src="<?php echo lang('/public/footico.png'); ?>" alt="" /></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('support/index'); ?>'"><img data-v-59e03106="" src="data:image/png;base64,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" alt="" /><img data-v-59e03106="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAAoCAYAAABjPNNTAAAKQ2lDQ1BJQ0MgcHJvZmlsZQAAeNqdU3dYk/cWPt/3ZQ9WQtjwsZdsgQAiI6wIyBBZohCSAGGEEBJAxYWIClYUFRGcSFXEgtUKSJ2I4qAouGdBiohai1VcOO4f3Ke1fXrv7e371/u855zn/M55zw+AERImkeaiagA5UoU8Otgfj09IxMm9gAIVSOAEIBDmy8JnBcUAAPADeXh+dLA//AGvbwACAHDVLiQSx+H/g7pQJlcAIJEA4CIS5wsBkFIAyC5UyBQAyBgAsFOzZAoAlAAAbHl8QiIAqg0A7PRJPgUA2KmT3BcA2KIcqQgAjQEAmShHJAJAuwBgVYFSLALAwgCgrEAiLgTArgGAWbYyRwKAvQUAdo5YkA9AYACAmUIszAAgOAIAQx4TzQMgTAOgMNK/4KlfcIW4SAEAwMuVzZdL0jMUuJXQGnfy8ODiIeLCbLFCYRcpEGYJ5CKcl5sjE0jnA0zODAAAGvnRwf44P5Dn5uTh5mbnbO/0xaL+a/BvIj4h8d/+vIwCBAAQTs/v2l/l5dYDcMcBsHW/a6lbANpWAGjf+V0z2wmgWgrQevmLeTj8QB6eoVDIPB0cCgsL7SViob0w44s+/zPhb+CLfvb8QB7+23rwAHGaQJmtwKOD/XFhbnauUo7nywRCMW735yP+x4V//Y4p0eI0sVwsFYrxWIm4UCJNx3m5UpFEIcmV4hLpfzLxH5b9CZN3DQCshk/ATrYHtctswH7uAQKLDljSdgBAfvMtjBoLkQAQZzQyefcAAJO/+Y9AKwEAzZek4wAAvOgYXKiUF0zGCAAARKCBKrBBBwzBFKzADpzBHbzAFwJhBkRADCTAPBBCBuSAHAqhGJZBGVTAOtgEtbADGqARmuEQtMExOA3n4BJcgetwFwZgGJ7CGLyGCQRByAgTYSE6iBFijtgizggXmY4EImFINJKApCDpiBRRIsXIcqQCqUJqkV1II/ItchQ5jVxA+pDbyCAyivyKvEcxlIGyUQPUAnVAuagfGorGoHPRdDQPXYCWomvRGrQePYC2oqfRS+h1dAB9io5jgNExDmaM2WFcjIdFYIlYGibHFmPlWDVWjzVjHVg3dhUbwJ5h7wgkAouAE+wIXoQQwmyCkJBHWExYQ6gl7CO0EroIVwmDhDHCJyKTqE+0JXoS+cR4YjqxkFhGrCbuIR4hniVeJw4TX5NIJA7JkuROCiElkDJJC0lrSNtILaRTpD7SEGmcTCbrkG3J3uQIsoCsIJeRt5APkE+S+8nD5LcUOsWI4kwJoiRSpJQSSjVlP+UEpZ8yQpmgqlHNqZ7UCKqIOp9aSW2gdlAvU4epEzR1miXNmxZDy6Qto9XQmmlnafdoL+l0ugndgx5Fl9CX0mvoB+nn6YP0dwwNhg2Dx0hiKBlrGXsZpxi3GS+ZTKYF05eZyFQw1zIbmWeYD5hvVVgq9ip8FZHKEpU6lVaVfpXnqlRVc1U/1XmqC1SrVQ+rXlZ9pkZVs1DjqQnUFqvVqR1Vu6k2rs5Sd1KPUM9RX6O+X/2C+mMNsoaFRqCGSKNUY7fGGY0hFsYyZfFYQtZyVgPrLGuYTWJbsvnsTHYF+xt2L3tMU0NzqmasZpFmneZxzQEOxrHg8DnZnErOIc4NznstAy0/LbHWaq1mrX6tN9p62r7aYu1y7Rbt69rvdXCdQJ0snfU6bTr3dQm6NrpRuoW623XP6j7TY+t56Qn1yvUO6d3RR/Vt9KP1F+rv1u/RHzcwNAg2kBlsMThj8MyQY+hrmGm40fCE4agRy2i6kcRoo9FJoye4Ju6HZ+M1eBc+ZqxvHGKsNN5l3Gs8YWJpMtukxKTF5L4pzZRrmma60bTTdMzMyCzcrNisyeyOOdWca55hvtm82/yNhaVFnMVKizaLx5balnzLBZZNlvesmFY+VnlW9VbXrEnWXOss623WV2xQG1ebDJs6m8u2qK2brcR2m23fFOIUjynSKfVTbtox7PzsCuya7AbtOfZh9iX2bfbPHcwcEh3WO3Q7fHJ0dcx2bHC866ThNMOpxKnD6VdnG2ehc53zNRemS5DLEpd2lxdTbaeKp26fesuV5RruutK10/Wjm7ub3K3ZbdTdzD3Ffav7TS6bG8ldwz3vQfTw91jicczjnaebp8LzkOcvXnZeWV77vR5Ps5wmntYwbcjbxFvgvct7YDo+PWX6zukDPsY+Ap96n4e+pr4i3z2+I37Wfpl+B/ye+zv6y/2P+L/hefIW8U4FYAHBAeUBvYEagbMDawMfBJkEpQc1BY0FuwYvDD4VQgwJDVkfcpNvwBfyG/ljM9xnLJrRFcoInRVaG/owzCZMHtYRjobPCN8Qfm+m+UzpzLYIiOBHbIi4H2kZmRf5fRQpKjKqLupRtFN0cXT3LNas5Fn7Z72O8Y+pjLk722q2cnZnrGpsUmxj7Ju4gLiquIF4h/hF8ZcSdBMkCe2J5MTYxD2J43MC52yaM5zkmlSWdGOu5dyiuRfm6c7Lnnc8WTVZkHw4hZgSl7I/5YMgQlAvGE/lp25NHRPyhJuFT0W+oo2iUbG3uEo8kuadVpX2ON07fUP6aIZPRnXGMwlPUit5kRmSuSPzTVZE1t6sz9lx2S05lJyUnKNSDWmWtCvXMLcot09mKyuTDeR55m3KG5OHyvfkI/lz89sVbIVM0aO0Uq5QDhZML6greFsYW3i4SL1IWtQz32b+6vkjC4IWfL2QsFC4sLPYuHhZ8eAiv0W7FiOLUxd3LjFdUrpkeGnw0n3LaMuylv1Q4lhSVfJqedzyjlKD0qWlQyuCVzSVqZTJy26u9Fq5YxVhlWRV72qX1VtWfyoXlV+scKyorviwRrjm4ldOX9V89Xlt2treSrfK7etI66Trbqz3Wb+vSr1qQdXQhvANrRvxjeUbX21K3nShemr1js20zcrNAzVhNe1bzLas2/KhNqP2ep1/XctW/a2rt77ZJtrWv913e/MOgx0VO97vlOy8tSt4V2u9RX31btLugt2PGmIbur/mft24R3dPxZ6Pe6V7B/ZF7+tqdG9s3K+/v7IJbVI2jR5IOnDlm4Bv2pvtmne1cFoqDsJB5cEn36Z8e+NQ6KHOw9zDzd+Zf7f1COtIeSvSOr91rC2jbaA9ob3v6IyjnR1eHUe+t/9+7zHjY3XHNY9XnqCdKD3x+eSCk+OnZKeenU4/PdSZ3Hn3TPyZa11RXb1nQ8+ePxd07ky3X/fJ897nj13wvHD0Ivdi2yW3S609rj1HfnD94UivW2/rZffL7Vc8rnT0Tes70e/Tf/pqwNVz1/jXLl2feb3vxuwbt24m3Ry4Jbr1+Hb27Rd3Cu5M3F16j3iv/L7a/eoH+g/qf7T+sWXAbeD4YMBgz8NZD+8OCYee/pT/04fh0kfMR9UjRiONj50fHxsNGr3yZM6T4aeypxPPyn5W/3nrc6vn3/3i+0vPWPzY8Av5i8+/rnmp83Lvq6mvOscjxx+8znk98ab8rc7bfe+477rfx70fmSj8QP5Q89H6Y8en0E/3Pud8/vwv94Tz+4A5JREAAAAZdEVYdFNvZnR3YXJlAEFkb2JlIEltYWdlUmVhZHlxyWU8AAADdmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjFlMmE3MTU1LWVjN2UtZDg0OS05Zjg5LTI0NjgzNzM0YWMwYiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQzA0RjVFM0QwMTUxMUVDQkI5NUQ5QTdGNDBENjMzMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQzA0RjVFMkQwMTUxMUVDQkI5NUQ5QTdGNDBENjMzMSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOCAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNTAxYmU0OC03M2UyLWIwNGUtYWE2Ny03NmQwYzUyMDRkNjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MWUyYTcxNTUtZWM3ZS1kODQ5LTlmODktMjQ2ODM3MzRhYzBiIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+S6LxMAAABFxJREFUeNrsmGlIVFEUx50ptdSyxaKVsjSCrEgp0hb0Q2nZStG+UKkUIgQFlZURBRFRQV/akDbNihZaMI2kBdsXyi+hVq5ljJmVYmqL/Q/8X1xeM86Ms5TUgR/z3n3v3fufu5xz7jU0NTV5/O1m9GgF1ipEGmqCezvyfTvQAwSC7kTK6oGJFIF3LPvN/ArKrTbStoXi+oNIMA6MBYOaebcA5ILb4CYocXVP9gJzQQIYrJQ3gCpQB77yz/uALqC98t4LcAicBhW29qQ9IieDLWCUUvYU3AN3QB4brqMwmQYhYAyIAGHKGngAtoKrzhSZDDYpvXKdvZEOvuje9WRv6svmg3lgEsvku20QucMZIveC1byWBbAL7FGej2MvhYLOwAs0gmrwhOQq768C60A/3u+B0DWOiFQFPgSJ4DHvF3F+hoOuzdRRxSlxij0vNhzsA+N5vxtC17ZEpCyOg7y+BRaCNyAY7AQz7VykEtougPWgkO7qOIjm8zgITbVHpCyOa8CfC2I6KAYTwX4wwAHf+pIjIvX3BOfBaE6PaAh9ZEvEEWe8kQJrONwiMIbD5YhAsSDWM5neIAlUcj4n1w7q422LyBlgKq9lkdzgojgMApwU6QJYXyh6Tub4dpbLiE2zJrId3YSBw3yE5Smgj5NDsgSGzbzOAHfZ7nz0pldzIoeACbw+Acq5gie6KHeIhqB56M1KtufBtkIsiQxgV/vQH17iP0vShTZnmtSbCKGiIxO8Ar6iA2X+epHSe2fABt7fY2IgIW2YizMxqT8CvVmK3/tKhDsGoWGayKEMcVEMX2LP+BsLOrhYZEcwRYnpWhiVRXQRQgeKyOVc/qrl8TfETXmt1k4++K6UixOPM+qyGi3tMikvucN68fcDqNU9izCa6cUGZtHenMTuMD8Mqw/TvAa9TxWRbcz4TiMTV3ftgbT2DGbabGtk6NO7BV/mew1uElnPYfYz4+4+GemwVWvD1OsH54g7rBouSNrrRD+tWpGIzDHzUSB/y8ykW6X0oS2xAtapP5EoVRaqQfcsS0Seo7f/xsI6plNiz3UffARLuWfJtFNgJr+T7z/pnml+uUSZfqLnLLgsIt+CJYw2B8BKhkSxKwyRv/JPJquSbS9T3rNmUs8KftdN11sVfC47xxy2n8p0MQFl723Z46QxK9fsNTdVD3m/DcyxsPcuZDRL4RCP5DZCzUnTIGSxoxuxEdzY+yllMq/iQbbijGczcvhyyuRxu6AtzBjuufsq9cjQjofIZ87Y0m5n96vWyL1OBjf9lkwOERZwOulPTGRLm+Ksfbc3F1ishT1LNg8IKulbvTn3wtmDwRbm6SyIbHTmCUZ3zq/IZt75zFXpaSV7usEdgMmWEwx7wp6JC+SklbSrixWB6cz2Ta46n5ThjKP7sdeh5/O7eNZjs7Xk6E9i+lEeGMSQKAu995lDm0WK/8QhqjbEQXQtPSm2hk66jL6yxmKOZsup2v+D/X9J5E8BBgCL0C6w/4hu1gAAAABJRU5ErkJggg==" alt="" /><p data-v-59e03106=""><?php echo lang('在线'); ?></p></li><li data-v-59e03106="" class="" onclick="window.location.href='<?php echo url('my/index'); ?>'"><img data-v-59e03106="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAjCAYAAACHIWrsAAADoElEQVRIib2WTWhUVxTH/+e8+bC2tn60BGuh7QuxUhHBj5iJqVhi68TY1I2lFKy2Gze6UAQ3QnXRnRLElkIp0i5KoQsVo5kX+kGLTV4SaGlXkjoTAlqEgq0xkq95955y38yY6XMm8ybJ5A+zmHvfPb977sf/XBIRhJHTl3kHhDYACQCrAQgBt5XQDQiu7dlud4eJUxHouJlGAJ0AmmcPhJQAx5LN9UNzBqbczD4CLlec9owmNNGuPU12X9VApze9EUy/VwHzpYkexrzpdbta1v1Vqp/LjmT6ulqYEQmeUmxdLBu2VGOqb/g9AOvnBIRAE7/Z46ZfCw0E4dBcYDPDzdLyh6GAjpt+hiCb5wNE7nBsSfVnHotfIkOqA7ByvkAAz5PI8hBARBcAlosjFAs2RoINRBgVgVeqz7gLgHtFfVkAK8p8+y8Io8HGxzIUobsASrnF3yR4RStlZz3dYH5KqZcE2JEHB2ZGQ8lE/UTFDJMJW13/ZdiJRGR9sScIKGqJ98/ulrVjAMYK7Y6bWQLACsYhka4Sky59LYhxPmhABFmhmW44bmZroe26O9IB4NL/4hDAWkaJo1+WjF3O2roH0h9Zik6bklAsAYGh+4gkooQbA92IT3m4s2b5Bwca6qoDGvW4w10CvTd3lWck+VQIM2NJxNgaYpPTn+9sffVwuZjlvTSnDoC/kgCQ8hZWDBMiKIvOzgarmGFBzuBIByvviAa1mi0yRDOMfLR40ay+dvuFlRcO2qt+rBQrdMU36u69FSdgmxCtgfgVf4REBpMtDTpsjFmBTv/wMhZvK7ypZz1rWQQyPS5C4+beEfl7GQNkKcBP3H/uaW/VvbG7IAwmm+qnqgJ+N/BngxbrhAh2A3jR3BOIQvDwFCviKSjLMmnfEpIuFdOdezetvVMR2NN786Rw9AxA8TBLVDYT4L6n6VT7dvvTssCUm/6GYL0LhN6SirKEPnmj2T5a+O7RtXDczBcALSjMSANHUgPpjwv//Qyv/jqy/8nx7LfZSKVrOTeJcQThnW0J+2c+92DSIi2dQQtbSJEQWOOsn+EPP918OxuNXtHsv7hqKhHdyNPxaJvJrtYw35WY2pkEG2qLyklMnRHawALULQYwr9XmWHqLCPRYi/XHIgJ/4zirzvwK14ziv8TByII+49am+n4GjhfqeC1oCoylPHngrYQ99MhLv+8fen1Sxd6PsDaPJPOAna/HmUI9AU9cpa2L7TteHgSA/wDhKneNi625EAAAAABJRU5ErkJggg==" alt="" /><img data-v-59e03106="" src="data:image/png;base64,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UmVhZHlxyWU8AAADdmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjFlMmE3MTU1LWVjN2UtZDg0OS05Zjg5LTI0NjgzNzM0YWMwYiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBOURFODU3MkQwMTUxMUVDQjgxMEU4RUEzODIxNjYzOSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBOURFODU3MUQwMTUxMUVDQjgxMEU4RUEzODIxNjYzOSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOCAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNTAxYmU0OC03M2UyLWIwNGUtYWE2Ny03NmQwYzUyMDRkNjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MWUyYTcxNTUtZWM3ZS1kODQ5LTlmODktMjQ2ODM3MzRhYzBiIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+d+UoQwAAAnxJREFUeNrMmE1IVFEUgGccswyjJgQZCxShIkJa5CgKRREUkkQbI4RC3LRpUSK0cWELwcXAICVCiORCghZFFP1sggipXBgtrU2QEES/GEb0avouHGGI9/Td++69zIWPAzPv3ffN/Tn3vEmXSqVUJbeqVIW3ihesTtrBj907TxO6oRNyoNbMe3gG9+HBWvfXvVlas/+06RpErJ1QhK51Ln0Il2DRRLDKUO4U4WUMuZSM7quY1yYXRG4/4Y7mbbXwGHb42CSzhsu1DqadCjJ6fYR9CfbUMTjocgT7LWSOASeCjN5WwgELgm06z9UZwQbYbkGwEba5ENxg6XBQ/dS4OEm+QxBxj8r2n8u++w3ZiGu/Sl/WR/BDxGnwEfZAC+wSmuGQiP7fVB8/rQtyJP0hPIqYsi+wDJ+EFdgEmZDr77lMM+Mhn2WlMMiXfXYSbof0r6b2hjNBRlFVKSMhX+2FeZiTM/puxE69KCMduxlVM+RENU09mrddh/NeqhmZwhmN6wthcs5GsGwklegFOBryYwMpWK/CE+8Fa5mkChuhQ8op1eE7WZN/Y6xrJ2twi+zaeknGK8Jq3lMnxWapAwPJoUr4l65gtaaYSsJDcByaNH/XW8mB6jVhyfoaRO4y4YpMZ5L2DYZhwtouRu4mYcyCXEry4zXZPMkTNXJThDMOXnnV7h9NNMXI9RJuOX43P8w0P9UeQeQysqBdt4LpFPeYvCaavAIwGHkTwW6Pf8GcMBFs9SjYaiLY4FEwZyIYeBQMTARfexRcMBEsehSc1BYkeb4gDHqQO8uzFo2LBXLUEcI5Ka9q4tR4MY5X9dr5HKaRm3dasLpuFf8n+j8BBgBW8KmNg4IXMAAAAABJRU5ErkJggg==" alt="" /><p data-v-59e03106=""><?php echo lang('我的'); ?></p></li></ul></div><script>
		 function updateactivetime(){
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
    </script></div></div><script type="text/javascript" src="/static_new6/js/manifest.3ad1d5771e9b13dbdad2.js"></script><!--<script type="text/javascript" src="/static_new6/js/vendor.832c1df07e64f8264dc6.js"></script>--><!--<script type="text/javascript" src="/static_new6/js/app.1ef0c51eb4b45b9f8d06.js"></script>--><script>
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