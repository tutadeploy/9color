/**
 * URL映射配置文件
 * 用于将相对路径的接口调用转换为正确的ThinkPHP路径
 *
 * 创建时间: 2024-06-14
 * 用途: Nginx代理环境下的前端接口路由改造
 */

window.ApiUrlMapping = {
  // 版本信息
  version: "1.1.0",

  // 调试模式（开发环境设为true，生产环境设为false）
  debug: true,

  // URL映射表
  mappings: {
    // ============ 用户相关接口 ============
    // 登录接口
    doLogin: "/index/user/do_login",
    do_login: "/index/user/do_login",

    // 注册接口
    do_register: "/index/user/do_register",
    doRegister: "/index/user/do_register",

    // 忘记密码接口
    do_forget: "/index/user/do_forget",
    doForget: "/index/user/do_forget",

    // 退出登录
    logout: "/index/user/logout",
    doLogout: "/index/user/logout",

    // 更新活跃时间
    updateactivetime: "/index/user/updateactivetime",

    // ============ 首页相关接口 ============
    // 获取用户消息
    get_user_msg: "/index/index/get_user_msg",
    getUserMsg: "/index/index/get_user_msg",

    // 设置用户消息
    set_user_msg: "/index/index/set_user_msg",
    setUserMsg: "/index/index/set_user_msg",

    // 语言切换
    changelang: "/index/index/changelang",

    // ============ 订单相关接口 ============
    // 提交订单
    submit_order: "/index/rot_order/submit_order",
    submitOrder: "/index/rot_order/submit_order",

    // 抢单接口（新版）
    "convey/save": "/index/rot_order/submit_order",
    conveySave: "/index/rot_order/submit_order",

    // 获取订单列表
    order_list: "/index/order/index",
    getOrderList: "/index/order/index",

    // 订单详情
    order_detail: "/index/order/detail",
    getOrderDetail: "/index/order/detail",

    // 订单信息（用于提交订单弹窗）
    "order/orderInfo": "/index/order/order_info",
    orderInfo: "/index/order/order_info",

    // 提交订单（立即提交）
    "order/doOrder": "/index/order/do_order",
    doOrder: "/index/order/do_order",

    // ============ 控制器相关接口 ============
    // 理财宝相关
    lixibao_ru: "/index/ctrl/lixibao_ru",
    lixibaoRu: "/index/ctrl/lixibao_ru",

    // VIP充值
    recharge_dovip: "/index/ctrl/recharge_dovip",
    rechargeDovip: "/index/ctrl/recharge_dovip",

    // ============ 我的相关接口 ============
    // 我的主页
    my_index: "/index/my/index",
    myIndex: "/index/my/index",

    // 个人信息
    my_profile: "/index/my/profile",
    myProfile: "/index/my/profile",

    // ============ 支付相关接口 ============
    // 支付接口
    pay: "/index/pay/index",
    doPay: "/index/pay/index",

    // 支付回调
    pay_callback: "/index/pay/callback",
    payCallback: "/index/pay/callback",

    // ============ 商城相关接口 ============
    // 商品列表
    goods_list: "/index/shop/goods_list",
    goodsList: "/index/shop/goods_list",

    // 商品详情
    goods_detail: "/index/shop/goods_detail",
    goodsDetail: "/index/shop/goods_detail",

    // ============ API相关接口 ============
    // 通用API
    api: "/index/api/index",
    doApi: "/index/api/index",

    // 获取验证码
    get_verify: "/index/api/get_verify",
    getVerify: "/index/api/get_verify",

    // ============ 发送相关接口 ============
    // 发送消息
    send_msg: "/index/send/send_msg",
    sendMsg: "/index/send/send_msg",

    // ============ 支持相关接口 ============
    // 客服支持
    support: "/index/support/index",
    getSupport: "/index/support/index",

    // ============ 定时任务相关接口 ============
    // 定时任务
    crontab: "/index/crontab/index",
    runCrontab: "/index/crontab/index",

    // ============ 头像相关接口 ============
    // 头像修改
    editHeadImg: "/index/my/headimg",

    // ============ 银行卡相关接口 ============
    // 绑定银行卡
    "bankinfo/save": "/index/my/bind_bank",
    bankinfoSave: "/index/my/bind_bank",
    bindBank: "/index/my/bind_bank",

    // ============ 提现相关接口 ============
    // 提现申请
    "deposit/do_deposit": "/index/ctrl/do_deposit",
    depositDoDeposit: "/index/ctrl/do_deposit",
    doDeposit: "/index/ctrl/do_deposit",
  },

  // 控制器推断规则
  controllerRules: {
    // 根据URL路径推断控制器
    "/login": "user",
    "/register": "user",
    "/user/": "user",
    "/my/": "my",
    "/order": "order",
    "/rot_order": "rot_order",
    "/rotOrder": "rot_order",
    "/ctrl/": "ctrl",
    "/pay": "pay",
    "/shop": "shop",
    "/api": "api",
    "/send": "send",
    "/support": "support",
    "/crontab": "crontab",
    "/admin": "admin",
  },

  // 默认控制器
  defaultController: "index",

  // 响应格式转换配置
  responseTransforms: {
    doLogin: {
      successField: "code", // 成功判断字段
      successValue: 0, // 成功判断值 (0表示成功)
      messageField: "info", // 消息字段
      redirectUrl: "/index/home", // 成功后跳转URL
      enabled: true, // 是否启用转换
    },

    do_login: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      redirectUrl: "/index/home",
      enabled: true,
    },

    do_register: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      redirectUrl: "/login",
      enabled: true,
    },

    doRegister: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      redirectUrl: "/login",
      enabled: true,
    },

    do_forget: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    // 登出接口响应转换
    logout: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      redirectUrl: "/user/login",
      enabled: true,
    },

    doLogout: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      redirectUrl: "/user/login",
      enabled: true,
    },

    // 订单信息接口响应转换
    "order/orderInfo": {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    orderInfo: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    // 提交订单接口响应转换
    "order/doOrder": {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    doOrder: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    // 新版抢单接口响应转换
    "convey/save": {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
      customTransform: function (response) {
        console.log("[API转换] 原始响应:", response);
        // 新版期望的响应格式转换
        if (response.code === 0) {
          const transformedData = {
            success: true,
            msg: response.info || "抢单成功",
            data: {
              orderNo: response.oid,
              createTime: Date.now(),
              oid: response.oid,
              add_id: response.add_id || null,
              // 添加标识，表示需要获取详细订单信息
              needOrderDetail: true,
            },
            originalData: response,
          };
          console.log("[API转换] 转换后响应:", transformedData);
          return transformedData;
        } else {
          const transformedData = {
            success: false,
            msg: response.info || "抢单失败",
            data: null,
            originalData: response,
          };
          console.log("[API转换] 转换后响应:", transformedData);
          return transformedData;
        }
      },
    },

    conveySave: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
      customTransform: function (response) {
        // 新版期望的响应格式转换
        if (response.code === 0) {
          return {
            success: true,
            msg: response.info || "抢单成功",
            data: {
              orderNo: response.oid || response.data?.orderNo,
              createTime: response.data?.createTime || Date.now(),
              oid: response.oid,
              add_id: response.add_id,
            },
            originalData: response,
          };
        } else {
          return {
            success: false,
            msg: response.info || "抢单失败",
            data: null,
            originalData: response,
          };
        }
      },
    },

    editHeadImg: {
      url: "/index/my/headimg",
      method: "POST",
      params: {
        pic: "headpic", // 将headpic参数映射到pic
      },
      response: {
        success: "code",
        message: "info",
        redirect: "url",
        enabled: "status",
      },
    },

    // 绑定银行卡接口响应转换
    "bankinfo/save": {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    bankinfoSave: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    bindBank: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    // 提现接口响应转换
    "deposit/do_deposit": {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    depositDoDeposit: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },

    doDeposit: {
      successField: "code",
      successValue: 0,
      messageField: "info",
      enabled: true,
    },
  },

  // URL跳转映射
  urlRedirects: {
    "/": "/index/home",
    "index/home": "/index/home",
    "/login": "/user/login",
    login: "/user/login",
  },

  // 日志记录
  log: function (message, type = "info") {
    if (this.debug && console) {
      const timestamp = new Date().toISOString();
      const logMessage = `[${timestamp}] [ApiUrlMapping] ${message}`;

      switch (type) {
        case "error":
          console.error(logMessage);
          break;
        case "warn":
          console.warn(logMessage);
          break;
        case "info":
        default:
          console.log(logMessage);
          break;
      }
    }
  },

  // 参数名映射配置（如果需要）
  paramMapping: {
    doRegister: {
      // 前端参数名 -> 后端参数名
      userName: "user_name",
      depositPwd: "deposit_pwd",
      inviteCode: "invite_code",
    },
  },
};
