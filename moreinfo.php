<?php
	include 'utils/functions.php';
	include 'utils/data.php';
	session_init();
	if(!isset($_SESSION['userid'])) { header("location: index.php"); return; };
	$mysqli = new mysqli($host, $db_name, $pass, $db_host);
	$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
	$result = $mysqli->query($query);	
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$username = $row['username'];

        $query = sprintf("select *  from project where id=%d",$_GET['id']);

	//echo $query;
	$result = $mysqli->query($query);	
	$row = $result->fetch_array(MYSQLI_ASSOC);
        $info = unserialize($row['moreinfo']);
        
        function get_mult($info, $name, $num)
        {
            $s="";
            if(array_key_exists($name, $info))
                    $s=  sprintf("%s, %s", $s, $info[$name]);
            for($i=1; $i<=$num;$i++)
                if(array_key_exists($name."_".$i, $info))
                    $s=  sprintf("%s, %s", $s, $info[$name."_".$i]);
            return substr($s,1);
        }
        
        ?>
        
        <table style="width:100%;white-space: nowrap;">
        <tr>
            <td class="bold">Title: </td><td><?=$row['title']?></td>
        </tr>
        <tr>
            <td class="bold">Country of reference:</td>
            <td><?=$row['country']?></td>
        </tr><tr>
            <td class="bold">Influence:</td>
            <td><?=$row['influence']?></td>            
        </tr><tr>
            <td class="bold">Description:</td><td><?=$row['description']?></td>
        </tr>
        </tr><tr class="additionalinfo">
            <td class="bold">Technology name:</td>
            <td><?=$info["technoloyname"]?></td>
        </tr><tr class="additionalinfo">
            <td class="bold">Technology type:</td>
            <td><?=get_mult($info, "technologytype",10)?></td>
        </tr><tr class="additionalinfo">
            <td class="bold">Environmental medium</td>
            <td><?=get_mult($info, "environmentalmedium",10)?></td>
        </tr>

        <tr class="additionalinfo">
            <td colspan="2" style="background-color: transparent"><br/><strong>Target contaminants and performance (%)</strong></td>
        </tr>
        <tr class="additionalinfo">
            <td>NHVOC Nonhalogenated volatile organic compounds:</td>
            <td><?=$info["nonhalogenatedvolatile"]?></td>
        </tr><tr class="additionalinfo">
            <td>HVOC Halogenated volatile organic compouds:</td>
            <td><?=$info["halogenatedvolatile"]?></td>
        </tr>
        <tr class="additionalinfo">
            <td>NHSVOC Nonhalogenated semivolatile organic compounds:</td>
            <td><?=$info["nonhalogenatedsemivolatile"]?></td>
        </tr><tr class="additionalinfo">
            <td>HSVOC Halogenated semivolatile organic compounds :</td>
            <td><?=$info["halogenatedsemivolatile"]?></td>
        </tr><tr class="additionalinfo">
            <td>Inorganics:</td>
            <td><?=$info["inorganics"]?></td>
        </tr><tr class="additionalinfo">
            <td>Metals / metalloids:</td>
            <td><?=$info["metals"]?></td>
        </tr><tr class="additionalinfo">
            <td>Fuels:</td>
            <td><?=$info["fuels"]?></td>
        </tr>
        </tr><tr class="additionalinfo">
            <td>Radionuclides:</td>
            <td><?=$info["radionuclides"]?></td>
        </tr>        
        </tr><tr class="additionalinfo">
            <td>Explosives:</td>
            <td><?=$info["explosives"]?></td>
        </tr>

        <tr class="additionalinfo">
            <td colspan="2" style="background-color: transparent"><br/><strong>Technology applicability conditions</strong></td>
        </tr>
        <tr class="additionalinfo">
            <td>Annual average temperature (°C):</td>
            <td><?=$info["annualtemperature"]?></td>
        </tr><tr class="additionalinfo">
            <td>Remediation technology time scale:</td>
            <td><?=$info["remediationtechnologytime"]?> <?=$info["remediationtechnologytime_unit"]?></td>
        </tr><tr class="additionalinfo">
            <td>Max achievable soil depth (m):</td>
            <td><?=$info["maxsoil"]?></td>
        </tr><tr class="additionalinfo">
            <td>Nature of soil:</td>
            <td><?=get_mult($info, "soilnature",10)?></td>
        </tr><tr class="additionalinfo">
            <td>Range of suitable organic carbon:</td>
            <td><?=$info["organiccarbon"]?></td>
        </tr>
        </tr><tr class="additionalinfo">
            <td>Costs:</td>
            <td><?=$info["costs"]?> <?=$info["costs_unit"]?></td>
        </tr>
<!--        </tr><td colspan="2" style="background-color: transparent">&nbsp;</td><tr class="additionalinfo">
        <tr class="additionalinfo">
            <td class="bold">Possibility to reuse the treated material:</td>
            <td><?=$info["possibilityreuse"]?></td>
        </tr><tr class="additionalinfo">
            <td class="bold">Possibility to use the remediation technology in a treatment train:</td>
            <td><?=$info["possibilityremediation"]?></td>
        </tr>-->
    </table>