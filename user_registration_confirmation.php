<?php
	include 'utils/functions.php';
	include 'utils/data.php';
        header('Content-type: text/html; charset=utf-8');
     
        
        $mysqli = new mysqli($host, $db_name, $pass, $db_host);        
        $error = -1;
        //var_dump($_POST);
        if(isset($_POST['email']))
        {
            $activationkey = md5(uniqid(rand(), true));
            $array = array(
                'email' => $_POST['email'],
                'password' => md5($_POST['password'].'wp1'),
                'language' => $_POST['nationality'],
                'controlkey' => $activationkey,
                'verified' => 1,
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'organization' => $_POST['organization'],
                'otherdata' => $_POST['otherdata']
            );
            
            $error = insert($mysqli, 'user', array_keys($array), array_values($array));
//            var_dump($mysqli->error);
            if($error==0)
            {
                //SEND MAIL
                $to = $mysqli->real_escape_string($_POST['email']);

                $subject = "[TIMBRE INFORMATION SYSTEM] Email confirmation";
                $body = "Thank you for registering for the \"TIMBRE INFORMATION SYSTEM\" web application.\nTo start using the Information System click the link below, or copy and paste it in the address bar of your browser.\n\nhttp://www.dais.unive.it/~timbre/EXPERTSYSTEM/email_validator.php?validationCode=$activationkey";

                $headers = 'From: timbre@dais.unive.it' . "\r\n";

                if (mail($to, $subject, $body)) {
                    $wrong=false;
                }
            
//              echo $error;
                for($i=1; $i<=17; $i++)
                {
                    if(! isset($_POST['cat_'.$i])) continue;
                    $array = array(
                    'name' =>  $_POST['cat_'.$i],
                    'predefined' => 0
                    );
                    insert($mysqli, 'stkhcategories', array_keys($array), array_values($array));

                    query($mysqli,
                    sprintf("INSERT INTO rel_users_stkhcategories(users_id, stkhcategories_id) 
                            SELECT u.id, s.id
                            FROM user u, stkhcategories s
                            WHERE u.email = '%s'
                            AND s.name = '%s'", $_POST['email'], $_POST['cat_'.$i]
                    ));
                }
            } 
            
        }       
?>

<html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
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
<span id="position">Timbre</span>
</div>
<div id="header">
<img src="img/logo.jpg" />

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
<div id="login_title">New user registration</div>
<?php
if($error==0)
    echo "<p class='success'> Registration successfully completed! A confirmation email has been sent to your email address.<br/>Please, follow the instructions in the email to activate your account.</p>";
else
    echo "<p class='error'> Registration NOT successfully completed! Please, verify the submitted data.</p>"
?>
</div>


</body>
</html>