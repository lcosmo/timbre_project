<?php
include 'utils/functions.php';
include 'utils/data.php';

session_init();
$page='searchaims.php';
if(isset($_SESSION['nextpage']))
    $page=$_SESSION['nextpage'];

$wrong = false;
if (isset($_POST['login'])) {
    
    $mysqli = new mysqli($host, $db_name, $pass, $db_host);
    if (
        $mysqli->real_escape_string($_POST['username']) != '' /* && $mysqli->real_escape_string($_POST['password'])!= '' */) {
        $query = sprintf("select *  from user where email='%s' and password='%s'", $mysqli->real_escape_string($_POST['username']), md5($_POST['password'].'wp1'));
        
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row == null) {
            $wrong = 1;
        } 
        else if(!$row['verified'])
        {
            $wrong = 2;
        }
        else{
            // Start the session of the user			
            $_SESSION['user'] = $row['email'];
            $_SESSION['userid'] = $row['id'];

            header("location: ".$page);
        }
    }
}
?>
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
        <div id="navigation_div">
            <span id="position">Introduction to Timbre</span>

        </div>
        <div id ="timbre_description" >
            <p>The European FP7 project timbre - Tailored Improvement for Brownfield
                Regeneration in Europe - aims to support end-users in overcoming existing
                barriers in brownfield regeneration by developing and providing customised
                problem and target-oriented packages of technologies, approaches and
                management tools for a megasite's reuse planning and remediation.</p>
            <p>
                Therefore, this Information System in meant to be an information platform and
                an information management tool to support experts and end-users to get
                access to all the available information concerning brownfields regeneration
                processes.  
            </p>
        </div>


        <div id="login_div">
            <div id="login_title">Login</div>
            <div id="login_form">
                <form method="post" style="margin: 0; padding:0;">
                    <table>
                        <tr><td>Email:</td><td><input class="text_input" id="username" type="text" name="username" /></td></tr>
                        <tr><td>Password:</td><td><input class="text_input" id="password" type="password" name="password" /></td></tr>
<?php if ($wrong==1) echo "<tr><td colspan='2'><span class='error'>Wrong username or password!</span></td></tr>"; ?>
<?php if ($wrong==2) echo "<tr><td colspan='2'><span class='error'>Email address not verified!</span></td></tr>"; ?>
                        <tr><td>&nbsp;</td><td style="height: 28px">
                                <a href="recover.php">Forgot password?</a>
                                <input id="login" class="button" type="submit" name="login" value="Login" />
                            </td></tr>
                    </table>
                </form>
            </div>

            <div id="register_form">
                &nbsp;<input id="register" class="button" type="button" name="register" value="Register" onClick="window.location='user_registration.php'" />
                <span id="new_user">New user?&nbsp;&nbsp;</span>
            </div>
        </div>

        <div id="footer" style="margin-top: 120px">
            <table id="disclaimer">
                <tr>
                    <td>
                        <strong>Disclaimer</strong><br />
                        This software is aimed at assisting brownfield regeneration stakeholders. It is provided for information purposes only and its contents are not intended to replace consultation of any applicable legal sources or the necessary advice of a legal expert, where appropriate. This software has been produced in the context of the Timbre Project. The research leading to these results has received funding from the European Community's Seventh Framework Programme (FP7 2011-2014) under grant agreement no 265364. All information in this software is provided "as is" and no guarantee or warranty is given that the information is fit for any particular purpose. The user therefore uses the information at its sole risk and liability. For the avoidance of all doubts, the European Commission has no liability in respect of this software, which is merely representing the Timbre consortium view.    
                    </td>
                    <td>
                        <img src="img/flogo1.png" /></td><td>
                        <img src="img/flogo2.png" />
                    </td>
                </tr>
        </div> 

    </body>
</html>