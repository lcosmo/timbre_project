<?php
        $phases[0] = 'Strategic planning';
        $phases[1] = 'Investigation (preliminary/detailed)';
        $phases[2] = 'Risk assessment (qualitative/quantitative)';
        $phases[3] = 'Remediation strategies and options';
        $phases[4] = 'Remediation technologies evaluation and selection';
        $phases[5] = 'Building and infrastructure documents';
        $phases[6] = 'Deconstruction/re-use of structures materials';
        $phases[7] = 'Waste management';
        $phases[8] = 'Requalification plan development';
        $phases[9] = 'Implementation, control, monitoring (land back to market)';
        $phases[10] = 'Socio-economic assessment';
        $phases[11] = 'Funding and financing';
        $phases[12] = 'Decision-making and communication';
        
        error_reporting(-1);
	include 'functions.php';
	include 'data.php';
	session_init();
	
        header('Content-Type: text/html');
        ini_set('display_errors',1);
        ini_set('display_startup_errors',1);
        
        if(!isset($_SESSION['userid'])) { header("location: index.php"); return; };
	$mysqli = new mysqli($host, $db_name, $pass, $db_host);
	$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
	$result = $mysqli->query($query);	
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$username = $row['email'];
        $lang = $row['language'];
	$isadmin = $row['admin']==1;
		
        $uid = $_SESSION['userid'];
        
        
        $query = "SELECT count(*) FROM searchpreferences s WHERE s.usersessions_id = ".$_SESSION['sessionid'];
        $result = $mysqli->query($query);
        $row = $result->fetch_array();
               
        
        if(isset($_GET['cat']))
        {
            $i = $_GET['catid'];
            $array = array(
                'usersessions_id' => $_SESSION['sessionid'],
                'category_id' => $i,
                'category_name' => $_GET['cat'],
                'sequential_order' => $row[0]+1
            );
            $_SESSION['selectedcategories'][$array['sequential_order']] = $array;
            insert($mysqli, 'searchpreferences', array_keys($array), array_values($array)); 
        }
        
        $query = "SELECT sequential_order, category_id, category_name FROM searchpreferences s WHERE s.usersessions_id = ".$_SESSION['sessionid']." order by sequential_order" ;
        $result = $mysqli->query($query);

        $sel_phases = array(0,0,0,0,0,0,0,0,0,0,0, 0, 0);
        $ord_phases = array();
        $i=0;
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $ord_phases[sizeof($ord_phases)] = $row['category_id']-1;
            $sel_phases[$row['category_id']-1]=1;
        }
        for($i=0; $i<13; $i++)
        {
            if($sel_phases[$i]==0)
                $ord_phases[sizeof($ord_phases)] = $i;
        }
?>

<li class="search_all" style=""><a class='phase' href="javascript:void(0);"><span>All</span></a></li>
<?
$i=0;
for($i=0; $i<sizeof($phases); $i++)
{
    $p=$phases[$ord_phases[$i]];
    $phid = $ord_phases[$i]+1;
    if($sel_phases[$ord_phases[$i]]==0)
        echo "<li><a class='phase' href=\"javascript:void(0);\"><span class='categorynotselected' phase='$p' phid='$phid'>" . $p . "</span></a></li>";
    else
        echo "<li><a class='phase' href=\"javascript:void(0);\"><span class='prefered' phase='$p' phid='$phid'>" . $p . "</span></a></li>";

}
?>