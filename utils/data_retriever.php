<?php          
        //ini_set('display_errors',1);
        //ini_set('display_startup_errors',1);
        //error_reporting(-1);


	header('Content-Type: text/xml'); 

	include 'functions.php';
	include 'data.php';
	session_init();
	if(!isset($_SESSION['userid'])) { header("location: index.php"); return; };
	$mysqli = new mysqli($host, $db_name, $pass, $db_host);
	$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
	$result = $mysqli->query($query);	
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$username = $row['email'];
        $lang = $row['language'];
	$isadmin = $row['admin']==1;
                
        $uid = $_SESSION['userid'];
        
        if (isset($_POST['delete'])) {
            header('Content-Type: text/html');
            
            $query = "select * from project where id=".$_POST['id'];
            
            $result = $mysqli->query($query);	
            if($row = $result->fetch_array(MYSQLI_ASSOC))
            {
                if($row['user']==$_SESSION['userid'] || $isadmin)
                {
                    $query = "delete from project where id=".$_POST['id'];
                    $result = $mysqli->query($query);
                    exit;
                }
                else
                {
                    echo "ERROR: Permission denied";
                    exit;  
                }
            }
            else {
               echo "ERROR: Invalid item id";
               exit;
            }
            exit;
       }


	$phase = $_GET['phase']!="All"?$mysqli->real_escape_string($_GET['phase']):'%';
	$typ= $_GET['typ']!="All"?$mysqli->real_escape_string($_GET['typ']):'%';
        
        $count_array= preg_split ("/,/",$_GET['country'], -1, PREG_SPLIT_NO_EMPTY);

        if(sizeof($count_array)==0) $countries=" country LIKE '%' or ";
        else
        {
            $countries="";
            foreach($count_array as $s)
            {
                if($s=="All") { $countries=" country LIKE '%' or "; break; }
                $countries .= sprintf(" country = '%s' or ",$mysqli->real_escape_string($s));
            }
        }

	$search="";
//	if(isset($_GET['query']) && $_GET['query']!="")
//        foreach( preg_split ("/[\s,]+/",$_GET['query']) as $s)
//		$search .= sprintf('(title LIKE \'%%%1$s%%\' or description LIKE \'%%%1$s%%\' or moreinfo LIKE \'%%%1$s%%\') and ',$mysqli->real_escape_string($s));
//	$query = sprintf("select *, rating*(
//                 CASE WHEN lang_ori LIKE '%s' OR lang_other LIKE '%s' THEN 1.1
//                 WHEN lang_ori LIKE 'English' OR NOT (ISNULL(link_eng) OR link_eng LIKE '') THEN 1
//                 ELSE 0.9 END             
//                 )*
//                 IF(country='%s',1.5,1)
//                 *health as wrating  from project where %s phase LIKE '%s' and typology LIKE '%s' AND (%s FALSE) order by phase, typology",$lang,$lang,$_SESSION['searchaims']['prefered_country'],$search,$phase,$typ,$countries);

        $words=array(''); 
        $query = '';
        if(isset($_GET['query']) && $_GET['query']!="")
            $words = preg_split ("/[\s,]+/",$_GET['query']);

        foreach( $words  as $s)
        {
		$search = sprintf('(title LIKE \'%%%1$s%%\' or description LIKE \'%%%1$s%%\' or moreinfo LIKE \'%%%1$s%%\') and ',$mysqli->real_escape_string($s));
                if($query!='') $query = $query.' 
UNION ALL
                    '; 
                $query = $query.sprintf(" select *, rating*(
                 CASE WHEN lang_ori LIKE '%s' OR lang_other LIKE '%s' THEN 1.1
                 WHEN lang_ori LIKE 'English' OR NOT (ISNULL(link_eng) OR link_eng LIKE '') THEN 1
                 ELSE 0.9 END             
                 )*
                 IF(country='%s',1.5,1)
                 *health as twrating  from project where %s phase LIKE '%s' and typology LIKE '%s' AND (%s FALSE)",$lang,$lang,$_SESSION['searchaims']['prefered_country'],$search,$phase,$typ,$countries);
        }
        $query = sprintf('select *,  twrating*(1+(count(*)-1)*1) as wrating from (%s) data group by data.id', $query);

        if($phase=='%' && !(isset($_GET['user']) && $_GET['user']!=''))
            $query = sprintf("select *, s.sequential_order as ord FROM (%s) c, searchpreferences s WHERE s.usersessions_id=%d AND c.phase = s.category_name", $query, $_SESSION['sessionid']);            
        else
            $query = sprintf("select *, 0 as ord FROM (%s) o",$query);
//        $query = sprintf("select *,  IFNULL(o.sequential_order,99) as ord from (%s) c LEFT OUTER JOIN (SELECT category_name, sequential_order FROM searchpreferences WHERE `usersessions_id`=%d) o
//	ON c.phase = o.category_name", $query, $_SESSION['sessionid']);
        
         
        $query = sprintf("select *, count(vl.vid) as vnum 
                          FROM (%s) c LEFT OUTER JOIN 
                          ( SELECT vl.id as vid, vl.project_id 
                            FROM visitedlinks vl, usersessions us 
                            WHERE us.id=vl.usersessions_id AND us.users_id=$uid AND 
                                NOT EXISTS(SELECT * FROM rating r WHERE r.project_id=vl.project_id AND r.user_id=$uid)
                           ) vl ON c.id=vl.project_id GROUP BY c.id", $query);
        $query = sprintf("select item.*, count(r.id) as rated from (%s) item LEFT OUTER JOIN rating r ON item.id=r.project_id AND item.user = r.user_id group by item.id",$query);
        
        $query = sprintf("select item.*, c.id as cid, c.name as name from category_name c, (%s) item WHERE item.phase=c.name",$query);
        
        $query = sprintf("select item.*, count(distinct vl.users_id) as nvisits, ceil(sum(if(r.question2=0,1,0))/count(*)) as inap, ceil(sum(if(r.rating1>0,1,0))/count(*)+1) as nrates FROM (%s) item LEFT OUTER JOIN (SELECT vl.*, us.users_id FROM visitedlinks vl, usersessions us WHERE vl.usersessions_id=us.id) vl ON item.id=vl.project_id LEFT OUTER JOIN rating r ON item.id=r.project_id GROUP BY item.id order by item.ord, item.cid, item.phase, item.typology, item.wrating desc", $query);

        if(isset($_GET['user']) && $_GET['user']!='')
            $query = sprintf("select * from (%s) data where data.user=%d",$query,$_GET['user']);
        
        $result = $mysqli->query($query);
        
        //var_dump($result->num_rows);
  //      echo $query;
//        die();
	$lastphase = "-1";
	$lasttyp = "-1";
	
        /**BUILDING RESPONSE**/
	$doc = new DomDocument('1.0');
	$root = $doc->createElement('root');
	$root = $doc->appendChild($root);
	$root->setAttribute('id',$_GET['id']);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{	
//            var_dump($row);
            if(isset($_GET['debug'])){echo "<pre>"; print_r($row); echo "</pre>"; continue; }
            
//            print_r($row);
            if($row['phase']!=$lastphase)
            { 
                    $phase = $root->appendChild($doc->createElement('phase'));
                    $phase->setAttribute('name',$row['phase']);
                    $lastphase=$row['phase'];
                    $lasttyp="-1";
            }
            //echo print_r($row)."-";
            if($row['typology']!=$lasttyp)
            {
                    $typ = $phase->appendChild($doc->createElement('typology'));
                    $typ->setAttribute('name',$row['typology']);
                    $lasttyp=$row['typology'];
                    
            }
            $item = $typ->appendChild($doc->createElement('item'));

            foreach($row as $col => $val)
            {
                if($col=="pahse" || $col=="typology" || $col=="link_ori" || $col=="lang_ori" || $col=="moreinfo") continue;
                $country = $item->appendChild($doc->createElement($col));
                $country->appendChild($doc->createTextNode($val));
//                    $country->appendChild($doc->createTextNode(mb_convert_encoding($val,'UTF-8','ISO-8859-1')));
            }

            $moreinfo = $item->appendChild($doc->createElement('moreinfo'));
            $moreinfo ->appendChild($doc->createTextNode($row['moreinfo']!=''?'yes':'no'));

            $link_ori = $item->appendChild($doc->createElement('link_ori'));
            $link_ori->appendChild($doc->createTextNode(mb_convert_encoding($row['link_ori'],'UTF-8','ISO-8859-1')));
            $link_ori->setAttribute('lang',mb_convert_encoding($row['lang_ori'],'UTF-8','ISO-8859-1'));

            $canmod = $item->appendChild($doc->createElement('canmodify'));
            $mod=$row['user']==$_SESSION['userid'] || $isadmin;
            $canmod->appendChild($doc->createTextNode($mod!=''?'true':'false'));
                
//                $vnum = $item->appendChild($doc->createElement('vnum'));
//                $vnum ->appendChild($doc->createTextNode($row['num']));
	}
	
	$OUTXML = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
	echo $OUTXML;	
?>