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
$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$username = $row['email'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>TIMBRE Project</title>
        <script src="../js/ajax.js"   type="text/javascript"></script>
        <script src="../js/jquery-1.10.2.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#btnExport").click(function(e) {
                    //window.open('data:application/vnd.ms-excel,' + $('#data').html());
                    //e.preventDefault();
                    tableToExcel('data','Search Aims')
                });
            });
            
            var tableToExcel = (function() {
                var uri = 'data:application/vnd.ms-excel;Content-Disposition: attachment; filename="downloaded.pdf";base64,'
                  , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                return function(table, name) {
                  if (!table.nodeType) table = document.getElementById(table)
                  var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                  window.location.href = uri + base64(format(template, ctx))
                }
              })();

        </script>
        
        <style>
            table
            {
                border:1px;
                border-style: solid;
                border-collapse: collapse;                
            }
            table td, table th{
                border:1px;
                border-bottom: 0px;
                border-style: solid;
                border-collapse: collapse;
                margin: 0px;
            }
            
            table th{
                padding:3px;
            }
            table td.inner
            {
                
                border-top: 0px;
            }
            
        </style>
    </head>
    
    
    <body>
        <button id="btnExport"><a href="?checkall">Check All Links</a></button>
        <button id="btnExport"><a href="?checkbroken">Check Broken Links</a></button>
        
        <button id="btnExport">Export as Excel</button>
        <div id="data" style="width:100%">
        <table style="width:100%">  
            <thead>
                <tr><th>Error</th><th>Type</th><th>Link</th><th>Project</th></tr>
            </thead>
            <tbody> 
        <?php
            function test_link($url, $id,  $project, $type)
            {
                if($url=='') return;
                
                $ch = curl_init($url);//$row['link_ori'] link_eng link_other
                curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_CONNECT_ONLY, true);
                curl_exec($ch);
                $health=1;
                
                if(!curl_errno($ch))
                {
                    $info = curl_getinfo($ch);
                    if($info['http_code']!=200)
                    {
                        if($info['http_code']==404)
                           $health = 0.2;
                       else
                           $health = 0.9;                    
                    }             
                 }
                else
                {
                    $health = 0;
                    $info['http_code'] = 0;
                    
                }
                curl_close($ch);
                flush();
                
                return array($health,$info['http_code']);
            }
             
            if(isset($_GET['checkall']) || isset($_GET['checkbroken']))
            {
                if(isset($_GET['checkall']))
                   $query = "SELECT * FROM project p";
                else
                   $query = "SELECT * FROM project p where status!=200"; 
                $result = $mysqli->query($query); 
                while($row = $result->fetch_assoc())
                {   
                     $health = test_link($row['link_ori'],$row['id'], $row['title'], "original");
                     $mysqli->query(sprintf("UPDATE project SET health=%f, status=%d, last_check=CURRENT_TIMESTAMP WHERE id=%d",$health[0],$health[1],$row['id']));
                }
            }
            
            $query = "SELECT * FROM project p";
       
            $result = $mysqli->query($query); 
            while($row = $result->fetch_assoc())
            {               
//                $health = test_link($row['link_ori'],$row['id'], $row['title'], "original");//$row['link_ori'] link_eng link_other
//                test_link($row['link_eng'], $row['id'], $row['title'], "english");
//                test_link($row['link_other'],$row['id'], $row['title'], "other language");
                
                if($row['status']!=200)
                {
                    echo "<tr><td>" . $row['status'] . "</td><td>".$row['id']."</td><td><a href='" . $row['link_ori'] ."' target='blank'>" . substr($row['link_ori'],0,40) ."...</a></td><td>" . $row['title'] ."</td></tr>";
                }       
//                $mysqli->query(sprintf("UPDATE project SET health=%f, status=%d, last_check=CURRENT_TIMESTAMP WHERE id=%d",$health[0],$health[1],$row['id']));
//                var_dump($mysqli->error);
            }
            
             
        ?>
            </tbody></table>
            </div>
    </body>
</html>