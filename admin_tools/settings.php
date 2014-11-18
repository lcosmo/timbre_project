
<?php
include '../utils/functions.php';
include '../utils/data.php';

header('Content-type: text/html; charset=utf-8');

session_init();
if (!isset($_SESSION['userid']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("location: admin_tools/index.php");
    return;
};

$mysqli = new mysqli($host, $db_name, $pass, $db_host);

if (isset($_GET['download']) and file_exists('../backups/'.$_GET['download'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($_GET['download']));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($_GET['download']));
    ob_clean();
    flush();
    readfile($_GET['download']);
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        Backups:
        <?php          
            //shell_exec("mysqldump -u $db_name $db_host > ../backups/backup.sql");  

            $query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
            
            $backup_file=  getcwd().'/../backups/backup.sql';
            $sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";

            $result = $mysqli->query($query);
            $row = $result->fetch_array(MYSQLI_ASSOC);


            mysql_select_db('test_db');
            $retval = mysql_query( $sql, $conn );
            if(! $retval )
            {
              die('Could not take data backup: ' . mysql_error());
            }
            echo "Backedup  data successfully\n";
            mysql_close($conn);


            $dir    = '../backups/';
            $files = scandir($dir);
            foreach($files as $f)
            {
                if($f=='.' or $f=='..') continue;
                $s = filesize($dir.$f)/1000+1;
                echo "<li>$f ($s KB)<a href='settings.php?download=$f'>Download</a> <a href='settings.php?delete=$f'>Delete</a></li>";
            }
        ?>
    </body>
</html>
