<?php
    include 'utils/functions.php';
    include 'utils/data.php';

    header('Content-type: text/html; charset=utf-8');

    session_init();
    $_SESSION['nextpage'] = 'superuser_request.php';

    if (!isset($_SESSION['userid'])) {
        header("location: index.php");
        return;
    };
    unset($_SESSION['nextpage']);
    
    $mysqli = new mysqli($host, $db_name, $pass, $db_host);
    $query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $userid = $row['id'];
    $username = $row['email'];   if (!isset($_SESSION['userid'])) {
        header("location: index.php");
        return;
    };     
    
    
    if(isset($_POST['superuser_request']))
    {   
        $name = $_POST['name'];
        $organization = $_POST['organization'];
        $position = $_POST['position'];
        $field = $_POST['field'];
        $years = $_POST['years'];
        $reason = $_POST['reason'];
        
        //SEND MAIL
        $to = $mysqli->real_escape_string('cosmo@dsi.unive.it,erika.rizzo@unive.it,lisa.pizzol@unive.it');

        $subject = "[TIMBRE INFORMATION SYSTEM] Expert User Request";
        $body = 
<<<EOF
USER ID: $userid
EMAIL:   $username

NAME AND SURNAME:    $name
ORGANIZAION:         $organization
POSITION:            $position
FIELD OF ACTIVITY:   $field
YEARS OF EXPERIENCE: $years

REASON:
$reason
               
EOF;
        $headers = 'From: Timbre Project<timbre@dsi.unive.it>' . "\r\n";

        if (mail($to, $subject, $body)) {
            $wrong=false;
        }
                
        header("location: user_info.php");
        return;
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/explore.css"> 
        <link rel="stylesheet" type="text/css" href="css/login.css">    

        <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css"> 
        <link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css"> 
        
        <title>TIMBRE Project</title>
        
        <script src="./js/jquery.js" type="text/javascript"></script>
        <script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
        
        <script type="text/javascript">
        $(document).ready(function() {
                $.validator.addMethod("valueNotEquals", function(value, element, arg){
                    return arg != value;
                }, "Value must not equal arg.");

                $.validator.addMethod("checkMail", function(value, element, arg){
                    return arg != value;
                }, "Value must not equal arg.");

                $.validator.setDefaults({
                    onkeyup: false
                })
              
                $("#user_info_form").validate({
                            rules: {
                                    prevpassword: {
                                            required: true,
                                            minlength: 6
                                    },
                                    password: {
                                            required: true,
                                            minlength: 6
                                    },
                                    confirm_password: {
                                            required: true,
                                            minlength: 6,
                                            equalTo: "#password"
                                    },
//                                    email: {
//                                            required: true,
//                                            email: true
////                                            ,remote:{  
////                                                url: "utils/validatorAJAX.php",  
////                                                type: "get"  
////                                            }                                  
//                                    },
                                    nationality: {
                                        required: true,
                                        valueNotEquals: ""
                                    }
                            },
                            success: function(label) {
                                label.html("<img src='img/success.png' height='15px' />");
                            },
                            messages: {
                                nationality: {
                                    valueNotEquals: "Please select an item!"
                                },
                                email: {
                                    remote: "This email address has been already used"
                                }
            }  
                    });

                });
            </script>
        
    </head>
    <body>   
        <div id="navigation_div">
            <span id="position"><a href="searchaims.php">Timbre</a> > Expert User request </span>
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
        <div id="login_div" style="width: 600; margin-left: -300px;">
            <div id="login_title">Expert User Request</div>      
            
            <form method="post" id="user_info_form" action="superuser_request.php">
            <table>
                <tr><td>Name and Surname:</td><td><input type="text" id="name" name="name" size="40em"></td></tr>
                <tr><td>Organisation you work for:</td><td><input type="text" id="organization" name="organization" size="40em"></td></tr>
                <tr><td>Position in the organisation you work for:</td><td><input type="text" id="posiion" name="position" size="40em"></td></tr>
                <tr><td>Field of activity:</td><td><input type="text" id="field" name="field" size="40em"></td></tr>
                <tr><td>Years of experience:</td><td><input type="text" id="years" name="years" size="40em"></td></tr>
                <tr><td>Reasons why you want to become a TIMBRE Information System Expert User:</td>
                <td><textarea type="text" id="reason" name="reason" cols="31" rows="3"></textarea></td></tr>
                
            <tr><td>&nbsp;</td><td>
            <input id="Ok" type="submit" name="superuser_request" id="superuser_request" value="Submit" style="float:right;" />
            <a href="<?=$_SERVER['HTTP_REFERER']?>"  style="float:right; margin-right: 10px;" >Cancel</a>

            </td></tr>
            </table>
            </form>
            
        </div>
    </body>
</html>
