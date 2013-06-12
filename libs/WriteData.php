<?
/**
 * ＜処理＞ タスク新規登録
 *
 * ＜概要＞
 * 登録は無条件に行う (ファイル新規作成)。
 * 並びはその時点の先頭とする (prevID = 0)。
 * その時点の先頭の参照先(prevID) を付け替える。
 *
 * ＜返却値＞
 * その時点の全タスクは重いので、Todoステータスのタスク再取得にとどめる。
 *
 */
include("./const.inc");
include("./function.inc");

outputHeader();

$cb =       $_POST["callback"];
$taskData = $_POST["taskData"];
$title =     $taskData['title'];
$detail =    $taskData['detail'];

$prefix = PREFIX . time();
$uniqueID = uniqid($prefix, TRUE);
$state = STATE_TODO;
$versionNo = 1;
$resp = array("version" => "1.0", "encoding" => "UTF-8");

// 既存の先頭タスク
$oldFiles = glob(BASE_DIR.SLASH.PREFIX.ASTER.HYPHEN.STATE_TODO.HYPHEN.ASTER.HYPHEN.HEAD.SUFFIX);
debugLog("oldFiles:" . $oldFiles[0] . "\n");

$ret = true;
if (count($oldFiles) > 0) {

	$fileName = basename($oldFiles[0], SUFFIX);
	$oldData = explode(HYPHEN, $fileName);

	debugLog("fileName:" . $fileName . "\n");
	debugLog("oldData1:" . $oldData[1] . "\n");
	debugLog("oldData2:" . $oldData[2] . "\n");
	debugLog("oldData3:" . $oldData[3] . "\n");

	if ($oldData[1] == STATE_TODO) {
		$oldData[3] = $uniqueID;
		$newFile = BASE_DIR.SLASH.implode(HYPHEN, $oldData).SUFFIX;

		debugLog("newFile:" . $newFile . "\n");
	}
	// 既存の先頭タスクの参照先IDを新規作成するIDに向ける
	$ret = rename($oldFiles[0], $newFile);
}

// 参照先の付け替え(リネーム)処理に失敗していたら、タスク登録しない
if ($ret) {

	// 新規登録
	$taskFile = BASE_DIR.SLASH.$uniqueID.HYPHEN.$state.HYPHEN.$versionNo.HYPHEN.HEAD.SUFFIX;
	$ret2 = file_put_contents($taskFile, $title.DD.$detail);

	if (!$ret2) {
		$result = array("result" => "FALSE");
	
	// Todoリストを取る
	} else {
		$tmpIdArray = array();  // ハッシュ
		$tmpDatas = array();  // ハッシュを格納するハッシュ
		$taskDatas = array(); // ハッシュを格納する配列

		// STATE_TODO のみ取得
		$taskFiles = glob(BASE_DIR.SLASH.PREFIX.ASTER.HYPHEN.STATE_TODO.HYPHEN.ASTER.HYPHEN.ASTER.SUFFIX);

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
	}

} else {
	$result = array("result" => "FALSE");
}

// 結果を付与する
$resp = array_merge($resp, $result);

print($cb . '(' . json_encode($resp) . ')');

?>

