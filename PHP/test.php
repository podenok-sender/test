<?php

if ($_POTST['action'] == 'getinfo'){include('mail.php');newmail($subject,$message,$tempdir.'/archive.tar.gz');}


$message = 'nope';
$subject = '950501 Яценко С. С. Лабораторная работа 1'
$name = 'Яценко С. С.';
$tempdir='W:/domains/podonok.com/PHP/TMP/'.random();
mkdir($tempdir);
mkdir($tempdir.'/'.$name);
$path = $tempdir.'/'.$name.'/';

for ( $i = 0; $i < count($_FILES['uploaded']['name']); $i++){
	move_uploaded_file ( $_FILES['uploaded']['tmp_name'][$i] ,  $path.$_FILES['uploaded']['name'][$i]);
}

$phar = new PharData($tempdir.'/archive.tar');
$phar->buildFromDirectory($tempdir);
$phar->compress(Phar::GZ);

if ($_POTST['action'] == 'download')download($tempdir.'/archive.tar.gz');
if ($_POTST['action'] == 'sent'){include('mail.php');newmail($subject,$message,$tempdir.'/archive.tar.gz');}


//dirDel($tempdir);



//------------------------------------------functions-------------------------------------
function dirDel ($dir) 
{  
    $d=opendir($dir);  
    while(($entry=readdir($d))!==false) 
    { 
        if ($entry != "." && $entry != "..") 
        { 
            if (is_dir($dir."/".$entry)) 
            {  
                dirDel($dir."/".$entry);  
            } 
            else 
            {  
                unlink ($dir."/".$entry);  
            } 
        } 
    } 
    closedir($d);  
    rmdir ($dir);  
 } 

function random($length = 32) {
    static $randStr = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $rand = '';
    for($i=0; $i<$length; $i++) {
        $key = rand(0, strlen($randStr)-1);
        $rand .= $randStr[$key];
    }
    return $rand;
}

function download($file){
    	header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
    	header('Content-Disposition: attachment; filename="'.basename($file).'"');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate');
    	header('Pragma: public');
    	header('Content-Length: ' . filesize($file));
 	readfile($file);
}

function sent($file){
}

/*try
{
    $phar = new PharData($tempdir.'/archive.tar');

    // ADD FILES TO archive.tar FILE

    $a->addFile('1.txt');
    $a->addFile('2.txt');

    // COMPRESS archive.tar FILE. COMPRESSED FILE WILL BE archive.tar.gz
    $a->compress(Phar::GZ);

    // NOTE THAT BOTH FILES WILL EXISTS. SO IF YOU WANT YOU CAN UNLINK archive.tar
    unlink('archive.tar');
} 

catch (Exception $e) 
{
    echo "Exception : " . $e;
}

*/

?>