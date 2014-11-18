<?php

function session_init()
{
    //session_save_path('/public/timbre/EXPERTSYSTEM/temp');
    //var_dump($_SERVER["DOCUMENT_ROOT"]);
    //session_save_path('/public/timbre/EXPERTSYSTEM/temp');
    $tempdir = 'temp';
    while(!file_exists($tempdir))
        $tempdir='../'.$tempdir;
    session_save_path($tempdir);
    
    ini_set('session.gc_maxlifetime', 12*60*60); // 3 hours
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    ini_set('session.cookie_secure', FALSE);
    ini_set('session.use_only_cookies', TRUE);
    session_start();
}


function insert($mysqli, $table, $rows, $values = null) {
    $istruzione = 'INSERT INTO ' . $table;
    
    if ($rows != null) {
        $istruzione .= ' (' . implode(',', $rows) . ')';
    }

    for ($i = 0; $i < count($values); $i++) {
        if (is_string($values[$i]))
            $values[$i] = '\'' . $mysqli->real_escape_string($values[$i]) . '\'';
    }
    $values = implode(',', $values);
    $istruzione .= ' VALUES (' . $values . ')';
    $mysqli->query($istruzione);
  
    $myFile = "query_log.txt";
    $fh = fopen($myFile, 'a');
    fwrite($fh, date("Y-m-d H:i:s") . "] Error: " . $mysqli->errno . ")\t " . $istruzione . " -> ". $mysqli->error . "\n");
    fclose($fh);     
    
    return $mysqli->errno;
//echo "<p>".$istruzione."</p>";
}

function query($mysqli, $istruzione)
{
    $mysqli->query($istruzione);
    
    $myFile = "query_log.txt";
    $fh = fopen($myFile, 'a');
    fwrite($fh, date("Y-m-d H:i:s") . "] Error: " . $mysqli->errno . ")\t " . $istruzione . " -> ". $mysqli->error . "\n");
    fclose($fh);
    
    return $mysqli->errno;
}


function insertOrUpdate($mysqli, $table, $rows, $values = null, $updaterows) {
    $istruzione = 'INSERT INTO ' . $table;

    $istruzione .= ' (' . implode(',', $rows) . ')';
    for ($i = 0; $i < count($values); $i++) {
        if (is_string($values[$i]))
            $s_values[$i] = '"' . $mysqli->real_escape_string($values[$i]) . '"';
        else
            $s_values[$i] = $mysqli->real_escape_string($values[$i]);
    }
    $s_values = implode(',', $s_values);
    $istruzione .= ' VALUES (' . $s_values . ')';
    $istruzione .= ' ON DUPLICATE KEY UPDATE  ';
    foreach ($updaterows as $value) {
        $upquery[$value] = ' ' . $rows[$value] . '= "' . $mysqli->real_escape_string($values[$value]) . '" ';
    }
    $istruzione .= implode(',', $upquery);
    
    $res = $mysqli->query($istruzione);

    
    $myFile = "query_log.txt";
    $fh = fopen($myFile, 'a');
    fwrite($fh, date("Y-m-d H:i:s") . "] Error: " . $mysqli->errno . ")\t " . $istruzione . " -> ". $mysqli->error . "\n");
    fclose($fh);    

//    return $mysqli->errno;
    
    return $res;
}

function formatTime($sec) {
    $min = $sec / 60;
    $sec = $sec % 60;
    return sprintf('%02d:%02d', intval($min), intval($sec));
}

?>