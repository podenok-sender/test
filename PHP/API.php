<?php
//-------------------------------------------------------------------------------------------------
//	API.php
//
//	Описание:
//	Отвечает на три типа POST-запросов.
//
//	Аргументы POST-запроса:
//	action - тип запроса, принамает значения:		[string]
//		'info' - получение информации о лабораторной,
// 		'save' - сохранение лабораторной,
//		'sent' - отправка лабораторной электронным письмом.
//
//	id - 32х символьная строка, адрес лабораторной работы.	[string:32]
//	name1 - фамилия студента.				[string]
//	name1 - имя студента.					[string]
//	name1 - отчество студента.				[string]
//	group - номер группы студента.				[string:6]
//	lab - '1 -'8' - номера лабораторных работ соответственно,
//	      '9' - курсовой проект.				[int:1-9]
//	comments - коменарии к работе (опционально).		[string]
//	copyFiles - список файлов для копирования.		[sring array]
//	copyID - арес файлов для копирования.			[string:32]
//	OK - код возврата.					[bool]
//	message - сообщение об ошибке.				[string]
//
//	Файлы POST-запроса:
//	[files] - двумерный массив файлов,
//	первое поле - одно из описанный далее,
//	второе поле - порядковый номер загружаемого файла.
//	(подробнее: https://www.php.net/manual/ru/features.file-upload.post-method.php)
//		[name] - названия файлов.			[string array]
//			[0] - информация о первом файле.	[string]
//			[1] - информация о втором файле.	[string]
//		[tmp_name] - временное имя в директории.	[string array]
//		[size] - размер фала в байтах.			[int array]
//		[error] - ошибки при загрузке файла.		[int array]
//
//
//	Пример запроса:
//	info - принимает: action, id; возвращает id, name1, name2, name3, group, lab, comments, files[name], OK, message;
// 	send - принимает: action, id; возвращает OK, message;
//	save - принимает: action, id, name1, name2, name3, group, lab, comments, copyFiles, copyID, files; возвращает OK, message;
//
//	Последняя правка: Яценко Станислав 14.04.2021
//-------------------------------------------------------------------------------------------------


$path = __DIR__ . '/TMP/';

if ($_POST['action'] == 'info')
{
	header('Content-type: application/json');
	$file = $path . $_POST['id'] . '/info';

	if (!file_exists($file))
	{
		$res['OK'] = 0;
		$res['message'] = "INFO file not found";
		echo json_encode($res);
		return;
	}
	$info = json_decode(file_get_contents($file) , true);

	if (!is_numeric($info['group']) || strlen($info['group']) != 6 || $info['lab'] < 1 || $info['lab'] > 9 || $info['id'] != $_POST['id'] || count($info['files']['name']) < 1)
	{ // проверка корректности информации
		$res['OK'] = 0;
		$res['message'] = "Uncorrect INFO file";
		echo json_encode($res);
		return;
	}
	$info['OK'] = 1;
	echo json_encode($res);
	return;
}

if ($_POST['action'] == 'sent')
{

	header('Content-type: application/json');
	$path = __DIR__ . '/TMP/' . $_POST['id'] . '/';

	if (!file_exists($path . 'archive.tar.gz'))
	{ // проверка существования архива
		$res['OK'] = 0;
		$res['message'] = "archive.tar.gz not found";
		echo json_encode($res);
		return;
	}

	if (!file_exists($path . 'info'))
	{ // проверка существования файла данных
		$res['OK'] = 0;
		$res['message'] = "INFO file not found";
		echo json_encode($res);
		return;
	}

	$info = json_decode(file_get_contents($path . 'info') , true);

	if (!is_numeric($info['group']) || strlen($info['group']) != 6 || $info['lab'] < 1 || $info['lab'] > 9 || $info['id'] != $_POST['id'] || count($info['files']['name']) < 1)
	{ // проверка корректности информации
		$res['OK'] = 0;
		$res['message'] = "Uncorrect INFO file";
		echo json_encode($res);
		return;
	}

	$subject = $info['group'] . ' ' . $info['name1'] . ' ' . mb_substr($info[name2], 0, 1) . '. ' . mb_substr($info[name3], 0, 1) . '. ';
	if ($info['lab'] < 9) $subject = $subject . 'Лабораторная работа №' . $info['lab'];
	if ($info['lab'] == 9) $subject = $subject . 'Курсовое проектирование';

	if (newmail($subject, $info['comment'], $path . 'archive.tar.gz') < 1)
	{ // письмо не отправлено
		$res['OK'] = 0;
		$res['message'] = "Can't send email !";
		echo json_encode($res);
		return;
	}

	$res['OK'] = 1;
	echo json_encode($res);
}

if ($_POST['action'] == 'save')
{

	//	header('Content-type: application/json');
	//	$res['OK'] = 1;
	//	$res['message'] = "File aredy saved";
	//	echo json_encode($res);
	//	return;
	$path = __DIR__ . '/TMP/';

	if ($_POST['id'] != '' && eqal())
	{ // создание нового файла не требуется
		$res['OK'] = 1;
		$res['message'] = "File aredy saved";
		echo json_encode($res);
		return;
	}

	$id = random(); // создание новой лабораторной
	$tempdir = $path . $id;
	mkdir($tempdir); // создание новой дериктории
	//$name = $_POST[name1] . '' . mb_substr($_POST[name2], 0, 1) . '.' . mb_substr($_POST[name3], 0, 1) . '.'; // название папки пользователя
	$name = 'Стас';
	echo $tempdir . '/' . htmlentities($name);

	mkdir($tempdir . '/' . htmlentities($name) , 0755, true); // создание папки пользователя
	if ($_POST['copyID'] != '' && count($_POST['copyFiles']['name']) > 0)
	{

		$oldphar = new PharData($path . $_POST['copyID'] . '/archive.tar.gz');
		$oldphar->extractTo($path . $_POST['copyID'], null, true);
		$copypath = $path . $_POST['copyID'] . '/' . foldername($_POST['copyID']);

		for ($i = 0;$i < count($_POST['copyFiles']['name']);$i++) copy($copypath . '/' . $_POST['copyFiles']['name'][$i], $tempdir . '/' . $name . $_POST['copyFiles']['name'][$i]);
		deldir($copypath);

	}

	$res['OK'] = 1;
	$res['message'] = "OK";
	echo json_encode($res);
	return;

}

//-------------------------------------------------------------------------------------------------
//	download.php
//
//	Описание:
//	Скачивает файл 'file' (или архив, если tar = true) с сервера из лабораторной с номером 'id'.
//
//	id - 32х символьная строка, адреса лабы.	[string:32]
//	file - название файла для скачивания 		[string]
//	tar - запрос на скачивание лабы в архиве	[bool]
//
//	Пример запроса:
//	http://podonok.com/PHP/download.php?id=jeb88gpWN5Fl41xtahmvDoIQEh8eyWza&file=lab3.pdf
//
//	Последняя правка: Яценко Станислав 14.04.2021
//-------------------------------------------------------------------------------------------------
if ($_POST['action'] == 'download')
{

	if (empty($_POST['id']) || (empty($_POST['file']) && empty($_POST['tar'])))
	{
		echo "invalid request";
		return;
	}

	$file = dirname(__DIR__) . '\\PHP\\TMP\\' . $_POST['id'] . '\\'; //путь к файлам лабы
	if ($_POST['tar'] == true)
	{
		$file = $file . 'archive.tar.gz'; // путь к архиву
		
	}
	else
	{
		if (!file_exists($file . 'info'))
		{ // проверка существования файла данных
			echo "INFO file not found <br>";
			echo $file;
			return;
		}
		$info = json_decode(file_get_contents($file . 'info') , true);
		$foldername = $info[name1] . ' ' . mb_substr($info[name2], 0, 1) . '. ' . mb_substr($info[name3], 0, 1) . '.'; // название папки (ФИО пользователя)
		$file = $file . $foldername . '\\' . $_POST['file']; // путь к искомому файлу
		
	}

	if (!file_exists($file))
	{
		echo "File not found <br>";
		echo $file;
		return;
	} // проверка файла на существование
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	readfile($file);
}

//------------------------------------------functions-------------------------------------
function equal()
{

	$path = __DIR__ . '/TMP/';
	$file = $path . $_POST['id'] . '/info';

	if (!file_exists($file))
	{
		return false;
	}

	$info = json_decode(file_get_contents($file) , true);

	if (info['id'] != $_POST['id'] || info['name1'] != $_POST['name1'] || info['name2'] != $_POST['name2'] || info['name3'] != $_POST['name3'] || info['group'] != $_POST['group'] || info['lab'] != $_POST['lab'] || info['comments'] != $_POST['comments'] || count(info['files']['name']) != count($_POST['copyFiles']['name']))
	{
		return false;
	}

	for ($i = 0;$i < count(info['files']['name']);$i++) if (info['files']['name'][$i] != $_POST['copyFiles']['name'][$i]) return false;
	return true;
}

function deldir($dir)
{
	$d = opendir($dir);
	while (($entry = readdir($d)) !== false)
	{
		if ($entry != "." && $entry != "..")
		{
			if (is_dir($dir . "/" . $entry))
			{
				deldir($dir . "/" . $entry);
			}
			else
			{
				unlink($dir . "/" . $entry);
			}
		}
	}
	closedir($d);
	rmdir($dir);
}

function random($length = 32)
{
	static $randStr = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$rand = '';
	for ($i = 0;$i < $length;$i++)
	{
		$key = rand(0, strlen($randStr) - 1);
		$rand .= $randStr[$key];
	}
	return $rand;
}

/*
function newObject($cpy = false){
$name = 'name';

	$id = random();
	$tempdir='W:/domains/podonok.com/PHP/TMP/'.$id;
	mkdir($tempdir);
	mkdir($tempdir.'/'.$name);
	$path = $tempdir.'/'.$name.'/';
	
	for ( $i = 0; $i < count($_FILES['uploaded']['name']); $i++){
		move_uploaded_file ( $_FILES['uploaded']['tmp_name'][$i] ,  $path.$_FILES['uploaded']['name'][$i]);
	}
	
	if ($cpy){// если нужно скопировать файлы из друой лабы
	}

	$phar = new PharData($tempdir.'/archive.tar');
	$phar->buildFromDirectory($tempdir);
	$phar->compress(Phar::GZ);

	$info = array("name1" => "Яценко", "name2" => "Станислав", "name3" => "Сергеевич", "group" => "950501", "id"=> $id, "lab" => '1', "comments" => 'no comments! :0');
	$info['files']['name'] =  $_FILES['uploaded']['name'];

//$info['name'] =  $_FILES['uploaded']['name'];

$file = $tempdir . '/info';
file_put_contents($file, json_encode($info));
}
*/

function foldername($id)
{
	$info = json_decode(file_get_contents(dirname(__DIR__) . '/TMP/' . $id . '/' . 'info') , true);
	return $info[name1] . ' ' . mb_substr($info[name2], 0, 1) . '. ' . mb_substr($info[name3], 0, 1) . '.';
}

?>
