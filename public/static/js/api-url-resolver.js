/**
 * API URL解析器
 * 智能转换相对路径为正确的ThinkPHP路径
 *
 * 创建时间: 2024-06-14
 * 依赖: url-mapping.js
 */

window.ApiUrlResolver = {
  // 版本信息
  version: "1.0.0",

  // URL缓存，提高性能
  urlCache: new Map(),

  /**
   * 解析URL，将相对路径转换为绝对路径
   * @param {string} url - 原始URL
   * @param {Object} options - 选项参数
   * @returns {string} 转换后的URL
   */
  resolve: function (url, options = {}) {
    if (!url) {
      this.log("URL为空，返回原值", "warn");
      return url;
    }

    // 缓存检查
    if (this.urlCache.has(url)) {
      const cachedUrl = this.urlCache.get(url);
      this.log(`使用缓存URL: ${url} -> ${cachedUrl}`);
      return cachedUrl;
    }

    const originalUrl = url;
    let resolvedUrl = url;

    try {
      // 1. 如果已经是完整URL，直接返回
      if (this.isAbsoluteUrl(url)) {
        this.log(`已是绝对URL: ${url}`);
        return url;
      }

      // 2. 处理ThinkPHP模板语法（虽然应该在服务器端处理，但做兼容）
      if (this.isThinkPHPUrl(url)) {
        this.log(`检测到ThinkPHP语法URL: ${url}`);
        return url; // ThinkPHP语法由服务器端处理
      }

      // 3. 提取URL基础部分（去除查询参数和锚点）
      const urlParts = this.parseUrl(url);
      const baseUrl = urlParts.base;

      // 4. 查找映射表
      if (window.ApiUrlMapping && window.ApiUrlMapping.mappings[baseUrl]) {
        resolvedUrl = window.ApiUrlMapping.mappings[baseUrl];
        this.log(`找到映射: ${baseUrl} -> ${resolvedUrl}`);
      } else {
        // 5. 智能推断控制器
        resolvedUrl = this.inferControllerUrl(baseUrl);
        this.log(`智能推断: ${baseUrl} -> ${resolvedUrl}`);
      }

      // 6. 重新组装URL（添加查询参数和锚点）
      if (urlParts.query || urlParts.hash) {
        resolvedUrl += urlParts.query + urlParts.hash;
      }

      // 7. 缓存结果
      this.urlCache.set(originalUrl, resolvedUrl);

      this.log(`URL解析完成: ${originalUrl} -> ${resolvedUrl}`);
      return resolvedUrl;
    } catch (error) {
      this.log(`URL解析出错: ${error.message}`, "error");
      return originalUrl; // 出错时返回原URL
    }
  },

  /**
   * 检查是否为绝对URL
   * @param {string} url
   * @returns {boolean}
   */
  isAbsoluteUrl: function (url) {
    return /^(https?:\/\/|\/\/)/.test(url) || url.startsWith("/");
  },

  /**
   * 检查是否为ThinkPHP模板语法
   * @param {string} url
   * @returns {boolean}
   */
  isThinkPHPUrl: function (url) {
    return /\{:.*\}/.test(url);
  },

  /**
   * 解析URL各部分
   * @param {string} url
   * @returns {Object}
   */
  parseUrl: function (url) {
    const parts = {
      base: url,
      query: "",
      hash: "",
    };

    // 提取锚点
    const hashIndex = url.indexOf("#");
    if (hashIndex !== -1) {
      parts.hash = url.substring(hashIndex);
      url = url.substring(0, hashIndex);
    }

    // 提取查询参数
    const queryIndex = url.indexOf("?");
    if (queryIndex !== -1) {
      parts.query = url.substring(queryIndex);
      parts.base = url.substring(0, queryIndex);
    }

    return parts;
  },

  /**
   * 智能推断控制器URL
   * @param {string} method - 方法名
   * @returns {string}
   */
  inferControllerUrl: function (method) {
    const currentPath = window.location.pathname;
    let controller = this.getDefaultController();

    // 根据当前页面路径推断控制器
    if (window.ApiUrlMapping && window.ApiUrlMapping.controllerRules) {
      const rules = window.ApiUrlMapping.controllerRules;

      for (const path in rules) {
        if (currentPath.includes(path)) {
          controller = rules[path];
          break;
        }
      }
    }

    this.log(`推断控制器: ${currentPath} -> ${controller}`);
    return `/index/${controller}/${method}`;
  },

  /**
   * 获取默认控制器
   * @returns {string}
   */
  getDefaultController: function () {
    if (window.ApiUrlMapping && window.ApiUrlMapping.defaultController) {
      return window.ApiUrlMapping.defaultController;
    }
    return "index";
  },

  /**
   * 清除URL缓存
   */
  clearCache: function () {
    this.urlCache.clear();
    this.log("URL缓存已清除");
  },

  /**
   * 获取缓存统计信息
   * @returns {Object}
   */
  getCacheStats: function () {
    return {
      size: this.urlCache.size,
      entries: Array.from(this.urlCache.entries()),
    };
  },

  /**
   * 日志记录
   * @param {string} message
   * @param {string} type
   */
  log: function (message, type = "info") {
    if (window.ApiUrlMapping && window.ApiUrlMapping.log) {
      window.ApiUrlMapping.log(`[Resolver] ${message}`, type);
    }
  },

  /**
   * 初始化解析器
   */
  init: function () {
    this.log("ApiUrlResolver 初始化完成");

    // 输出版本信息
    if (window.ApiUrlMapping && window.ApiUrlMapping.debug) {
      console.log(
        `%c ApiUrlResolver v${this.version} 已加载`,
        "color: #4CAF50; font-weight: bold;"
      );
    }
  },
};

// 自动初始化
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", function () {
    window.ApiUrlResolver.init();
  });
} else {
  window.ApiUrlResolver.init();
}
