<?php 
	//include '../PHP/config.php';
	//Session_start();
?>
	<!doctype html>
	<html>

	<head>
		<title>Control panel</title>

	<!--	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
 		<link rel="shortcut icon" href="/images/favicon.png" type="image/png">
		
		<script src="js/action.js"></script>
		<script src="js/auth.js"></script>
		<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
	-->
<link rel="stylesheet" href="styles.css" type="text/css">
	</head>
	<body>
			<header>
			
			</header>

		<form id="addProject" enctype="multipart/form-data" method="post" action ="PHP/test.php">
			
			<input id="name1" type="name1"  placeholder="Фамилия" required>
			
			<input id="name2" type="name2"  placeholder="Имя" required>			

			<input id="name3" type="name3"  placeholder="Отчество" required>			

			<select id="num" name="hero[]">
   			<option selected value="1">Лаборатрная работа 1</option>
			<option value="2">Лаборатрная работа 2</option>
    			<option value="3">Лаборатрная работа 3</option>
    			<option value="4">Лаборатрная работа 4</option>
   			<option value="5">Лаборатрная работа 5</option>
			<option value="6">Лаборатрная работа 6</option>
    			<option value="7">Лаборатрная работа 7</option>
    			<option value="8">Лаборатрная работа 8</option>
			<option value="9">Курсовой проект</option>
   			</select>
			
			<textarea id="files" placeholder="Файлы"required></textarea>

			<input type="file" id="file" multiple accept="image/*" onchange="handleFiles(this.files)">

			<textarea id="comments" placeholder="Комментарии (Опционально)" ></textarea>
			
			<input type="submit" id = "sent" value="Отправить">
		</form>	

  <form enctype="multipart/form-data" method="post" action ="PHP/test.php">
   	<p><input type="file" name="uploaded[]" multiple>
   	<input type="submit" value="Отправить"></p>
  </form> 
</body>
	
</html>