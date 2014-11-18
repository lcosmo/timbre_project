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


for ($i = 0; $i < 1000; $i++) {
    if (isset($_POST['sel' . $i])) {
        $assid = $_POST['sel' . $i];
        $query = <<<EOF
UPDATE searchaims SET assid=$assid WHERE id=$i
EOF;

        $options = "";
        $result = $mysqli->query($query);
    }
}
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
                    tableToExcel('data', 'Search Aims')
                });
            });

            var tableToExcel = (function() {
                var uri = 'data:application/vnd.ms-excel;Content-Disposition: attachment; filename="downloaded.pdf";base64,'
                        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                        , base64 = function(s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function(s, c) {
                    return s.replace(/{(\w+)}/g, function(m, p) {
                        return c[p];
                    })
                }
                return function(table, name) {
                    if (!table.nodeType)
                        table = document.getElementById(table)
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
            <form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Search Aim Description</th>
                            <th>Search Aim Id</th>
                    </thead>

                    <tbody>
<?php
$query = <<<EOF
SELECT * FROM searchaims WHERE id<21
EOF;

$options = "";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) {
    $assid = $row['assid'];
    $desc = $row['description'];
    $options.="<option value='$assid'>$desc</option>";
}

$query = <<<EOF
SELECT * FROM searchaims WHERE id>21
EOF;


$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    $id = $row['id'];
    $aim = $row['description'];
    $cat = $row['assid'];


    //echo "<td>$id</td>";
    echo "<td>$aim</td>";
//                echo "<td><input type='text' value='$cat' size='1'/></td>";
    echo "<td><select name='sel$id' id='sel$id'><option value='-1'> - </option>$options</select></td>";
    echo "\r\n <script>$('#sel$id').find('[value=$cat]').attr('selected','true');</script>";
    echo "</tr>";
}
?>
                    </tbody></table>
                <input type="submit" value="Update" />
            </form>
        </div>
    </body>
</html>