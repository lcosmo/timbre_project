<?php

    include 'utils/functions.php';
    include 'utils/data.php';

    $wrong=true;

    $mysqli = new mysqli($host, $db_name, $pass, $db_host);

    $query = sprintf("select *  from user where email='%s'", $mysqli->real_escape_string($_POST['username']));
    $result = $mysqli->query($query);
    
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $password = '';
    for ($i = 0; $i < 10; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    if($row = $result->fetch_array(MYSQLI_ASSOC))
    {
                $hash = md5($password.'wp1');
                query($mysqli,"UPDATE user SET password='$hash' WHERE id=".$row['id']);
                
                $email = $row['email'];
                $to = $row['email'];
		
		
		$subject = "[TIMBRE INFORMATION SYSTEM] Password recovery";
                $body = <<<EOF
According with your request, we have set a temporary password to your account.\n
You can access your account with the cretentials:\n
    Email:    $email\n
    Password: $password\n
Please, change the temporary password as soon as possible.\n\n
If you don't have recently requested such information, please contact us.
EOF;
	  if (mail($to, $subject, $body)) {
            $wrong=false;
          }
    }
    ?>  <!-- end of php tag-->

    <html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
<link rel="stylesheet" type="text/css" href="css/login.css">    
<script>
function cancel_input()
{
document.getElementById("username").value="";
document.getElementById("password").value="";
return false;
}
</script>
</head>
<body>
<!--HEAD-->
<div id="navigation_div">
<span id="position">Timbre</span>
<span id="profile"><a href="index.php">Please log in</a>&nbsp;</span>
</div>
<div id="header">
<img src="img/logo.jpg" />
<div id="h1"></div>
</div>
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
<div id="login_title">Password recovery</div>
<!--
<form id="login_form">
<p><label>Username:</label><input class="text_input" type="text" name="username" /></p>
<p><label>Password:</label><input class="text_input" type="password" name="password" /></p>
<input id="login" type="submit" name="login" value="Login" />
<input id="cancel" type="submit" name="login" value="Cancel" />
</form>
-->

<table>
<?php if($wrong) echo "<tr><td>The email address provided does not correspond to any registered user</td></tr>";
      else       echo "<tr><td>An email with your password has been successfully sent to the provided email address</td></tr>";
?>
<tr><td>&nbsp;</td><td>
</td></tr>
</table>
</div>


</body>
</html>