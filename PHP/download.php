<?php
// exmp:  http://podonok.com/PHP/download.php?id=jeb88gpWN5Fl41xtahmvDoIQEh8eyWza&file=lab3.pdf
if (empty($_GET['id']) || (empty($_GET['file']) && empty($_GET['tar'])) ){echo "err0";return;}
if ()
$path = dirname (__DIR__) . '\\PHP\\TMP\\';
$file = $path . $_GET['id'] . '\\';
$info = json_decode(file_get_contents($file . 'info'),TRUE);
$foldername = $info[name1] . ' ' . mb_substr($info[name2],0,1) . '. ' . mb_substr($info[name3],0,1). '.';	
$file = $file . $foldername. '\\' . $_GET['file'];

if (!file_exists($file)){echo "err1";echo $file;return;}

    	header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
    	header('Content-Disposition: attachment; filename="'.basename($file).'"');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate');
    	header('Pragma: public');
    	header('Content-Length: ' . filesize($file));
 	readfile($file);
?>