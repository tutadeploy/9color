<?php /*a:1:{s:60:"D:\project\sd\application\index\view\ctrl\deposit-black.html";i:1657581258;}*/ ?>
<!DOCTYPE html><html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, viewport-fit=cover"><title><?php echo lang('提现'); ?></title><link rel="stylesheet" href="/static_new/css/public.css"><link rel="stylesheet" href="/static_new/css/icon-font.css"><script src="/static_new/js/pack.js"></script><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><style>
        body {
            background-color:  #121212;
            margin-bottom: 65px;
        }

        .header {
            width: 100%;
            color: #fff!important;
            top: 0;
            z-index: 99;
            position: relative;
            background: #121212!important;
        }

        .header > p {
            width: 96%;
            margin: 0 2%;
            height: 46px;
            line-height: 46px;
            text-align: center;
        }

        .my-meun {
            overflow: hidden;
            margin-right: 15px;
            margin-left: 15px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .my-meun > .meun-item {
            font-size: 15px;
            position: relative;
            padding: 0 15px;
            height: 50px;
            background-color: #1D1D1F;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .my-meun > .meun-item > .item-line {
            border-bottom: 1px solid #f6f4f4
        }

        .my-meun > .meun-item > .item-line > span {
            font-size: 10px;
            line-height: 50px;
            color: #e5e9ec;
        }

        .my-meun > .meun-item > .item-line > icon {
            color: #000;
            position: absolute;
            right: 15px;
            top: 17px;
        }

        .item-line > input {
            border: none;
            position: absolute;
            right: 15px;
            top: 14px;
            margin-top: 2px;
            text-align: right;
            font-size: 15px;
            background: #1D1D1F;
            color: white;
        }

        .item-line > input:-moz-placeholder {
            color: #f6f4f4;
            font-size: 14px;
            text-align: right;
        }

        .item-line > input::-moz-placeholder {
            color: #f6f4f4;
            font-size: 14px;
            text-align: right;
        }

        .item-line > input:-ms-input-placeholder, textarea:-ms-input-placeholder {
            color: #f6f4f4;
            font-size: 14px;
            text-align: right;
        }

        .item-line > input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
            color: #f6f4f4;
            font-size: 14px;
            text-align: right;
        }

        .save-btn {
            margin: 20px 15px;
            padding: 10px;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            background: #2f3848!important;
        }

        .money-bg {
            margin-right: 15px;
            margin-left: 15px;
            border-radius: 10px;
            margin-top: 10px;
            background-color: #1D1D1F;
            position: relative;
            color: white;
        }

        .money-bg > p {
            padding: 10px;
        }

        .money-bg > input {
            font-size: 18px;
            border: none;
            width: 70%;
            margin: 10px 10px 20px 50px;
            background-color: #1D1D1F;
            color: #fff;
        }

        .money-bg > span {
            position: absolute;
            left: 20px;
            bottom: 55px;
            font-size: 22px;
        }

        .balance {
            color: #fffefe;
            font-size: 12px;
        }
        .balance  a {
            color: #fffefe;
            font-size: 12px;
        }

        .rate {
            
            /**border: 1px solid #bbb8ae;**/
            font-size: 12px;
            padding: 1px 6px;
            border-radius: 2500px;
            color: #fbbd08;
            margin-left: 5px;
        }
        .on{
            
            border: 1px solid #fbbd08;
        }

        .right {
            float: right;
        }
    </style><link rel="stylesheet" href="/static_new/css/theme.css"><script>
        (function () {
            var dw = document.createElement("script");
            dw.src = "/static_new/js/pack.js?e9154e78c011e7e0eba17228a1621937"
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(dw, s);
        })()
    </script><script type="application/javascript">
        window.onpageshow = function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script></head><body id="app"><div class="header" style="background: #fff;"><div class="goback" style="color: #464646!important"><span class="icon iconfont icon-fanhui"></span><a href="javascript:history.go(-1)" style="color: #dddada!important"><?php echo lang('返回'); ?></a></div><p><?php echo lang('提现'); ?></p></div><form id="login-form"><div class="money-bg"><p><?php echo lang('提现金额'); ?></p><p><span class="rate on" onclick="change('bank')" id="bankrate"><?php echo lang('银行卡'); ?></span><span class="rate " onclick="change('trc')" id="trcrate"> TRC-20 rate 1U</span><span class="rate" onclick="change('erc')" id="ercrate"> ERC-20 rate 10U</span></p><span><?php echo lang('￥'); ?></span><input name="num" id="num" type="number" placeholder="<?php echo lang('请输入提现金额'); ?>"><p class="balance"><?php echo lang('余额'); ?>：<?php echo lang('￥'); ?><?php echo htmlentities($user['balance']); ?><a href="javascript:void(0)" onclick="return tixianAll(<?php echo htmlentities($user['balance']); ?>)" style="margin-left:20px"><?php echo lang('全部提现'); ?></a></p></div><div class="my-meun"><div class="meun-item" id="TRC20" style="display:none"><div class="item-line"><span>USDT-TRC20</span><input type="text" placeholder="<?php echo lang('请输入TRC20地址'); ?>"  value="<?php echo htmlentities((isset($user['trc20']) && ($user['trc20'] !== '')?$user['trc20']:'')); ?>" name="trc20_address" autocomplete="off" ></div></div><div class="meun-item" id="ERC20" style="display:none"><div class="item-line"><span>USDT-ERC20</span><input type="text" placeholder="<?php echo lang('请输入ERC20地址'); ?>" value="<?php echo htmlentities((isset($user['erc20']) && ($user['erc20'] !== '')?$user['erc20']:'')); ?>" name="erc20_address" autocomplete="off" ></div></div><div class="meun-item" id="bankname" style="display:block"><div class="item-line"><span><?php echo lang('所属银行'); ?></span><input type="text"  value="<?php echo htmlentities((isset($info['bankname']) && ($info['bankname'] !== '')?$info['bankname']:'')); ?>" name="bankname" autocomplete="off" readonly=""></div></div><div class="meun-item" id="username" style="display:block"><div class="item-line"><span><?php echo lang('开户名'); ?></span><input type="text" value="<?php echo htmlentities((isset($info['username']) && ($info['username'] !== '')?$info['username']:'')); ?>" name="username" autocomplete="off" readonly=""></div></div><div class="meun-item" id="cardnum" style="display:block"><div class="item-line"><span id="bankname"><?php echo lang('银行卡号'); ?></span><input type="text" value="<?php echo htmlentities((isset($info['cardnum']) && ($info['cardnum'] !== '')?$info['cardnum']:'')); ?>" name="cardnum" autocomplete="off" readonly=""></div></div><div class="meun-item"><div class="item-line"><span><?php echo lang('资金密码'); ?></span><input type="password" placeholder="<?php echo lang('输入资金密码'); ?>" name="paypassword" autocomplete="off"></div></div></div><div class="money-bg"><p style="font-size: 11px">* <br><?php echo lang('请仔细核对收款信息'); ?><br><!--    <?php echo lang('本次提现扣除手续费'); ?><?php echo htmlentities($shouxu*100); ?>%--></p></div><input type="hidden" name="type" value="card"  id="type"></form><div class="save-btn"><?php echo lang('立刻提现'); ?></div><script type="application/javascript">
var bankname='<?php echo htmlentities($info['bankname']); ?>';
if (isNull(bankname)) {
                            $(document).dialog({infoText: '<?php echo lang('还没添加银行卡信息'); ?>!'});
                            setTimeout(function () {
                                window.location.href = '/index/my/bind_bank';
                            }, 2000);
}

    function tixianAll(price)
    {
        $('#num').val(price);
    }

    $(function () {
        if ($("input[name=trc20_address]").val() == '****') {
            loading = $(document).dialog({
                type: 'notice',
                infoIcon: '/static_new/img/loading.gif',
                infoText: '<?php echo lang('跳转中'); ?>....',
                autoClose: 0
            });
            $(document).dialog({infoText: '<?php echo lang('未绑定TRC20,前往绑定'); ?>!'});
            setTimeout(function () {
                window.location.href = '/index/my/bind_trc20';
            }, 2000);
        }
        /*检查表单*/
        function check() {
            if($("input[name=type]").val()!='card'){
              if ($("input[name=trc20_address]").val() == ''&& $("input[name=erc20_address]").val() == '') {
                $(document).dialog({infoText: '<?php echo lang('输入TRC20'); ?>'});
                return false;
            }  
            }else{
             if (isNull(bankname)) {
                            $(document).dialog({infoText: '<?php echo lang('还没添加银行卡信息'); ?>!'});
                            setTimeout(function () {
                                window.location.href = '/index/my/bind_bank';
                            }, 2000);
             }
            }
            if ($("input[name=paypassword]").val() == '') {
                $(document).dialog({infoText: '<?php echo lang('输入资金密码'); ?>'});
                return false;
            }
            return true;
        }

        /*点击登录*/
        $(".save-btn").on('click', function () {
            if (check()) {
                var loading = null;
                $.ajax({
                    url: '/index/ctrl/do_deposit',
                    data: $("#login-form").serialize(),
                    type: 'POST',
                    beforeSend: function () {
                        loading = $(document).dialog({
                            type: 'notice',
                            infoIcon: '/static_new/img/loading.gif',
                            infoText: '<?php echo lang('正在加载中'); ?>',
                            autoClose: 0
                        });
                    },
                    success: function (data) {
                        console.log(data);
                        loading.close();
                        if (data.code == 0) {
                            $(document).dialog({infoText: '<?php echo lang('提交成功,请耐心等待审核'); ?>!'});
                            setTimeout(function () {
                                window.location.href = '/index/my/index';
                            }, 2000);
                        } else if(data.code == 2){
                            $(document).dialog({infoText: data.info});
                            setTimeout(function () {
                                window.location.href = '/index/ctrl/edit_deposit_pwd';
                            }, 2000);
                        }else {
                            $(document).dialog({infoText: data.info});
                        }
                    }
                });
            }
        })
    })
    
    function change(type){
        if(type=="trc"){
            var type = document.getElementById("type");
             type.value = "trc";
            $("#trcrate").addClass("on");
            $("#ercrate").removeClass("on");
            $("#bankrate").removeClass("on");
            $("#cardnum").hide();
            $("#bankname").hide();
            $("#username").hide();
            $("#TRC20").show();
            $("#ERC20").hide();
            
        }
        if(type=="erc"){
            var type = document.getElementById("type");
             type.value = "erc"; 
            $("#ercrate").addClass("on");
            $("#trcrate").removeClass("on");
            $("#bankrate").removeClass("on");
            
            $("#cardnum").hide();
            $("#bankname").hide();
            $("#username").hide();
            $("#ERC20").show();
            $("#TRC20").hide();
        }
        if(type=="bank"){
            
             var type = document.getElementById("type");
             type.value = "card"; 
             
            $("#bankrate").addClass("on");
            $("#ercrate").removeClass("on");
            $("#trcrate").removeClass("on");
            $("#ERC20").hide();
            $("#TRC20").hide();
            $("#cardnum").show();
            $("#bankname").show();
            $("#username").show();
        }
        
    }
function isNull( str ){
if ( str == "" ) return true;
var regu = "^[ ]+$";
var re = new RegExp(regu);
return re.test(str);
}
</script></body></html>