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

        <div id="data">

            <table>
                <thead></thead>
                <tbody>

        <?php
            $query = "select * from (select link_ori, phase, typology, count(*) as n from project group by link_ori, phase, typology ) d where n>1";
            
            $result = $mysqli->query($query); 
            while($row = $result->fetch_assoc())
            {               
                echo "<tr><td colspan='3' style='font-weight: bold;'>".$row['phase']." - ".$row['typology']." - LINK: <a href='". $row['link_ori'] . "' target='_blank'>". $row['link_ori'] . "</a></td></tr>"; 
                $query = sprintf("select p.*, u.name, u.surname from project p, user u where p.user=u.id and link_ori = '%s' and phase='%s' and typology='%s' ",$row['link_ori'],$row['phase'],$row['typology']);
                
                //$query = sprintf("select p.*, '' as name, '' as surname  from project p where link_ori = '%s' and phase='%s' and typology='%s' ",$row['link_ori'],$row['phase'],$row['typology']);
                
                $result2 = $mysqli->query($query); 
                while($row2 = $result2->fetch_assoc())
                {  
                    echo "<tr><td>".$row2['user'].") ".$row2['name']." ".$row2['surname']."</td><td>".$row2['id']."</td><td>".$row2['title']."</td></tr>"; 
                }
            }            
             
        ?>
                    
                </tbody>
            </table>
    </body>
</html>