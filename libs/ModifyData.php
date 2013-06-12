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

$cb =        $_POST["callback"];
$taskData =  $_POST["taskData"];
$uniqueID =  $taskData['uniqueID'];
$title =     $taskData['title'];
$detail =    $taskData['detail'];
$state =     $taskData['state'];
$versionNo = $taskData['versionNo'];

$bit = 0;

// 対象タスクのファイル
$targetFile = BASE_DIR.SLASH.$uniqueID.HYPHEN.$state.HYPHEN.$versionNo.HYPHEN.ASTER.SUFFIX;

// 順番を変更する
function modOrder() {
}

// データ内容を変更する
function modData() {
}

// 状態を変更する
function modState() {
}

function bitCheck($bit, $mode) {
	return ($bit & $mode);
}

// 対象のタスクを開いてロック
$tfh = @fopen($taskFile, "w+"); // 追加モード
if ($tfh) {
	flock($tfh, LOCK_EX);


	// 順番だけ変更する
	if (bitCheck($bit, MOD_ORDER)) {

	// データ内容を変更する
	} else if ($bit & MOD_DATA) {

	// 状態を変更する
	} else if (MOD_STATE) {

	} else {

	}

	$taskBody = $title.DD.$detail;

	// 変更内容でデータを更新
	fputs($tfh, $taskBody);
}


//
//
//


$resp = "";

if (!$tfh) {
	$resp = "failed to open file: " .$taskFile;
} else {

	flock($tfh, LOCK_EX);

	// タスクの変更
	$modFlag = false;
	$taskList = explode("\n", $taskStr);
	foreach ($taskList as $key => $task) {
		$tArray = explode(",", $task);
		if ($tArray[0] == $uniqueID) {
			$tArray[1] = $title;
			$tArray[2] = $detail;
			$tArray[3] = $state;

			if ($tArray[4] != $versionNo) {
				// TODO バージョン番号がマッチしないときの処理
			} else {
				$tArray[4] = $versionNo + 1;
				$modFlag = true;
			}
			$newTask = implode(",", $tArray);
			$taskList[$key] = $newTask;
			break;
		}
	}
	if ($modFlag) {
		$taskStr = implode("\n", $taskList);
		if ($taskStr != null) {
			fputs($tfh, $taskStr);
		} else {
		}
	} else {
		$resp = array("version" => "1.0", "encoding" => "UTF-8", "result" => "false");
	}
}

flock($tfh, LOCK_UN);
fclose($tfh);

print($cb . '(' . json_encode($resp) . ')');
?>

