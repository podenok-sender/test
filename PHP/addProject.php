<?php
	include '../../PHP/config.php';
	session_start(); 
	$conn = @mysqli_connect( $servername, $serveruser, $serverpass, $bdname);
	mysqli_query($conn, "SET NAMES 'utf8'");

	if ($_SESSION['login'] !=1 || stripos($_SESSION['accessLevel'],'#addVote') === false){
        	echo '<data>';
       		echo "no access permissions";
       		echo '</data>';
       		return;
	}

  	 if (mysqli_connect_errno())
	{
		echo '<error>';
		echo '<code>2</code>';
		echo '<message>'.mysqli_connect_error().'</message>';
		echo '</error>';
		return;
    }
/*        if(!$_FILES['image'])
        {
            echo '<error>';
            echo '<code>3</code>';
            echo '<message>Invalid file</message>';
            echo '</error>';
            return;
        }

        $filename = $_FILES['image']['name'];

        $location = tempnam("images", "vote");
        $uploadOk = 1;
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        $valid_types = array("jpg","jpeg","png");
        $valid = in_array(strtolower($filetype), $valid_types);
        if($valid)
        {
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $location))
            {
                echo '<error>';
                echo '<code>4</code>';
                echo '<message>Can\'t write file</message>';
                echo '</error>';
                return;
            }
        }
        else
        {
            echo '<error>';
            echo '<code>5</code>';
            echo '<message>Invalid file</message>';
            echo '</error>';
            return;
        }
*/
        $title = filter_var($_POST["title"], FILTER_SANITIZE_STRING);
        $youtube = filter_var($_POST["link"], FILTER_SANITIZE_STRING);
        $info = filter_var($_POST["info"], FILTER_SANITIZE_STRING);
        $start = filter_var($_POST["start"], FILTER_SANITIZE_STRING);
        $end = filter_var($_POST["end"], FILTER_SANITIZE_STRING);
       // $location = pathinfo($location, PATHINFO_FILENAME);

        if (!preg_match("/^https:\/\/www.youtube.com\/embed\/[0-9a-zA-Z\-\_]*$/", $youtube))
        {
            echo '<error>';
            echo '<code>7</code>';
            echo '<message>Bad youtube link</message>'.$youtube;
            echo '</error>';
            return;
        }

		$result = @mysqli_query($conn, "INSERT INTO votes (title, image, youtube, info, start, end) VALUES ('$title', '$location', '$youtube', '$info', '$start', '$end')");
        if(mysqli_errno($conn))
        {
            echo 'mysqli_error';
            echo mysqli_error($conn);
            return;
        }

       echo 'OK';
       return;
   

?>