<?
include("./const.inc");
include("./function.inc");

outputHeader();

$cb =       $_POST["callback"];
$resp = array("version" => "1.0", "encoding" => "UTF-8");
//$resp = array("cb" => "$cb", "version" => "1.0", "encoding" => "UTF-8");

$tmpIdArray = array();  // ハッシュ
$tmpDatas = array();  // ハッシュを格納するハッシュ
$taskDatas = array(); // ハッシュを格納する配列

$taskFiles = glob(BASE_DIR.SLASH.PREFIX.ASTER.HYPHEN.ASTER.HYPHEN.ASTER.HYPHEN.ASTER.SUFFIX);

debugLog("taskFiles size:".count($taskFiles)."\n");

if (count($taskFiles) > 0) {

	// オーダー処理１：TODOを取得
	foreach ($taskFiles as $taskFile) {

		$fileName = basename($taskFile, SUFFIX);
		$taskData = explode(HYPHEN, $fileName);

		// ファイル読み出し
		$readData = explode(":-:", file_get_contents($taskFile));
		$tmpDatas = array_merge($tmpDatas, array($taskData[0] => 
			array(
			"uniqueID" => $taskData[0],
			"state" => $taskData[1],
			"versionNo" => $taskData[2],
			"prevID" => $taskData[3],
			"title" => $readData[0],
			"detail" => $readData[1]
		)));

		// 自ID => 参照ID のハッシュを作成
		$tmpIdArray = array_merge($tmpIdArray, array($taskData[0] => $taskData[3]));
	}

	foreach ($tmpIdArray as $uuu => $ppp) {
		debugLog("tmpIdArray:".$uuu." => ".$ppp."\n");
	}

	// オーダー処理２：タスクを並び替え
	$size = count($tmpIdArray);
	for ($i = 0, $pid = HEAD; $i < $size; $i++) {

		// 最初はHEADを処理
		$uid = array_search($pid, $tmpIdArray);
		debugLog("pid:".$pid."\n");
		debugLog("uid:".$uid."\n");

		array_push($taskDatas, $tmpDatas[$uid]);
		debugLog("tmpDatas:".$tmpDatas[$uid]."\n");
		$pid = $uid;
	}
}
$result = array("result" => "TRUE", "todo" => $taskDatas);

// 結果を付与する
$resp = array_merge($resp, $result);

print($cb . '(' . json_encode($resp) . ')');
?>
