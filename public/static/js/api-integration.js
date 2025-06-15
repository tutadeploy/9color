/**
 * API集成模块
 * 拦截jQuery Ajax请求，自动转换URL
 *
 * 创建时间: 2024-06-14
 * 依赖: jQuery, url-mapping.js, api-url-resolver.js
 */

(function ($) {
  "use strict";

  // 检查依赖
  if (typeof $ === "undefined") {
    console.error("[ApiIntegration] jQuery未找到，无法初始化API拦截器");
    return;
  }

  if (typeof window.ApiUrlResolver === "undefined") {
    console.error(
      "[ApiIntegration] ApiUrlResolver未找到，请确保已引入api-url-resolver.js"
    );
    return;
  }

  if (typeof window.ApiUrlMapping === "undefined") {
    console.error(
      "[ApiIntegration] ApiUrlMapping未找到，请确保已引入url-mapping.js"
    );
    return;
  }

  // 保存原始的Ajax方法
  const originalAjax = $.ajax;
  const originalGet = $.get;
  const originalPost = $.post;

  // 统计信息
  const stats = {
    totalRequests: 0,
    transformedRequests: 0,
    transformedResponses: 0,
    errors: 0,
    startTime: Date.now(),
  };

  /**
   * 日志记录
   */
  function log(message, type = "info") {
    if (window.ApiUrlMapping && window.ApiUrlMapping.log) {
      window.ApiUrlMapping.log(`[Integration] ${message}`, type);
    }
  }

  /**
   * 转换参数名
   * @param {string} apiName - API名称
   * @param {Object} data - 原始参数对象
   * @returns {Object} 转换后的参数对象
   */
  function transformParams(apiName, data) {
    if (
      !window.ApiUrlMapping.paramMapping ||
      !window.ApiUrlMapping.paramMapping[apiName]
    ) {
      return data;
    }

    const mapping = window.ApiUrlMapping.paramMapping[apiName];
    const transformedData = {};

    // 如果data是字符串，需要解析
    if (typeof data === "string") {
      // 处理序列化的表单数据
      const params = new URLSearchParams(data);
      for (let [key, value] of params) {
        const newKey = mapping[key] || key;
        transformedData[newKey] = value;
      }

      // 转换回字符串格式
      return new URLSearchParams(transformedData).toString();
    } else if (typeof data === "object" && data !== null) {
      // 处理对象形式的数据
      for (let key in data) {
        const newKey = mapping[key] || key;
        transformedData[newKey] = data[key];
      }
      return transformedData;
    }

    return data;
  }

  /**
   * 从URL中提取API名称
   */
  function extractApiName(url) {
    // 检查映射表中的匹配
    for (let apiName in window.ApiUrlMapping.mappings) {
      if (window.ApiUrlMapping.mappings[apiName] === url || apiName === url) {
        return apiName;
      }
    }
    return null;
  }

  /**
   * 响应数据转换器
   * 将ThinkPHP格式的响应转换为前端期望的格式
   */
  function transformResponseData(apiName, responseData) {
    const config = window.ApiUrlMapping.responseTransforms[apiName];
    if (!config || !config.enabled) {
      return responseData;
    }

    const isSuccess = responseData[config.successField] === config.successValue;
    const message = responseData[config.messageField] || "";

    const transformedData = {
      success: isSuccess,
      msg: message,
      data: responseData,
      originalData: JSON.parse(JSON.stringify(responseData)),
    };

    // 添加重定向URL（如果成功且有配置）
    if (isSuccess && config.redirectUrl) {
      transformedData.redirectUrl = transformRedirectUrl(config.redirectUrl);
    }

    if (window.ApiUrlMapping.debug.logTransforms) {
      console.log(`[API Transform] ${apiName}:`, {
        original: responseData,
        transformed: transformedData,
      });
    }

    return transformedData;
  }

  /**
   * URL跳转转换器
   */
  function transformRedirectUrl(url) {
    // 如果是相对路径，可能需要进一步处理
    if (url && !url.startsWith("http") && !url.startsWith("/")) {
      return "/" + url;
    }
    return url;
  }

  /**
   * 转换Ajax选项中的URL
   */
  function transformAjaxOptions(options) {
    stats.totalRequests++;

    if (!options || !options.url) {
      return options;
    }

    const originalUrl = options.url;
    const transformedUrl = window.ApiUrlResolver.resolve(originalUrl);

    if (originalUrl !== transformedUrl) {
      stats.transformedRequests++;
      log(`Ajax URL转换: ${originalUrl} -> ${transformedUrl}`);

      // 创建新的选项对象，避免修改原对象
      const newOptions = $.extend({}, options, {
        url: transformedUrl,
      });

      return newOptions;
    }

    return options;
  }

  /**
   * 错误处理包装器
   */
  function wrapErrorHandler(originalError, url) {
    return function (xhr, status, error) {
      stats.errors++;
      log(`请求失败: ${url} - ${status}: ${error}`, "error");

      if (originalError) {
        originalError.apply(this, arguments);
      }
    };
  }

  /**
   * 重写$.ajax方法
   */
  $.ajax = function (options) {
    // 处理$.ajax(url, options)的调用方式
    if (typeof options === "string") {
      const url = options;
      options = arguments[1] || {};
      options.url = url;
    }

    // 转换URL
    const transformedOptions = transformAjaxOptions(options);

    // 保存原始回调函数
    const originalSuccess = transformedOptions.success;
    const originalError = transformedOptions.error;

    // 包装成功回调 - 添加响应转换
    if (transformedOptions && transformedOptions.url) {
      transformedOptions.success = function (data, textStatus, xhr) {
        try {
          // 转换响应数据
          const apiName = extractApiName(transformedOptions.url);
          const transformedData = transformResponseData(apiName, data);

          // 调用原始成功回调
          if (originalSuccess) {
            originalSuccess.call(this, transformedData, textStatus, xhr);
          }
        } catch (error) {
          log(`成功回调处理出错: ${error.message}`, "error");
          // 出错时仍调用原始回调
          if (originalSuccess) {
            originalSuccess.call(this, data, textStatus, xhr);
          }
        }
      };

      // 包装错误处理
      transformedOptions.error = wrapErrorHandler(
        originalError,
        transformedOptions.url
      );
    }

    // 调用原始方法
    return originalAjax.call(this, transformedOptions);
  };

  /**
   * 重写$.get方法
   */
  $.get = function (url, data, success, dataType) {
    const transformedUrl = window.ApiUrlResolver.resolve(url);
    if (url !== transformedUrl) {
      stats.transformedRequests++;
      log(`Get URL转换: ${url} -> ${transformedUrl}`);
    }
    return originalGet.call(this, transformedUrl, data, success, dataType);
  };

  /**
   * 重写$.post方法
   */
  $.post = function (url, data, success, dataType) {
    const transformedUrl = window.ApiUrlResolver.resolve(url);
    if (url !== transformedUrl) {
      stats.transformedRequests++;
      log(`Post URL转换: ${url} -> ${transformedUrl}`);
    }
    return originalPost.call(this, transformedUrl, data, success, dataType);
  };

  /**
   * 提供统一的API请求方法（可选使用）
   */
  window.apiRequest = function (url, data, options) {
    options = options || {};

    const defaultOptions = {
      url: url,
      data: data,
      type: "POST",
      dataType: "json",
    };

    const finalOptions = $.extend(defaultOptions, options);
    return $.ajax(finalOptions);
  };

  /**
   * 获取统计信息
   */
  window.getApiStats = function () {
    return {
      ...stats,
      uptime: Date.now() - stats.startTime,
      cacheStats: window.ApiUrlResolver.getCacheStats(),
    };
  };

  /**
   * 重置统计信息
   */
  window.resetApiStats = function () {
    stats.totalRequests = 0;
    stats.transformedRequests = 0;
    stats.transformedResponses = 0;
    stats.errors = 0;
    window.ApiUrlResolver.clearCache();
    log("统计信息已重置");
  };

  /**
   * 调试模式下的全局方法
   */
  if (window.ApiUrlMapping && window.ApiUrlMapping.debug) {
    // 提供全局调试方法
    window.debugApi = {
      stats: window.getApiStats,
      reset: window.resetApiStats,
      resolve: function (url) {
        return window.ApiUrlResolver.resolve(url);
      },
      test: function (url) {
        console.log("URL转换测试:");
        console.log("输入:", url);
        console.log("输出:", window.ApiUrlResolver.resolve(url));
      },
      testResponse: function (url, mockData) {
        console.log("响应转换测试:");
        console.log("URL:", url);
        console.log("模拟数据:", mockData);
        console.log(
          "转换结果:",
          transformResponseData(
            extractApiName(url),
            mockData || { code: 0, info: "测试消息" }
          )
        );
      },
      transformUrl: function (url) {
        return transformRedirectUrl(url);
      },
      testParams: function (apiName, mockParams) {
        const result = transformParams(apiName, mockParams);
        console.log(`API: ${apiName}`, {
          输入参数: mockParams,
          输出参数: result,
        });
        return result;
      },
    };

    // 输出调试信息
    console.log(
      "%c ApiIntegration 已启用",
      "color: #2196F3; font-weight: bold;"
    );
    console.log("调试方法: window.debugApi");
  }

  log("ApiIntegration 初始化完成");
})(jQuery);

/**
 * 全局初始化函数
 */
window.initApiIntegration = function () {
  // 检查所有依赖是否加载完成
  const dependencies = [
    { name: "jQuery", check: () => typeof $ !== "undefined" },
    {
      name: "ApiUrlMapping",
      check: () => typeof window.ApiUrlMapping !== "undefined",
    },
    {
      name: "ApiUrlResolver",
      check: () => typeof window.ApiUrlResolver !== "undefined",
    },
  ];

  const missingDeps = dependencies.filter((dep) => !dep.check());

  if (missingDeps.length > 0) {
    console.error(
      "[ApiIntegration] 缺少依赖:",
      missingDeps.map((dep) => dep.name).join(", ")
    );
    return false;
  }

  console.log(
    "%c API路由系统初始化成功 ✓",
    "color: #4CAF50; font-weight: bold; font-size: 14px;"
  );

  if (window.ApiUrlMapping.debug) {
    console.log("🔧 调试模式已启用");
    console.log("📊 统计方法: window.getApiStats()");
    console.log('🧪 测试方法: window.debugApi.test("doLogin")');
  }

  return true;
};

// 自动初始化
$(document).ready(function () {
  setTimeout(function () {
    window.initApiIntegration();
  }, 100); // 延迟100ms确保所有依赖都加载完成
});
