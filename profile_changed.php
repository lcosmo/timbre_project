<?php
	include 'utils/functions.php';
	include 'utils/data.php';
        header('Content-type: text/html; charset=utf-8');
        session_init();
        


        
        $mysqli = new mysqli($host, $db_name, $pass, $db_host);        
        $error = 0;

        $query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $username = $row['email'];
        $password = $row['password'];
        $uid = $row['id'];
        //var_dump($_POST);
        if(isset($_POST['change_user_info']))
        {            
            if(isset($_POST['password']) && $_POST['password']!="")
            {
               
                if(md5($_POST['prevpassword'].'wp1')==$password )
                {
                    $array = array(
                        'id' => $uid,
                        'password' => md5($_POST['password'].'wp1'),
                        'language' => $_POST['nationality']
                    );
                    insertOrUpdate($mysqli, 'user', array_keys($array), array_values($array), array(1,2));
                }
                else
                    $error=1;
            }
            else
            {
                $array = array(
                    'id' => $uid,
                    'language' => $_POST['nationality']
                );
                insertOrUpdate($mysqli, 'user', array_keys($array), array_values($array), array(1));
            }
        }       
?>

<html>
<head>

<title>TIMBRE Project</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/explore.css"> 
        <link rel="stylesheet" type="text/css" href="css/login.css">     
        
<script src="./js/jquery.js" type="text/javascript"></script>        
<script type="text/javascript">

</script>


<style type="text/css">
    #login_div
{
	position:relative;
	left:50%;
	        
	border-color: rgb(130, 90, 87);
        border-style: solid;
        border-width: 0px 2px 2px 2px;
    
	width: 700px;
	margin-left: -350px;
	
	margin-top: 3%;
	
}
    
</style>
    
</head>



<body>
<!--HEAD-->
        <div id="navigation_div">
            <span id="position"><a href="searchaims.php">Timbre</a> > search aim </span>
            <span id="profile"><a href="user_info.php"><?php echo $username ?></a> - <a href="logout.php">Logout</a>&nbsp;</span>
        </div>
        <div id="header">
            <img id="logo" src="img/logo2.png" />

            <img id="header_bkg" src="img/bkg_header.png" />

            <span id="timbrelink">the timbre project: <a target="_blank" href="http://www.timbre-project.eu/">http://www.timbre-project.eu/</a></span>
        </div>
        <div id="disclaimer_overlay_right">
                <table>
                <tr>
                    <td>
                        This research project has received funding from the European Community’s Seventh Framework Programme (2011-2014) under grant agreement no. 265364
                    </td>
                    <td>
                        <img src="img/flogo1.png" /></td><td>
                        <img src="img/flogo2.png" />
                    </td>
                </tr>
                </table>
        </div>
<!--END HEAD-->


<div id="login_div">
<div id="login_title">User Profile</div>
<?php
if($error==0)
    echo "<p class='success'> User profile saved.<br><a href='explore.php'>Return to your search.</a></p>";
else
    echo "<p class='error'> Wrong password.<br> <a href='user_info.php'>Return to your profile.</a></p>"
?>
</div>


</body>
</html>