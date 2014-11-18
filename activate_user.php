<?php
	include 'utils/functions.php';
	include 'utils/data.php';
	session_init();
	//if(!isset($_SESSION['userid'])) { header("location: index.php"); return; };
	
	$mysqli = new mysqli($host, $db_name, $pass, $db_host);


	if(isset($_POST['Ok']))
	{
            $query = sprintf("select *  from user where email='%s'", $mysqli->real_escape_string(strtolower($_POST['email'])));
            $result = $mysqli->query($query);	
			echo $_POST['email']."<br/>";
			echo $_POST['email']."<br/>";
            echo $query."<br/>";
 		    if($row = $result->fetch_array(MYSQLI_ASSOC))
            {     
			
                if($row['password']!='')
                {
                    header("location: activate_user.php?error=password");
                    exit;  
                }
                
                try{
                insertOrUpdate($mysqli, 'user',
                        array('id','username','password'),
                        array(
                        $row['id'],
                        $mysqli->real_escape_string($_POST['user']),
                        $mysqli->real_escape_string($_POST['password'])
                        ),
                        array(1,2));
                header("location: activate_user.php?success");
                }
                catch(Exception $ex)
                {
                    header("location: activate_user.php?error=username"+$ex);
                    exit;
                }
            }
            else
            {
                header("location: activate_user.php?error=email");
                exit;
            }                
	}	
?>

<html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
<link rel="stylesheet" type="text/css" href="css/login.css">

<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
		
<script>
function cancel_input()
{
document.getElementById("name").value="";
document.getElementById("surname").value="";
document.getElementById("istitution").value="";
document.getElementById("qualification").selectedIndex=0;
return false;
}
  $(document).ready(function(){
    $("#myform").validate({
  rules: {
    password: "required",
    user: "required",
    password2: {
      equalTo: "#password"
    },
	email: 
	{
	required: true,
	email: true
	}
  }
});
  });
  
</script>
</head>
<body>
<!--HEAD-->
<div id="navigation_div">
<span id="position">Timbre</span>
<span id="profile">&nbsp;</span></div>
</div>
<div id="header">
<img src="img/logo.jpg" />
<div id="h1">User Activation</div>
</div>

<!--END HEAD-->


<div id="login_div">

<? 
if(isset($_GET['success']))
{
    echo "Your account has been succesfully activated.<br />Go to <a href='index.php'>Login</a> page.";
    header('Refresh: 5; url= index.php');
    echo "<!--";
}
?>   
<div id="login_title">Please fill the information below</div>

<form id="myform" method="post" id="activate_user.php">
<? 
if(isset($_GET['error']))
{   
    echo "<span class='error'>";
    if($_GET['error']=='email')
        echo "Invalid email address.";
    else if($_GET['error']=='username')
        echo "The username already exists.";
    else if($_GET['error']=='password')
        echo "The selected user has already been activated. Please go to the <a href='index.php'>Login Page</a> and insert your credential.";
    else echo "Unknown error"; 
    
    echo "</span>";
}
?>
<table>
<tr><td>Email:</td><td><input class="text_input" id="email" type="text" name="email" value="" /></td></tr>
<tr><td>Insert new username:</td><td><input class="text_input" id="user" type="text" name="user" value="" /></td></tr>
<tr><td>Insert new password:</td><td><input class="text_input" id="password" type="password" name="password" value="" /></td></tr>
<tr><td>Confirm the password:</td><td><input class="text_input" id="password2" type="password" name="password2" value="" /></td></tr>
</td></tr>

<tr><td>&nbsp;</td><td>
<input id="Ok" type="submit" name="Ok" value="OK" />
</td></tr>
</table>
</form>

<? echo  isset($_GET['success'])?"-->":""; ?>   
</div>


</body>
</html>