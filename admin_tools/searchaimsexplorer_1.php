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
        <button id="btnExport">Export as Excel</button>
        <div id="data">
        <table>
            <thead>
                <tr><th>Stakeholder Category</th><th>Name</th><th>Surname</th><th>Email</th><th>Nr of evaluated  Links</th><th>Search Aim</th><th>Nr of clicked Links</th><th>Session date</th></tr>
            </thead>
            <tbody>
        <?php
            $ustable = " SELECT u.*, sum(if(r.id IS NULL, 0, 1)) as rated " .
                       " FROM user u LEFT JOIN rating r ON u.id=r.user_id " .
                       " GROUP BY u.id";
            
             $query = "SELECT sess.*, count(*) as visited_links FROM " .
                      " (SELECT us.id, s.order, s.name as sname, u.name, u.rated, u.surname, u.email, us.aims, us.start_date " .
                      " FROM usersessions us, ($ustable) u, stkhcategories s, rel_users_stkhcategories ustk" .
                      " WHERE s.id = ustk.stkhcategories_id AND ustk.users_id = u.id AND us.users_id=u.id AND us.start_date > '2013-10-31 00:00:00'".
                      " UNION " .
                      " SELECT us.id, -1 as `order`, 'uncategorized' as sname, u.name, u.rated, u.surname, u.email, us.aims, us.start_date".
                      " FROM usersessions us, ($ustable) u " .
                      " WHERE NOT EXISTS ( SELECT * FROM rel_users_stkhcategories ustk where ustk.users_id = u.id) AND us.users_id=u.id AND us.start_date > '2013-10-31 00:00:00' ORDER BY `order`, start_date desc) sess" .
                      " LEFT JOIN visitedlinks vl ON vl.usersessions_id=sess.id".
                      " GROUP BY sess.id". 
                      " ORDER BY sess.order, sess.email";
             
             
//            $query = " SELECT 1 as border, '' as  sname, u.name, u.surname, u.email, us.aims, us.start_date FROM usersessions us, user u" .
//                     " WHERE  us.users_id=u.id AND us.start_date > '2013-10-31 00:00:00'".
//                     " ";
       
            $result = $mysqli->query($query);
            $prev_sname;
            $prev_email;
            while($row = $result->fetch_assoc())
            {
                echo "<tr>";
                $sname = $row['sname'];
                $name = $row['surname'];
                $surname = $row['name']; 
                $email = $row['email'];
                $aims = $row['aims'];
                $date = $row['start_date'];
                $click = $row['visited_links'];
                $rated = $row['rated'];
                
                if($sname==$prev_sname)
                    echo "<td class='inner'>&nbsp</td>";
                else
                {
                    echo "<td>$sname</td>";
                    $prev_email = '--------'; 
                }
                
                if($email==$prev_email)
                    echo "<td class='inner'>&nbsp</td><td class='inner'>&nbsp</td><td class='inner'>&nbsp</td><td class='inner'>&nbsp</td>";
                else
                    echo "<td>$name</td><td>$surname</td><td>$email</td><td>$rated</td>";
                
                echo "<td>$aims</td>";
                echo "<td>$click</td>";
                echo "<td>$date</td>";
                
                
                $prev_sname = $sname; 
                $prev_email = $email; 
                
                echo "</tr>";
            }
            
            
        ?>
            </tbody></table>
            </div>
    </body>
</html>