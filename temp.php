<?php
	include 'utils/functions.php';
	include 'utils/data.php';
        header('Content-type: text/html; charset=utf-8');
     
        
        $mysqli = new mysqli($host, $db_name, $pass, $db_host);       
        
        /*
        $query = "SELECT aims from usersessions WHERE ID>517";
        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        
        $id=0;
        $aims = explode(";", $row['aims']);
        foreach ($aims as $aim)
        {
            $query = "INSERT INTO searchaims(id,description) VALUES (".($id+1).",'$aim')";
            $result = $mysqli->query($query);          
            echo "<p>".$mysqli->error."</p>";
            $id=($id+11)%21;
        }
        */
        /*
        $query = "SELECT aims from usersessions WHERE id<400";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            
            $aim = $row['aims'];
            if($aim=="") continue;
            echo "$aim<br>";
            $query = "INSERT INTO searchaims(description) VALUES ('$aim')";
            $mysqli->query($query);          
            echo "<p>E: ".$mysqli->error."</p>";
            
        }
        
        
        $query = "SELECT id, description, assid from searchaims order by assid";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            echo "<p>".$row['assid']." > ".$row['description']."</p>";
        }
        */
        
        $query = "SELECT id, aims from usersessions WHERE aims!=''";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $aims = explode(";", $row['aims']);
            $usid = $row['id'];
            foreach ($aims as $aim)
            {
                $aim = $mysqli->escape_string($aim);
                $query = "INSERT INTO rel_usersessions_searchaims(usersessions_id,searchaims_id) SELECT '$usid' as id, id as aid FROM searchaims WHERE description = '$aim'";
                $mysqli->query($query);          
                echo "<p>".$mysqli->error."</p>";
            }
        }
         
        
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
