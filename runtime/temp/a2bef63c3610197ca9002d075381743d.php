<?php /*a:1:{s:58:"D:\project\sd\application\admin\view\deal\add_user_cj.html";i:1629356278;}*/ ?>
<form class="layui-form layui-card" action="<?php echo request()->url(); ?>" data-auto="true" method="post" autocomplete="off"><div class="layui-card-body"><div class="layui-form-item"><label class="layui-form-label label-required">赠送彩金</label><div class="layui-input-block"><input name="balance" type="number" min="0" required placeholder="请输入赠送彩金,账户总余额<?php echo htmlentities($info['balance']); ?>" class="layui-input">
                提示:请输入赠送彩金
            </div></div></div><input name="id" type="hidden" value="<?php echo htmlentities($info['id']); ?>"><div class="hr-line-dashed"></div><div class="layui-form-item text-center"><button class="layui-btn" type='submit'>提交</button><button class="layui-btn layui-btn-danger" type='button' data-close>取消</button></div><script>
        var test = "<?php echo htmlentities((isset($info['kouchu_balance_uid']) && ($info['kouchu_balance_uid'] !== '')?$info['kouchu_balance_uid']:'0')); ?>";
        $("#selectList").find("option[value="+test+"]").prop("selected",true);

        window.form.render();
    </script></form>
