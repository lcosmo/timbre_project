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
                color: gray;
                border-top: 0px;
            }
            
        </style>
    </head>
    
    
    <body>
        <button id="btnExport">Export as Excel</button>
        <div id="data">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Search Aims</th>
                    <th>Selected categories of information</th>
                    <th>Session date</th></tr>
            </thead>
            <tbody>
        <?php
            $query = <<<EOF
SELECT us.start_date, u.name, u.surname, u.email, 
GROUP_CONCAT(DISTINCT CAST(stc.order AS CHAR)  SEPARATOR ',') as cat, 
GROUP_CONCAT(CAST(sa.assid AS CHAR)  SEPARATOR ',') as aims 
FROM usersessions us, rel_usersessions_searchaims rus, user u, rel_users_stkhcategories rsu, stkhcategories stc, searchaims sa
 WHERE us.id=rus.usersessions_id and u.id=us.users_id and rsu.users_id=u.id and stc.id=rsu.stkhcategories_id and sa.id=rus.searchaims_id
group by us.id
EOF;
                    
       
            $result = $mysqli->query($query);
            $prev_name;
            $prev_email;
            $prev_sid;
            while($row = $result->fetch_assoc())
            {
                echo "<tr>";
                $date = $row['start_date'];
                $name = $row['name'];
                $surname = $row['surname']; 
                $email = $row['email'];
                $aims = $row['aims'];
                $cat = $row['cat'];
                
                if(true || $sid!=$prev_sid)
                {
//                    if($email==$prev_email)
//                        echo "<td class='inner'>$name</td>";
//                    else
//                    {
//                        echo "<td>$name</td>";
//                        $prev_email = '--------'; 
//                    }

                    if($email==$prev_email)
                        echo "<td class='inner'>$name</td><td class='inner'>$surname</td><td class='inner'>$email</td>";
                    else
                        echo "<td>$name</td><td>$surname</td><td>$email</td>";

                    echo "<td>$aims</td>";
                    echo "<td>$cat</td>";
                    echo "<td>$date</td>";
                }
                else
                {
//                    echo "<td class='inner'>$name</td><td class='inner'>$surname</td><td class='inner'>$email</td>".
//                         "<td class='inner'>$aims</td><td class=''>$cat</td><td class='inner'>$date</td>";
                }
                $prev_name = $name; 
                $prev_email = $email; 
                $prev_sid = $sid;
                echo "</tr>";
            }
        ?>
            </tbody></table>
            </div>
    </body>
</html>