<?php
    include 'utils/functions.php';
    include 'utils/data.php';

    header('Content-type: text/html; charset=utf-8');
   
    session_init();
    $_SESSION['nextpage'] = 'user_info.php';
    
    if (!isset($_SESSION['userid'])) {
        header("location: index.php");
        return;
    };
    unset($_SESSION['nextpage']);
    
    $mysqli = new mysqli($host, $db_name, $pass, $db_host);
    $query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $username = $row['email'];        
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
            $('#dropdown').click(showLoginMenu);      
            $(document).click(hideLoginMenu);
                $.validator.addMethod("valueNotEquals", function(value, element, arg){
                    return arg != value;
                }, "Value must not equal arg.");

                $.validator.addMethod("checkMail", function(value, element, arg){
                    return arg != value;
                }, "Value must not equal arg.");

                $.validator.setDefaults({
                    onkeyup: false
                })
                
                $("#change_password").click(function()
                {
                    $(".nepassword").show();
                });

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
            <span id="position"><a href="searchaims.php">Timbre</a> > User Profile </span>
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
            <div id="login_title">User Profile</div>      
            
            <form method="post" id="user_info_form" action="profile_changed.php">
            <table>
            <tr><td width="150">Email:</td><td>
            
            <span><input class="" id="email" type="text" name="email" size="40em" value='<?= $row['email'] ?>' readonly /></span></td></tr>
            <tr><td>Password:</td><td><button id="change_password">Change Password</button></td></tr>
            <tr class="nepassword" style="display:none"><td>Current Password:</td><td>
            <input class="" id="prevpassword" type="password" name="prevpassword" value="" size="40em"  /></td></tr>
            <tr class="nepassword" style="display:none"><td>New Password:</td><td>
            <input class="" id="password" type="password" name="password" value="" size="40em"  /></td></tr>
            <tr class="nepassword" style="display:none"><td>Confirm New Password:</td><td>
            <input class="" id="confirm_password" type="password" name="confirm_password" value="" size="40em"  /></td></tr>
            
            <tr><td>Preferred language:</td><td><select name="nationality" class="selectinsertinput" >
                                <option></option>
                                <?php
                                $langs = explode(",", file_get_contents("utils/languages.txt"));
                                foreach ($langs as $l)
                                    if(strtolower($row['language'])==strtolower($l))
                                        echo "<option selected>" . $l . "</option>";
                                    else                                        
                                        echo "<option>" . $l . "</option>";
                                ?>
                            </select></td></tr>
            <tr><td>&nbsp;</td><td></td></tr>
           
            <tr><td colspan="2" style="font-size: 70%">
                    <p>
                        <strong>Would you like to become Expert-User?</strong><br>
                        If yes, please click <a href="superuser_request.php">here</a>.
                    </p>
                    <p>
                        Expert User definition:<br>
                        Expert Users will have system rights to modify and delete not updated information and web links. Users who wish to modify old or inappropriate information can ask to become Expert Users by providing information about their competences in the field and explaining why they require to become Expert-User. The administrator of the system will evaluate the requests.
                    </p>
                    
                </td></tr>
            <tr><td>&nbsp;</td><td>
            <input id="Ok" type="submit" name="change_user_info" id="login" value="Save" style="float:right;" />
            <a href="explore.php"  style="float:right; margin-right: 10px;" >Cancel</a>

            </td></tr>
            </table>
            </form>
            
        </div>
    </body>
</html>
