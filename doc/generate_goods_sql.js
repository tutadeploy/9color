// JSON数据转SQL VALUES语句生成脚本
const fs = require("fs");

// 读取JSON数据
function readJsonData() {
  try {
    const data = fs.readFileSync("data.json", "utf8");
    const jsonData = JSON.parse(data);
    return jsonData.rows || [];
  } catch (error) {
    console.error("读取JSON文件失败:", error);
    return [];
  }
}

// 转义SQL字符串
function escapeSqlString(str) {
  if (!str) return "";
  return str.replace(/'/g, "\\'").replace(/\\/g, "\\\\");
}

// 生成单条商品的VALUES语句
function generateSingleValues(item) {
  // 字段映射
  const id = item.id || "NULL";
  const shopName = "默认商店";
  const enShopName = "Default Shop";
  const goodsName = escapeSqlString(item.title || "");
  const enGoodsName = escapeSqlString(item.title || ""); // 暂用中文名
  const goodsInfo = ""; // JSON中describe为null，使用空字符串
  const enGoodsInfo = escapeSqlString(item.title || "");
  const goodsPrice = parseFloat(item.market_price) || 0.0;
  const goodsPic = item.logoimage || "";
  const addtime = item.createtime || Math.floor(Date.now() / 1000);
  const status = item.statusswitch === "1" ? 1 : 0;
  const cid = 1; // 默认分类ID
  const isTj = "NULL";

  return `(${id},'${shopName}','${enShopName}','${goodsName}','${enGoodsName}','${goodsInfo}','${enGoodsInfo}',${goodsPrice},'${goodsPic}',${addtime},${status},${cid},${isTj})`;
}

// 生成所有VALUES语句
function generateAllValues(jsonData) {
  const values = jsonData.map((item) => generateSingleValues(item));
  return values.join(",\n");
}

// 主函数
function main() {
  console.log("开始处理JSON数据...");

  const jsonData = readJsonData();
  if (jsonData.length === 0) {
    console.log("没有找到有效的JSON数据");
    return;
  }

  console.log(`找到 ${jsonData.length} 条商品数据`);

  // 生成VALUES语句
  const valuesStatement = generateAllValues(jsonData);

  // 输出到文件
  const outputContent = `-- 批量商品数据VALUES语句\n-- 生成时间: ${new Date().toISOString()}\n-- 数据条数: ${
    jsonData.length
  }\n\n${valuesStatement}`;

  fs.writeFileSync("generated_values.sql", outputContent, "utf8");

  console.log("VALUES语句已生成到 generated_values.sql");
  console.log("\n前3条数据预览:");
  console.log(
    jsonData
      .slice(0, 3)
      .map((item) => generateSingleValues(item))
      .join(",\n")
  );
}

// 执行脚本
main();
