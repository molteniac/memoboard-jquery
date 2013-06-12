<?

function debugLog($str) {
	if (DEBUG_LOG) {
		print($str);
	}
}


function outputHeader() {
	mb_http_output('UTF-8');
	header('Content-type: text/javascript; charset=UTF-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

?>
