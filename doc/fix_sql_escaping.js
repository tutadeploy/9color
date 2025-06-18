// 修复SQL转义字符问题的脚本
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

// 正确转义SQL字符串
function escapeSqlString(str) {
  if (!str) return "";
  // 只需要转义单引号，不需要双重转义
  return str.replace(/'/g, "\\'");
}

// 生成单条商品的VALUES语句
function generateSingleValues(item) {
  const id = item.id || "NULL";
  const shopName = "默认商店";
  const enShopName = "Default Shop";
  const goodsName = escapeSqlString(item.title || "");
  const enGoodsName = escapeSqlString(item.title || "");
  const goodsInfo = "";
  const enGoodsInfo = escapeSqlString(item.title || "");
  const goodsPrice = parseFloat(item.market_price) || 0;
  const goodsImg = item.logoimage || "";
  const createTime = item.createtime || Date.now();
  const statusSwitch = item.statusswitch || 1;
  const isRecommend = 1;
  const sort = "NULL";

  return `(${id},'${shopName}','${enShopName}','${goodsName}','${enGoodsName}','${goodsInfo}','${enGoodsInfo}',${goodsPrice},'${goodsImg}',${createTime},${statusSwitch},${isRecommend},${sort})`;
}

// 主函数
function main() {
  const data = readJsonData();

  if (data.length === 0) {
    console.log("没有找到数据");
    return;
  }

  console.log(`找到 ${data.length} 条记录`);

  // 生成VALUES语句
  const valuesList = data.map((item) => generateSingleValues(item));

  // 将VALUES语句连接起来
  const valuesString = valuesList.join(",\n");

  // 写入文件
  fs.writeFileSync("fixed_values.sql", valuesString);

  console.log("SQL VALUES语句已生成到 fixed_values.sql 文件");
  console.log("请手动将这些VALUES添加到01-init.sql文件的INSERT语句中");
}

main();
