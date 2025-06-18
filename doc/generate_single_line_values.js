// 生成单行VALUES语句的脚本
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
  const createTime = parseInt(item.createtime) || Math.floor(Date.now() / 1000);
  const status = parseInt(item.statusswitch) || 1;
  const showStatus = 1;
  const lastField = "NULL";

  return `(${id},'${shopName}','${enShopName}','${goodsName}','${enGoodsName}','${goodsInfo}','${enGoodsInfo}',${goodsPrice},'${goodsImg}',${createTime},${status},${showStatus},${lastField})`;
}

// 主函数
function main() {
  const jsonData = readJsonData();

  if (jsonData.length === 0) {
    console.log("没有找到数据");
    return;
  }

  console.log(`找到 ${jsonData.length} 条商品数据`);

  // 生成所有VALUES，用逗号连接成一行
  const allValues = jsonData.map((item) => generateSingleValues(item));

  // 创建完整的INSERT语句，所有VALUES在一行
  const sqlStatement = `INSERT INTO \`xy_shop_goods_list\` VALUES ${allValues.join(
    ","
  )};`;

  // 保存到文件
  fs.writeFileSync("single_line_values.sql", sqlStatement, "utf8");

  console.log("单行VALUES语句已生成到 single_line_values.sql");
  console.log(`总共生成了 ${allValues.length} 条记录`);
  console.log("语句长度:", sqlStatement.length, "字符");
}

main();
