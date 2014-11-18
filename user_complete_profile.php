<?php
include 'utils/functions.php';
include 'utils/data.php';

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

        
session_init();

if (!isset($_SESSION['userid'])) {
    header("location: index.php");
    return;
};
$mysqli = new mysqli($host, $db_name, $pass, $db_host);


$query = "SELECT * FROM user where id=".$_SESSION['userid'];
$result = $mysqli->query($query);
$row = $result->fetch_assoc();

$name = $row['name'];
$surname = $row['surname'];
$organization = $row['organization'];
$otherdata = $row['otherdata'];

$error = -1;
//var_dump($_POST);
$category_inserted=true; 
if(isset($_POST['confirm']))
{
    $activationkey = md5(uniqid(rand(), true));
    $array = array(
        'id' => $_SESSION['userid'],
        'name' => $_POST['name'],
        'surname' => $_POST['surname'],
        'organization' => $_POST['organization'],
        'otherdata' => $_POST['otherdata']
    );

    $updated = insertOrUpdate($mysqli, 'user', array_keys($array), array_values($array), array(1,2,3,4));
    
    $category_inserted=false;
//            var_dump($mysqli->error);
            if($updated)
            {                              
                for($i=1; $i<=17; $i++)
                {
                    if(! isset($_POST['cat_'.$i])) continue;
                    if($i==17)
                        $_POST['cat_'.$i]=$_POST['other_text'];
                        
                    $array = array(
                    'name' =>  $_POST['cat_'.$i],
                    'predefined' => 0
                    );
                    $category_inserted=true;
                    insert($mysqli, 'stkhcategories', array_keys($array), array_values($array));

                    query($mysqli,
                    sprintf("INSERT INTO rel_users_stkhcategories(users_id, stkhcategories_id) 
                            SELECT ".$_SESSION['userid'].", s.id
                            FROM stkhcategories s
                            WHERE s.name = '%s'", $_POST['cat_'.$i]
                    ));
                }
            }
            if ($category_inserted) {
                header("location: searchaims.php");
                return;
            };
            
        }       
        

?>

<html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
<link rel="stylesheet" type="text/css" href="css/login.css">    

<script src="./js/jquery.js" type="text/javascript"></script>        
<script src="./js/jquery-validationEngine/jquery.validationEngine.js" type="text/javascript"></script>   
<script src="./js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>

<script type="text/javascript">
    function update_other()
    {
        $('#other_checkbox').val($('#other_text').val());
    }

    $(document).ready(function() {
        update_other();

        $("#cancel_button").click(function()
        {
            document.location.href = "index.php"; 
        });

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
                            password: {
                                    required: true,
                                    minlength: 6
                            },
                            confirm_password: {
                                    required: true,
                                    minlength: 6,
                                    equalTo: "#password"
                            },
                            email: {
                                    required: true,
                                    email: true,
                                    remote:{  
                                        url: "utils/validatorAJAX.php",  
                                        type: "get"  
                                    }                                  
                            },
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

.valid
{
    color: green;
}

label[class='error']
{
    font-size: 9pt;
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
<div id="login_title">Complete Your Profile</div>
<!--
<form id="login_form">
<p><label>Username:</label><input class="text_input" type="text" name="username" /></p>
<p><label>Password:</label><input class="text_input" type="password" name="password" /></p>
<input id="login" type="submit" name="login" value="Login" />
<input id="cancel" type="submit" name="login" value="Cancel" />
</form>
-->

<form method="post" id="user_info_form" action="user_complete_profile.php">
<table  style="width: 100%">
    <tr><td style="font-weight:bold; font-size:130%;" colspan="2" >Mandatory data</td><td>
 <?php
 if (!$category_inserted)
      echo "<tr><td colspan='2'><p class='error'>You must select at least one stakeholder category.</td></tr></p>" 
 ?>
            
            
<tr><td colspan="2">Please, indicate in which stakeholder category/categories you are included (more than one answer is possible):</td></tr>
<tr>
    <td colspan="2">
        <table width="100%" style="width: 100%;">
            <tbody>
                <tr>
                    <td><input class="autofill" name="cat_1" value="Site owner" type="checkbox"><label>Site owner</label></td>
                    <td><input class="autofill" name="cat_9" value="Technology provider" type="checkbox"><label>Technology provider</label></td>
                </tr>  
                <tr>
                    <td><input class="autofill" name="cat_2" value="Site neighbour" type="checkbox"><label>Site neighbour</label></td>
                    <td><input class="autofill" name="cat_10" value="Consultant" type="checkbox"><label>Consultant</label></td>
                </tr>
                <tr>
                    <td><input class="autofill" name="cat_3" value="Local authorities (town or city)" type="checkbox"><label>Local authorities (town or city)</label></td>
                    <td><input class="autofill" name="cat_11" value="Financier" type="checkbox"><label>Financier</label></td>
                </tr>                
                <tr>
                    <td><input class="autofill" name="cat_4" value="Regional and sub-regional government" type="checkbox"><label>Regional and sub-regional government</label></td>
                    <td><input class="autofill" name="cat_12" value="Contractor" type="checkbox"><label>Contractor</label></td>
                </tr>                
                <tr>
                    <td><input class="autofill" name="cat_5" value="Regional and national regulator" type="checkbox"><label>Regional and national regulator</label></td>
                    <td><input class="autofill" name="cat_13" value="Insurer" type="checkbox"><label>Insurer</label></td>
                </tr>                
                <tr>
                    <td><input class="autofill" name="cat_6" value="Public interest group" type="checkbox"><label>Public interest group</label></td>                   
                    <td><input class="autofill" name="cat_14" value="End-user" type="checkbox"><label>End-user</label></td>
                </tr>                
                <tr>
                    <td><input class="autofill" name="cat_7" value="Developer/investor" type="checkbox"><label>Developer/investor</label></td>
                    <td><input class="autofill" name="cat_15" value="Media" type="checkbox"><label>Media</label></td>
                </tr>                
                <tr>
                    <td><input class="autofill" name="cat_8" value="Local community group (neighbour-hood, districts)" type="checkbox"><label>Local community group (neighbour-hood, districts)</label></td>
                    <td><input class="autofill" name="cat_16" value="Scientific community and researcher" type="checkbox"><label>Scientific community and researcher</label></td>
                </tr>                
                <tr>
                    <td>&nbsp;</td>
                    <td><input class="autofill" name="cat_17" id="other_checkbox" value="Other:" type="checkbox"><label>Other:</label> <input class="autofill" name="other_text" id="other_text" type="textbox" onkeyup="update_other()"></td>
                </tr>    
            </tbody>
        </table>
    </td>
</tr>


<tr><td style="font-weight:bold; font-size:130%;">Optional data</td></tr>
<tr><td width="150">Name:</td><td>
        <span><input class="" id="email" type="text" name="name" size="40em" value="<?=$name?>" /></span></td></tr>
<tr><td width="150">Surname:</td><td>
        <span><input class="" id="email" type="text" name="surname" size="40em" value="<?=$surname?>" /></span></td></tr>
<tr><td width="150">Organisation you work for:</td><td>
        <span><input class="" id="email" type="text" name="organization" size="40em" value="<?=$organization?>" /></span></td></tr>
<tr><td width="150">Other:</td><td>
        <span><input class="" id="email" type="text" name="otherdata" size="40em" value="<?=$otherdata?>" /></span></td></tr>

<tr><td>&nbsp;</td></tr>
<tr><td><input type="button" name="cancel" id="cancel_button" value="Cancel" /></td><td>
<input id="Ok" type="submit" name="confirm" value="Confirm" style="float:right;" />
</td></tr>
</table>
</form>
</div>


</body>
</html>