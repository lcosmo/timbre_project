<?php
include '../utils/functions.php';
include '../utils/data.php';

header('Content-type: text/html; charset=utf-8');

session_init();

if (!isset($_SESSION['userid']) || !isset($_SESSION['userid']) || !$_SESSION['userid']) {
    header("location: admin_tools/index.php");
    return;
};

$mysqli = new mysqli($host, $db_name, $pass, $db_host);
$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$username = $row['email'];
?>


<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>TIMBRE Project</title>
        <link rel="stylesheet" type="text/css" href="../css/header.css">
        <link rel="stylesheet" type="text/css" href="../css/explore.css"> 
        <link rel="stylesheet" type="text/css" href="../css/login.css">    

        <script src="../js/ajax.js"   type="text/javascript"></script>
        <script src="../js/jquery-1.10.2.js" type="text/javascript"></script>
        <script src="../js/jquery-migrate-1.2.1.js" type="text/javascript"></script>

        <script src="../js/jquery-ui.min.js"                     type="text/javascript"></script>
        <script src="../js/jquery-validation/jquery.validate.js" type="text/javascript"></script>

        <script src="../js/framework_handler.js"   type="text/javascript"></script>

        <script src="../js/jquery.qtip/jquery.qtip-1.0.0-rc3.js"   type="text/javascript"></script>
        <script src="../js/autogrow.js"                            type="text/javascript"></script>

        <script src="../js/jquery.raty/js/jquery.raty.min.js" type="text/javascript"></script>
                
        <script type="text/javascript">
            $(document).ready(function()
            {
//                $('#ll')[0].click();
//                window.setInterval(function(){
//                    $('#ll')[0].click();
//                  }, 2000);
            });
        </script>
    </head>
    
    
    <body>
        <div id="navigation_div">
            <span id="position"><a href="searchaims.php">Timbre</a> > Administration Tools</span>
            <span id="profile"><a href="user_info.php"><?php echo $username ?></a> - <a href="../logout.php">Logout</a>&nbsp;</span>
        </div>
        <div id="header">
            <img id="logo" src="../img/logo2.png" />

            <img id="header_bkg" src="../img/bkg_header.png" />

            <span id="timbrelink">the timbre project: <a target="_blank" href="http://www.timbre-project.eu/">http://www.timbre-project.eu/</a></span>
        </div>
        <div style="width:100%; border:1px; border-style: solid; ">
            <a href="workshoptable.php" target='frame' id="ll">Workshop Table</a>&nbsp;
            <a href="searchaimsexplorer.php" target='frame' id="ll">Search Aims Explorer</a>&nbsp;
            <a href="ratingexplorer.php" target='frame' id="ll">Rating Explorer</a>&nbsp; 
            <a href="sessionexplorer.php" target='frame' id="ll">Sessions Explorer</a>&nbsp; 
            <a href="bindsa.php" target='frame' id="ll">Bind Search Aims</a>&nbsp; 
            <a href="testlinks.php" target='frame' id="ll">Broken Links Test</a>&nbsp; 
            <a href="duplicatedlinks.php" target='frame' id="ll">Duplicated Links</a>&nbsp; 
            <a href="query_log.php" target='frame'>Query Log</a>&nbsp; 
<!--            <a href="settings.php" target='frame'>General Settings</a>   -->
        </div>
        <iframe name="frame" style="width:100%; height:75%; margin-top:20px;" src="">
        </iframe>
    </body>
</html>