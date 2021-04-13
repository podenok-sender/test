//-------------------------------------------------------------------------------------------------
//	download.php
//
//	Описание:
//	Скачивает файл 'file' (или архив, если tar = true) с сервера из лабораторной с номером 'id'. 
//
//	Аргументы GET-запроса:
//	id - 32х символьная строка, адреса лабы.	[string:32]
//	file - название файла для скачивания 		[string]
//	tar - запрос на скачивание лабы в архиве	[bool]
//
//	Пример запроса:  
//	http://podonok.com/PHP/download.php?id=jeb88gpWN5Fl41xtahmvDoIQEh8eyWza&file=lab3.pdf
//
//	Последняя правка: Яценко Станислав 14.04.2021
//-------------------------------------------------------------------------------------------------

<?php

if (empty($_GET['id']) || (empty($_GET['file']) && empty($_GET['tar'])) ){echo "invalid request";return;}

$file = dirname (__DIR__) . '\\PHP\\TMP\\' . $_GET['id'] . '\\'; //пусть к файлам лабы 

if ($_GET['tar'] == 'true'){
	$file = $file.'archive.tar.gz';// путь к архиву
}
else {
	if (!file_exists($file . 'info')){echo "INFO file not found <br>"; echo $file ;return;} // проверка существования файла данных
	$info = json_decode(file_get_contents($file . 'info'),TRUE);
	$foldername = $info[name1] . ' ' . mb_substr($info[name2],0,1) . '. ' . mb_substr($info[name3],0,1). '.';// название папки (ФИО пользователя)	
	$file = $file . $foldername. '\\' . $_GET['file']; // путь к искомому файлу
}

if (!file_exists($file)){echo "File not found <br>"; echo $file ;return;} // проверка файла на существование

    	header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
    	header('Content-Disposition: attachment; filename="'.basename($file).'"');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate');
    	header('Pragma: public');
    	header('Content-Length: ' . filesize($file));
 	readfile($file);
?>
