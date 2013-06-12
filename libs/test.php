<?
/**
 * ＜処理＞ タスク編集
 *
 * ＜概要＞
 * ステータス毎の並びを Piggyback で返却。
 *
 */
include("./const.inc");
include("./function.inc");

outputHeader();


$dat = 011;


if ($dat & MOD_ORDER) {
	echo "MOD_ORDER!!\n";
}
if ($dat & MOD_DATA) {
	echo "MOD_DATA!!\n";
}
if ($dat & MOD_STATE) {
	echo "MOD_STATE!!\n";
}

echo $dat;

?>
