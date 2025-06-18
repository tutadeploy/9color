// 生成前50条商品数据的VALUES语句
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
  const id = item.id || "NULL";
  const shopName = "默认商店";
  const enShopName = "Default Shop";
  const goodsName = escapeSqlString(item.title || "");
  const enGoodsName = escapeSqlString(item.title || "");
  const goodsInfo = "";
  const enGoodsInfo = escapeSqlString(item.title || "");
  const goodsPrice = parseFloat(item.market_price) || 0.0;
  const goodsPic = item.logoimage || "";
  const addtime = item.createtime || Math.floor(Date.now() / 1000);
  const status = item.statusswitch === "1" ? 1 : 0;
  const cid = 1;
  const isTj = "NULL";

  return `(${id},'${shopName}','${enShopName}','${goodsName}','${enGoodsName}','${goodsInfo}','${enGoodsInfo}',${goodsPrice},'${goodsPic}',${addtime},${status},${cid},${isTj})`;
}

// 主函数
function main() {
  const jsonData = readJsonData();
  if (jsonData.length === 0) {
    console.log("没有找到有效的JSON数据");
    return;
  }

  // 只取前50条数据
  const firstBatch = jsonData.slice(0, 50);
  console.log(`处理前 ${firstBatch.length} 条商品数据`);

  // 生成VALUES语句
  const values = firstBatch.map((item) => generateSingleValues(item));
  const valuesString = ",\n" + values.join(",\n");

  console.log("生成的VALUES语句:");
  console.log(valuesString);

  // 保存到文件
  fs.writeFileSync("first_batch_values.txt", valuesString, "utf8");
  console.log("\nVALUES语句已保存到 first_batch_values.txt");
}

main();
