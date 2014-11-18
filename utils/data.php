<?php
    error_reporting(E_ERROR | E_PARSE);
  
    include 'db_credentials/data.php';
    include '../db_credentials/data.php';
    
    $host = $db_host;
    $pass = $db_pass;
    $db_host = $db_name;
    $db_name = $db_user;
?>