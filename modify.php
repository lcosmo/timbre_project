<?php
include 'utils/functions.php';
include 'utils/data.php';
session_init();

if (!isset($_SESSION['userid'])) {
    header("location: index.php");
    return;
};
$mysqli = new mysqli($host, $db_name, $pass, $db_host);
$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$username = $row['username'];
$userid=$row['id'];
$isadmin = $row['admin']==1;
    
if (isset($_GET['insertitem'])) {
    $array = array(
        'user' =>  $_SESSION['userid'],
        'phase' => $_POST['insert_phase_select'],
        'typology' => $_POST['insert_typ_select'],
        'subcategory' => $_POST['subcategory'],
        'country' => $_POST['country'],
        'influence' => $_POST['influence'],
        //$avinen = $mysqli->real_escape_string($_POST['avineng'])=="Yes";
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'lang_ori' => $_POST['language'],
        'link_ori' => $_POST['link_ori'],
        'link_eng' => $_POST['link_eng'],
        'lang_other' => $_POST['lang_other'],
        'link_other' => $_POST['link_other'],
        'moreinfo' => (isset($_POST['moreinfo']) && $_POST['moreinfo'] == 'yes')?serialize($_POST):'',
        'date' => date("Y/m/d H:i:s"),
        'rating1' => $_POST['rating1'],
        'rating2' => $_POST['rating2'],
        'rating3' => $_POST['rating3'],
        'rating' => ($_POST['rating1'] + $_POST['rating2'] + $_POST['rating3'])/3,
        'sustainable' => isset($_POST['sustainable'])?1:0
    );
    
    $more_info = "";
    if (isset($_POST['moreinfo']) && $_POST['moreinfo'] == 'yes')
        $more_info = serialize($_POST);
    
    insert($mysqli, "project", array_keys($array), array_values($array));
}

if (isset($_GET['modifyitem'])) {
    $pid = $_GET['id'];
            
    $array = array(
        'user' =>  $_SESSION['userid'],
        'id' => $_GET['id'],
        'phase' => $_POST['insert_phase_select'],
        'typology' => $_POST['insert_typ_select'],
        'subcategory' => $_POST['subcategory'],
        'country' => $_POST['country'],
        'influence' => $_POST['influence'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'lang_ori' => $_POST['language'],
        'link_ori' => $_POST['link_ori'],
        'link_eng' => $_POST['link_eng'],
        'lang_other' => $_POST['lang_other'],
        'link_other' => $_POST['link_other'],
        'moreinfo' => (isset($_POST['moreinfo']) && $_POST['moreinfo'] == 'yes')?serialize($_POST):'',
        'date' => date("Y/m/d H:i:s"),
        'rating1' => $_POST['rating1'],
        'rating2' => $_POST['rating2'],
        'rating3' => $_POST['rating3'],
        'sustainable' => isset($_POST['sustainable'])?1:0
    );

    insertOrUpdate($mysqli, "project",
           array_keys($array), array_values($array) ,
           array(2,3,4,5,6,7,8,9,10,11,12,13,14,16,17,18,19));
    
    


    //UPDATE RATING
    $result = $mysqli->query(
<<<EOF
   SELECT p.id, p.rating1, p.rating2, p.rating3, 
          count(r.rating1) as c, 
          avg(r.rating1) as r1, avg(r.rating2) as r2, avg(r.rating3) as r3, avg(r.rating4) as r4  
          FROM project p LEFT JOIN (SELECT * FROM rating r WHERE r.question1=1 AND r.question2=1) r
          ON r.rating1>0 AND r.project_id=p.id 
          WHERE p.id=$pid 
          GROUP BY p.id
EOF
);
    $row = $result->fetch_assoc();
    $rating =  (($row['rating1'] + $row['rating2'] + $row['rating3'])/3 + ($row['r1'] + $row['r2'] + $row['r3'] + $row['r4'])*$row['c']/4)/($row['c']+1);
    var_dump($row);
    var_dump($rating);
    
    $array = array(
      'id' => $pid,
      'rating'  => $rating
    );
    insertOrUpdate($mysqli, "project", array_keys($array), array_values($array), array(1));
}

if(!isset($_GET['id'])) exit();

$query = sprintf("select *  from project where id=%d",$_GET['id']);
//echo $query;
$result = $mysqli->query($query);	
$row = $result->fetch_array(MYSQLI_ASSOC);

if($row['user']!=$userid && !$isadmin)
{
    echo "Permission denied";
    exit();
}

header('Content-Type: text/xml');

$moreinfo = unserialize($row['moreinfo']);

$doc = new DomDocument('1.0');
$root = $doc->createElement('root');
$root = $doc->appendChild($root);
$root->setAttribute('id',$_GET['id']);

foreach($row as $col => $val)
{
    if($col!='moreinfo')
    {
    $country = $root->appendChild($doc->createElement("field"));
    $country->setAttribute('name',$col);
    $country->appendChild($doc->createTextNode($val));
    }
    else
    {
        if(sizeof($moreinfo)>1)
        foreach($moreinfo as $col => $val)
        {
    $country = $root->appendChild($doc->createElement("field"));
    $country->setAttribute('name',$col);
    $country->appendChild($doc->createTextNode($val));
        }
    }
}



$OUTXML = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
echo $OUTXML;

//$myFile = "post_log.html";
//$fh = fopen($myFile, 'w');
//fwrite($fh, $OUTXML);
//fclose($fh);
?>