<?php
$path = __DIR__ . '/TMP/';

if ($_POST['action'] == 'info'){
	header('Content-type: application/json');
	$file = $path . $_POST['id'] . '/info';

	if (!file_exists($file)){
		$info['OK'] = 0;
		echo json_encode($info);
		return;
	}
	$info = json_decode(file_get_contents($file),TRUE);
	if ($info['id'] != $_POST['id']) $info['OK'] = 0;
	else $info['OK'] = 1;
	echo json_encode($info);
	return;
}

