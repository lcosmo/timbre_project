<?php
	include 'functions.php';
	include 'data.php';
        header('Content-type: text/html; charset=utf-8');
     
        $mysqli = new mysqli($host, $db_name, $pass, $db_host);    
        $result = $mysqli->query(sprintf("SELECT * FROM user WHERE email LIKE '%s'", $_GET['email']));
        $row = $result->num_rows;
        echo $row==0?"true":"false";
?>
