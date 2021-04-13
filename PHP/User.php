<?php
	include '../../PHP/config.php';
	session_start(); 
	$conn = @mysqli_connect( $servername, $serveruser, $serverpass, $bdname);
	mysqli_query($conn, "SET NAMES 'utf8'");

if($_POST["action"] == "addUser"){
	if ($_SESSION['login'] !=1 || stripos($_SESSION['accessLevel'],'#addUser') === false){
        	echo '<data>';
       		echo "no access permissions";
       		echo '</data>';
       		return;
	}
	include('mail.php');
	include('mailTemplate.php');
	
	for ($i = 0; $i < count($_POST["users"]); $i++){
		$err[$i] = '';
		$name = filter_var($_POST["users"][$i], FILTER_SANITIZE_STRING);
       		$password = filter_var($_POST["passwords"][$i], FILTER_SANITIZE_STRING);
		$email = filter_var($_POST["emails"][$i], FILTER_SANITIZE_STRING);
		$accessLevel = $_POST["accessLevel"];
		$pass = $password;
		$password = hash("sha256", $password);
		$result = @mysqli_query($conn, "SELECT name FROM users WHERE name = '$name'");
		if ($result->num_rows > 0) { 
			$err[$i] = 'Пользователь с таким именем уже существует';
			continue;// already registered
		}
		$result = @mysqli_query($conn, "SELECT name FROM users WHERE email = '$email'");
		if ($result->num_rows > 0) { 
			$err[$i] = '"Эта почта уже занята';
			continue;// already registered
		}
		
        	if(mysqli_errno($conn)){
          		$err[$i] = 'Ошибка БД';
			continue;
       		}
		else $result = @mysqli_query($conn, "INSERT IGNORE INTO users ( name,email, password, accessLevel) VALUES ('$name', '$email', '$password', '$accessLevel')");

		$domain = substr(strrchr($email, "@"), 1);
		$res = getmxrr($domain, $mx_records, $mx_weight);
		if (false == $res || 0 == count($mx_records) || (1 == count($mx_records) && ($mx_records[0] == null  || $mx_records[0] == "0.0.0.0" ))) {
			$err[$i] = 'Почтовый сервер недоступен';

		}
		else{
			try {
				$mail->ClearAddresses();  
				$mail->addAddress($email);
				$mail->Body = NewMail($name,$pass);
    	
 				if ($mail->send())$err[$i] = '0';
				else $err[$i] = 'Ошибка отправки письма';	
			} 
			catch (Exception $e) {
				$err[$i] = 'Ошибка отправки письма';
			}
		}
		if ($err[$i] == 'Ошибка отправки письма') $result = @mysqli_query($conn, "DELETE FROM users WHERE name = '$name'");
	}
	header('Content-type: application/json');
	echo json_encode($err);
     	return;

}else if($_POST["action"] == "delete-user")
	{
        $name = filter_var($_POST["user-name"], FILTER_SANITIZE_STRING);

        $result = @mysqli_query($conn, "DELETE FROM users 
        WHERE name = '$name'");

        if(mysqli_errno($conn))
        {
            echo '<error>';
            echo '<code>3</code>';
            echo '<message>'.mysqli_error().'</message>';
            echo '</error>';
            return;
        }

        echo '<data>';
        echo 0;
        echo '</data>';
        return;
    }
    else if($_POST["action"] == "drop-users")
	{
        $result = @mysqli_query($conn, "DELETE FROM users 
        WHERE name != 'test'");

        if(mysqli_errno($conn))
        {
            echo '<error>';
            echo '<code>3</code>';
            echo '<message>'.mysqli_error().'</message>';
            echo '</error>';
            return;
        }

        echo '<data>';
        echo 0;
        echo '</data>';
        return;
    }
?>