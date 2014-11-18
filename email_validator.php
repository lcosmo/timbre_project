<?php

include 'utils/data.php';

$mysqli = new mysqli($host, $db_name, $pass, $db_host);
$error = -1;

//$error = 0;
if (isset($_GET['validationCode'])) {
    $result = $mysqli->query("SELECT * FROM user WHERE controlkey='" . $mysqli->real_escape_string($_GET['validationCode']) . "'");

    if ($row = $result->fetch_assoc()) {
        $mysqli->query("UPDATE user SET verified=1, controlkey='' WHERE id=" . $row['id']);
        $error = 0;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
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

        <!--END HEAD-->


        <div id="login_div">
            <div id="login_title">Email Validation</div>
        <?php
        if ($error == 0)
        {
            echo "<p class='success'>The email " . $row['email'] . " has been validated. <br/> <a href='index.php'>Log in</a> with your credentials to use the Information System.</p>";

            echo 
<<<EOF
            <div style="font-size: 70%">
            <p>
               <strong>Would you like to become Expert User?</strong><br>
               If yes, please click <a href="superuser_request.php">here</a>.
            </p>
            <p>
               Expert User definition:<br>
               Expert Users will have system rights to modify and delete not updated information and web links. Users who wish to modify old or inappropriate information can ask to become Expert Users by providing information about their competences in the field and explaining why they require to become Expert User. The administrator of the system will evaluate the requests.
            </p>
            </div>            
EOF;
            
        }
        else
            echo "<p class='error'>Invalid code.</p>"
        
        ?>

        </div>
    </div>

</body>
</html>