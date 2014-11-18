<?php
include '../utils/functions.php';
include '../utils/data.php';

session_init();
$page='admin_frame.php';

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
        else if(!$row['verified'] || !$row['admin'])
        {
            $wrong = 2;
        }
        else{
            // Start the session of the user	
            $_SESSION['admin'] = 1; 
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
        <link rel="stylesheet" type="text/css" href="../css/header.css">
        <link rel="stylesheet" type="text/css" href="../css/login.css">    
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
            <span id="position">Timbre Administration Tools</span>

        </div>
        <div id ="timbre_description" >
           
        </div>


        <div id="login_div">
            <div id="login_title">Login</div>
            <div id="login_form">
                <form method="post" style="margin: 0; padding:0;">
                    <table>
                        <tr><td>Email:</td><td><input class="text_input" id="username" type="text" name="username" /></td></tr>
                        <tr><td>Password:</td><td><input class="text_input" id="password" type="password" name="password" /></td></tr>
<?php if ($wrong==1) echo "<tr><td colspan='2'><span class='error'>Wrong username or password!</span></td></tr>"; ?>
<?php if ($wrong==2) echo "<tr><td colspan='2'><span class='error'>Access denied!</span></td></tr>"; ?>
                        <tr><td>&nbsp;</td><td style="height: 28px">
                                <a href="">&nbsp;</a>
                                <input id="login" class="button" type="submit" name="login" value="Login" />
                            </td></tr>
                    </table>
                </form>
            </div>

            
        </div>

        <div id="footer" style="margin-top: 120px">
            
        </div> 

    </body>
</html>