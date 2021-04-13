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

if ($_POST['action'] == 'info'){
	header('Content-type: application/json');
	$file = $path . $_POST['id'] . '/info';

	if (!file_exists($file)){
		$info['OK'] = 0;
		$res['message'] = "INFO file not found";
		echo json_encode($info);
		return;
	}
	$info = json_decode(file_get_contents($file),TRUE);
	
	if (!is_numeric($info['group']) || strlen($info['group']) != 6 ||
	$info['lab'] < 1 || $info['lab'] > 9 || $info['id'] != $_POST['id'] 
	|| count($info['files']['name']) < 1){	// проверка корректности информации 
		$res['OK'] = 0;
		$res['message'] = "Uncorrect INFO file";
		echo json_encode($res);
		return;
	} 
	$info['OK'] = 1;
	echo json_encode($info);
	return;
}

if ($_POST['action'] == 'sent'){

	header('Content-type: application/json');
	$path = __DIR__ . '/TMP/'.$_POST['id'].'/';

	if (!file_exists($path . 'archive.tar.gz')){	// проверка существования архива
		$res['OK'] = 0;
		$res['message'] = "archive.tar.gz not found";
		echo json_encode($res);
		return;
	} 

	if (!file_exists($path . 'info')){	// проверка существования файла данных
		$res['OK'] = 0;
		$res['message'] = "INFO file not found";
		echo json_encode($res);
		return;
	} 
	
	$info = json_decode(file_get_contents($path . 'info'),TRUE);
	
	if (!is_numeric($info['group']) || strlen($info['group']) != 6 ||
	$info['lab'] < 1 || $info['lab'] > 9 || $info['id'] != $_POST['id'] 
	|| count($info['files']['name']) < 1){	// проверка корректности информации 
		$res['OK'] = 0;
		$res['message'] = "Uncorrect INFO file";
		echo json_encode($res);
		return;
	} 
	
	$subject = $info['group'].' '.$info['name1']. ' ' . mb_substr($info[name2],0,1) . '. ' . mb_substr($info[name3],0,1). '. ';
	if ($info['lab'] < 9)$subject = $subject . 'Лабораторная работа №' . $info['lab'];
	if ($info['lab'] == 9)$subject = $subject . 'Курсовое проектирование';
	
	if (newmail($subject, $info['comment'], $path . 'archive.tar.gz') < 1){	// письмо не отправлено
		$res['OK'] = 0;
		$res['message'] = "Can't send email !";
		echo json_encode($res);
		return;
	} 
	
	$res['OK'] = 1;
	echo json_encode($res);	
}



if ($_POST['action'] == 'save'){



}

/*

if (id == 0){ // точно создаем новый обект 
	if ($_POST['copyId'] != '')$path = newObject(true);// with copy;
	else $path = newObject();// new;
}
else {
	if (equal)$path = $copypath;
	else $path = newObject(true);// with copy;
}

//	$file = $path . $_POST['id'] . '/info';

	newmail($subject,$message,$tempdir.'/archive.tar.gz');}
}


function equal(){

if (id == 0)return 0;
$info = json_decode(file_get_contents($file . 'info'),TRUE);

if ()
	if (count($_FILES['uploaded']['name']) > 0)return 0;
	if (count($_POST['copyFiles']['name']) == 0){
}



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
		$copypath = 'W:/domains/podonok.com/PHP/TMP/'.$_POST['copyId'].'/'.foldername($_POST['copyId']).'/';
		for ( $i = 0; $i < count($_POST['copyFiles']['name']); $i++){
			copy($copypath . $_POST['copyFiles']['name'][$i] , $path . $_POST['copyFiles']['name'][$i]); 
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

function foldername($id){
	$info = json_decode(file_get_contents(dirname (__DIR__) . '/TMP/' . $id . '/' . 'info'),TRUE);
	return $info[name1] . ' ' . mb_substr($info[name2],0,1) . '. ' . mb_substr($info[name3],0,1). '.';	
}
*/