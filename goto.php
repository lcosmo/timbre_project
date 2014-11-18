<?php
    include 'utils/functions.php';
    include 'utils/data.php';

    header('Content-type: text/html; charset=utf-8');

    session_init();

    if (!isset($_SESSION['userid'])) {
        header("location: index.php");
        return;
    };
    
    $mysqli = new mysqli($host, $db_name, $pass, $db_host);
    
    if(isset($_GET['pid']))
    {
        $array = array(
            'project_id' => $_GET['pid'],
            'usersessions_id' => $_SESSION['sessionid']
        );
        insert($mysqli, 'visitedlinks', array_keys($array), array_values($array));
    }

    if($_GET['url']!="")
        header("location: ".$_GET['url']);

    $myFile = "external_link.txt";
    $fh = fopen($myFile, 'a');
    fwrite($fh, $_GET['url'] . "\n");
    fclose($fh); 
?>
