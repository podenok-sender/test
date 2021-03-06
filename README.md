Podenok-sender
==============================================================================================
Содержание
==============================================================================================
[Введение](#введение) 

[Руководство пользователя](#инструкция) 

[Принцип работы приложения](#приложение)

[Описание POST-запросов](#post) 
+  [Описание полей JSON пакетов](#json)
+  [Файлы POST-запроса](#files)

[Примеоры POST-запросов](#примеры)
+  [Запрос информации о лабораторной](#info)
+  [Сохранение лабораторной](#save)
+  [Скачивание файлов лабораторной](#download)
+  [Отправка лабораторной](#sent)

[Работа сервера](#сервер)

[Интерфейс](#интерфейс)

<a name="введение"/>Введение
===================================================================================================

Знакома ли вам ситуация когда вы хотите сдать лабу, но у вас не получается отправить ее преподавателю?<br>
"Ну конечно!" - отвечаете вы. "Вот бы существовало решение этой проблемы...".<br>
Спешу вас обрадовать, такое решение есть!<br>
Представляем вам сервис для отправки лабораторных работ по СПОВМ - Podenok-sender.<br>


<a name="инструкция"/>Руководство пользователя
---------------------------------------------------------------------------------------------
Для отправки лабораторной, небходимо заполнить поля: Фамилия, Имя, Отчество, Номер группы. Выбрать номер отправляемой лабораторной работы, добавить в письмо коментарии к лабораторной работе (не уверен что он их читает).<br>
Поля заполняются кирилическими символами, без учета регистра, двойные фамилии пишутся через дефис.<br>
Поле "Код лабораторной" - не обязательное для заполнение. Если оставить поле пустым будет создана новая лабораторная, введя код можно редактировать уже загруженную лабораторную работу. 


<a name="приложение"/>Принцип работы приложения
---------------------------------------------------------------
Пользователь заполняет поля в форме и после выбирает одну из функций:
+  Сохранить - выполняет сохранение заргуженных файлов лабораторной работы на сервер и возвращает уникальный код лабораторной. 
+  Отправить - выполняет сохранение лабораторной работы, с последующей отправкой на почту Подёнка.
+  Скачать архив  - выполняет сохранение лабораторной работы и возвращает архив .tar.gz в Поденкочитаемом виде.

После заполнения полей формы и нажатия на одну из кнопок, скрипт выполняет проверку введенных пользователем данных. После проверки данные отправляются на сервер в формате JSON посредством асихрнного запроса ajax. На сервере осуществляется проверка полученных данных и файлов, создание директории с уникальным кодом лабораторной, все файлы пользователя архивируются и сжимаются средствами tar и gzip. После файлы, если необхоимо, отправляются Подёнку.

<a name="post"/>Описание POST-запросов
---------------------------------------------------------------
Пользовательский интерфейс осуществляет взаимодействие с сервером посредством POST-запросов к файлу API.php.<br>
Сервер различает 4 типа запросов:
+	info - получение информации о лабораторной по ее уникальному коду, возвращает информацию о лабораторной работе Фамилия, Имя, Номер группы и.т.д.
+ save - выполняет сохранение заргуженных файлов и заполненных полей на сервер, возвращает код ошибки или код лабораторной работы.
+ sent - отправка архива лабораторной работы электронным письмом, принимает код отправляемой лабораторной, возвращает код ошибки. 
+ download - скачивает файл с сервера, принимает код лабораторной, возвращает код ошибки или бинарный поток - запрашиваемый файл.

<a name="json"/>

### Описание полей JSON пакетов:

[action] - тип запроса, принамает значения:	   [string]
+ 'info' - получение информации о лабораторной;
+ 'save' - сохранение лабораторной;
+ 'sent' - отправка лабораторной электронным письмом;
+ 'download' - Скачивает файл с сервера;

[id] - код лабораторной, принамает значения:    [string:32]
+ '...' - 32x символьная строка - код существующей лабораторной.
+ '' - создание новой лабораторной.

[name1] - фамилия пользователя:                 [string]
+ 'Иванов' - строка состоящая только из русских символов и тире.
+ 'Иванов-Сидоров' - тире разеляет двойные фамилии.

[name2] - имя пользователя:                     [string]
+ 'Иван' - строка состоящая только из русских символов. 

[name3] - отчество пользователя:                [string]
+ 'Иванович' - строка состоящая только из русских символов.

[group] - номер группы:                         [string:6]
+ '950508' - сторка из 6 цифр.

[lab] - номер лабораторной работы:              [int]              
+ '1' - '8' - номера лабораторных работ соответственно.
+ '9' - курсовой проект.

[comments] - коменарии к работе (опционально).  [string]

[copyID] - код лабораторной, из которой будут скопированы файлы [copyFiles]:    [string:32]
+ '...' - 32x символьная строка - код существующей лабораторной.
+ '' - копирование файлов не требуется.

[copyFiles] - двумерный массив  - список файлов для копирования.
+  [name] - названия файлов.			[string array]
   +  [0] - информация о первом файле.	[string]
   +  [1] - информация о втором файле.	[string]
  

[OK] - код возврата:					               [bool]
+  '0' - ошибка выполнения запроса.
+  '1' - запрос выполнен успешно.

[message] - текст сообщения об ошибке.				[string]

[file] - название файла для скачивания. 			[string]

[tar] - запрос на скачивание лабы в архиве:		[bool]
+  'true' - скачать архив .tar.gz лабораторной работы.
+  'false' - скачать файл [file].


<a name="files"/>

### Файлы POST-запроса:

[files] - двумерный массив файлов,
первое поле - одно из описанный далее,
второе поле - порядковый номер загружаемого файла.
(подробнее: https://www.php.net/manual/ru/features.file-upload.post-method.php)
+  [name] - названия файлов.			[string array]
   +  [0] - информация о первом файле.	[string]
   +  [1] - информация о втором файле.	[string]
+  [tmp_name] - временное имя в директории.	[string array]
+  [size] - размер фала в байтах.			[int array]
+  [error] - ошибки при загрузке файла.		[int array]


<a name="примеры"/>Примеры POST-запросов
---------------------------------------------------------------


<a name="info"/>

### Запрос информации о лабораторной.

```js
$.ajax({
	type: 'POST',
	url: 'PHP/API.php',
	charset: "utf-8",
	data: {
		'action': 'info',
		'id': '4rdbDpHKB5SVuJJ2tAYoMTXN49pHKyb4',
	},
	success: function(data, textStatus, request) {

		if (data.OK) {
			alert(JSON.stringify(data));
		}
		else {
			alert(data.message);
		}

	},
	error: function(request, textStatus, errorThrown) {
		alert(textStatus);
	}
});
```

<a name="save"/>

### Сохранение лабораторной.

```js
$.ajax({
	type: 'POST',
	url: 'PHP/API.php',
	charset: "utf-8",
	data: {
		'action': 'save',
		'id': '', // Создание новой лабораторной
		'name1': 'Иванов',
		'name2': 'Иван',
		'name3': 'Иванович',
		'group': '950508',
		'lab': '1',
		'comments': 'Мой комментарий',
		'copyID': '4rdbDpHKB5SVuJJ2tAYoMTXN49pHKyb4',
		'copyFiles': { // Скопировать файлы 'copyFiles' из лабораторной 'copyID'
			'name': {
				'0': 'mail.cpp',
				'1': 'readme.pdf'
			}
		}
	},
	success: function(data, textStatus, request) {
		if (!data.OK) {
			alert(data.message);
		}

	},
	error: function(request, textStatus, errorThrown) {
		alert(textStatus);
	}
});
```


<a name="download"/>

### Скачивание файлов лабораторной.

```js
$.ajax({
	url: 'PHP/API.php',
	type: 'POST',
	dataType: 'binary',
	xhrFields: {
		responseType: 'blob'
	},
	data: {
		action: 'download',
		id: 'LhYe699a15f3dETHj4qSo6TditF5VtAz',
		file: 'lab.pdf',
		tar: 'false'
	},
	success: function(data, status, xhr) {
		var blob = new Blob([data], {
			type: xhr.getResponseHeader('Content-Type')
		});
		var link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = 'lab.pdf';
		link.click();
	},
	error: function(request, textStatus, errorThrown) {
		alert(textStatus);
	}
});
```
<a name="sent"/>

### Отправка лабораторной.

```js
$.ajax({
	type: 'POST',
	url: 'PHP/API.php',
	charset: "utf-8",
	data: {
		'action': 'sent',
		'id': '4rdbDp6KB5SVuJJ2tAYoMTXN49pHKyb4',
	},
	success: function(data, textStatus, request) {

		if (!data.OK) {
			alert(data.message);
		}

	},
	error: function(request, textStatus, errorThrown) {
		alert(textStatus);
	}
});
```

<a name="сервер"/>Работа сервера
---------------------------------------------------------------

Сервер работает под управлением Apache и написан на PHP 7.4. В директории PHP расположены файлы API.php и mail.php.
API.php - основной файл сервера, который выполняем обработку всех POST запросов поступающих от клиента. 
Файл mail.php - выполняет расылку сообщений Поденку. В файле config.php - заданы основные настройки работы сервера. Скрипт GC.php (garbage collector) - запускается по таймеру Cron и выполняет удаление лабораторных, которые не использовались более 10 дней, или размер которх превышает 10 мб. 

<a name="интерфейс"/>Интерфейс
---------------------------------------------------------------

Интерфейс пользователя написан на базе фреймворка React UI. Дизайн формы разработан в стиле Material Design. За основу были взяты элементы MATERIAL-UI с ресура https://material-ui.com/ru .
Дизайн фона позаимствоан с ресурса https://codepen.io/jacquelinclem/pen/udnwI . 


