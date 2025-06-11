<?php /*a:2:{s:57:"D:\project\sd\application\admin\view\shop\edit_goods.html";i:1623050074;s:46:"D:\project\sd\application\admin\view\main.html";i:1615942452;}*/ ?>
<div class="layui-card layui-bg-gray"><style>        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><form onsubmit="return false;" id="GoodsForm" data-auto="true" method="post" class='layui-form layui-card' autocomplete="off"><div class="layui-card-body think-box-shadow padding-left-40"><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">商品名称</span><input name="goods_name" required class="layui-input" value="<?php echo htmlentities($info['goods_name']); ?>" placeholder="请输入商品名称"><input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>"><input type="hidden" name="_csrf_" value="<?php echo systoken('admin/shop/edit_goods'); ?>"></label></div><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">商品名称(英文)</span><input name="en_goods_name" required class="layui-input" value="<?php echo htmlentities($info['en_goods_name']); ?>" placeholder="请输入商品名称"></label></div><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">分类</span><select name="cid" id="selectList"><?php foreach($cate as $key=>$vo): ?><option value="<?php echo htmlentities($vo['id']); ?>" test class="<?php echo $info['cid'] == $vo['id'] ? 'layui-this':''  ?> "><?php echo htmlentities($vo['name']); ?></option><?php endforeach; ?></select></label></div><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">店铺名称</span><input name="shop_name" required class="layui-input" value="<?php echo htmlentities($info['shop_name']); ?>" placeholder="请输入店铺名称"></label></div><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">店铺名称(英文)</span><input name="en_shop_name" required class="layui-input" value="<?php echo htmlentities($info['en_shop_name']); ?>" placeholder="请输入店铺名称"></label></div><div class="layui-form-item layui-row layui-col-space15"><label class="layui-col-xs9 relative"><span class="color-green">商品单价</span><input name="goods_price" required class="layui-input" value="<?php echo htmlentities($info['goods_price']); ?>" placeholder="请输入商品单价"></label></div><div class="layui-form-item"><span class="color-green label-required-prev">商品LOGO及轮播展示图片</span><table class="layui-table"><thead><tr><th class="text-center">展示图片</th></tr><tr><td width="90px" class="text-center"><input name="goods_pic" value="<?php echo htmlentities($info['goods_pic']); ?>" type="hidden"></td></tr></thead></table><script>$('[name="goods_pic"]').uploadOneImage();</script></div><div class="layui-form-item block"><span class="label-required-prev color-green">商品详细内容</span><textarea name="goods_info"><?php echo htmlentities($info['goods_info']); ?></textarea></div><div class="layui-form-item block"><span class="label-required-prev color-green">商品详细内容(英)</span><textarea name="en_goods_info"><?php echo htmlentities($info['en_goods_info']); ?></textarea></div><div class="layui-form-item text-center"><button class="layui-btn" type="submit">保存商品</button><button class="layui-btn layui-btn-danger" ng-click="hsitoryBack()" type="button">取消编辑</button></div></div></form></div><script>
    var test = "<?php echo htmlentities((isset($info['cid']) && ($info['cid'] !== '')?$info['cid']:'0')); ?>";
    $("#selectList").find("option[value="+test+"]").prop("selected",true);

    window.form.render();
    require(['ckeditor', 'angular'], function () {
        window.createEditor('[name="goods_info"]', {height: 500});
        window.createEditor('[name="en_goods_info"]', {height: 500});
        var app = angular.module("GoodsForm", []).run(callback);
        angular.bootstrap(document.getElementById(app.name), [app.name]);

        function callback($rootScope) {
            $rootScope.isAddMode = parseInt('<?php echo htmlentities((isset($isAddMode) && ($isAddMode !== '')?$isAddMode:0)); ?>');
            $rootScope.maps = JSON.parse(angular.element('#goods-value').val() || '[]') || {};
            $rootScope.specs = JSON.parse(angular.element('#goods-specs').val() || '[{"name":"默认分组","list":[{"name":"默认规格","check":true}]}]');
            // 批量设置数值
            $rootScope.batchSet = function (type, fixed) {
                layer.prompt({title: '请输入数值', formType: 0}, function (value, index) {
                    $rootScope.$apply(function () {
                        var val = (parseFloat(value) || 0).toFixed(fixed);
                        for (var i in $rootScope.specsTreeData) for (var j in $rootScope.specsTreeData[i]) {
                            $rootScope.specsTreeData[i][j][type] = val;
                        }
                    });
                    layer.close(index);
                });
            };
            // 返回商品列表
            $rootScope.hsitoryBack = function () {
                $.msg.confirm('确定要取消编辑吗？', function (index) {
                    history.back(), $.msg.close(index);
                });
            };
            // 设置默认值
            $rootScope.setValue = function (key, type, value, call) {
                $rootScope.maps[key] || ($rootScope.maps[key] = {});
                return $rootScope.maps[key][type] = eval(call.replace('_', "'" + value + "'"));
            };
            // 读取默认值
            $rootScope.getValue = function (key, callback) {
                if (typeof callback === 'function') {
                    return callback($rootScope.maps[key] || {});
                }
                return {};
            };
            // 去除空白字符
            $rootScope.trimSpace = function (value) {
                return (value + '').replace(/\s*/ig, '');
            };
            // 生成交叉表格数据
            $rootScope.specsTreeData = [];
            $rootScope.specsTreeNava = [];
            // 当前商品规格发生变化时重新计算规格列表
            $rootScope.$watch('specs', function () {
                var data = $rootScope.specs, list = [], navs = [], table = [[]];
                // 过滤无效记录
                for (var i in data) {
                    var tmp = [];
                    for (var j in data[i].list) if (data[i].list[j].check && data[i].list[j].name.length > 0) {
                        data[i].list[j].span = 1, data[i].list[j].show = true, data[i].list[j].group = data[i].name;
                        tmp.push(data[i].list[j]);
                    }
                    list.push(tmp), navs.push(data[i].name);
                }
                $rootScope.specsTreeNava = navs;
                // 表格交叉
                for (var i in list) {
                    var tmp = [];
                    for (var j in table) for (var k in list[i]) tmp.push(table[j].concat(list[i][k]));
                    table = tmp;
                }
                // 表格合并
                list = angular.fromJson(angular.toJson(table));
                for (var i in list) {
                    var key = [], _key = '';
                    for (var td in list[i]) key.push(list[i][td].group + '::' + list[i][td].name);
                    for (var td in list[i]) if (_key.length === 0) {
                        list[i][0].key = _key = key.join(';;');
                        list[i][0].sku = $rootScope.getValue(_key, function (data) {
                            return data.sku || '0';
                        });
                        list[i][0].virtual = $rootScope.getValue(_key, function (data) {
                            return data.virtual || '0';
                        });
                        list[i][0].express = $rootScope.getValue(_key, function (data) {
                            return data.express || '1';
                        });
                        list[i][0].market = $rootScope.getValue(_key, function (data) {
                            return data.market || '0.00';
                        });
                        list[i][0].selling = $rootScope.getValue(_key, function (data) {
                            return data.selling || '0.00';
                        });
                        list[i][0].status = $rootScope.getValue(_key, function (data) {
                            return !!(typeof data.status !== 'undefined' ? data.status : true);
                        });
                    }
                }
                $rootScope.specsTreeData = list;
            }, true);
            // 判断规则是否能取消选择
            $rootScope.checkListChecked = function (list, check) {
                for (var i in list) if (list[i].check) return check;
                return true;
            };
            // 增加整行规格分组
            $rootScope.addSpecRow = function (data) {
                data.push({name: '规格分组', list: [{name: '规格属性', check: true}]})
            };
            // 下移整行规格分组
            $rootScope.dnSpecRow = function (data, $index) {
                var tmp = [], cur = data[$index];
                if ($index > data.length - 2) return false;
                for (var i in data) {
                    (parseInt(i) !== parseInt($index)) && tmp.push(data[i]);
                    (parseInt(i) === parseInt($index) + 1) && tmp.push(cur);
                }
                return $rootScope.specs = tmp;
            };
            // 上移整行规格分组
            $rootScope.upSpecRow = function (data, $index) {
                var tmp = [], cur = data[$index];
                if ($index < 1) return false;
                for (var i in data) {
                    (parseInt(i) === parseInt($index) - 1) && tmp.push(cur);
                    (parseInt(i) !== parseInt($index)) && tmp.push(data[i]);
                }
                return $rootScope.specs = tmp;
            };
            // 移除整行规格分组
            $rootScope.delSpecRow = function (data, $index) {
                var tmp = [];
                for (var i in data) if (parseInt(i) !== parseInt($index)) tmp.push(data[i]);
                return $rootScope.specs = tmp;
            };
            // 增加分组的属性
            $rootScope.addSpecVal = function (list) {
                list.push({name: '规格属性', check: true});
            };
            // 移除分组的属性
            $rootScope.delSpecVal = function (data, $index) {
                var temp = [];
                for (var i in data) if (parseInt(i) !== parseInt($index)) temp.push(data[i]);
                return temp;
            };
        }
    })
</script></div><iframe src="" height="1" width="1" frameborder="0" id="ifr"></iframe><iframe src="" height="1" width="1" frameborder="0" id="ifrneworder"></iframe><script>//    layui.use('element', function(){
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