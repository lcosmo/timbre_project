<?php

//       require_once "Mail.php";
//
//        $from = "<from.gmail.com>";
//        $to = "<luckmanera@gmail.com>";
//        $subject = "Hi!";
//        $body = "Hi,\n\nHow are you?";
//
//        $host = "ssl://smtp.gmail.com";
//        $port = "465";
//        $username = "<myaccount.gmail.com>";
//        $password = "password";
//
//        $headers = array ('From' => $from,
//          'To' => $to,
//          'Subject' => $subject);
//        $smtp = Mail::factory('smtp',
//          array ('host' => $host,
//            'port' => $port,
//            'auth' => true,
//            'username' => $username,
//            'password' => $password));
//
//        $mail = $smtp->send($to, $headers, $body);
//
//        if (PEAR::isError($mail)) {
//          echo("<p>" . $mail->getMessage() . "</p>");
//         } else {
//          echo("<p>Message successfully sent!</p>");
//         }

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
<form method="post" action="recover_confirmation.php">
<table >
<tr><td colspan="2">Insert the email address that you have used to register on Timbre.</td></tr>
<tr><td >Email:</td><td><input class="text_input" id="username" type="text" name="username" style="width:350px"/></td></tr>
<tr><td>&nbsp;</td><td>
<input id="login" id="Recover" type="submit" name="login" value="Recover" />
</td></tr>
</table>
</form>
</div>


</body>
</html>