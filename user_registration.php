<?php
header('Content-type: text/html; charset=utf-8');
?>

<html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
<link rel="stylesheet" type="text/css" href="css/login.css">    

<script src="./js/jquery.js" type="text/javascript"></script>        

<script src="./js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
<script src="js/jquery.qtip/jquery.qtip-1.0.0-rc3.js"   type="text/javascript"></script>
        

<script type="text/javascript">
    
    function mk_popup(id,message)
    {
        $($('[name='+id+']').parent().find("label")[0]).qtip({
            content: message,
            show: 'mouseover',
            hide: 'mouseout',
            position: {
                target: 'mouse',
                adjust: { mouse: true }
                },
            style: { 
                name: 'light', // Inherit from preset style
                width: 400
            }                    
        });
    }
    
    function update_other()
    {
        $('#other_checkbox').val($('#other_text').val());
    }

    $(document).ready(function() {
        update_other();

        mk_popup("cat_1","Landowner/Problem owner, subsidiary interest group");
        mk_popup("cat_2","Immediate (< 1km), further afield");
        mk_popup("cat_3","Local authorities dealing with urban planning, environmental health, soil/groundwater protection");
        mk_popup("cat_4","Region and sub-regional authorities dealing with spatial planning and land management");
        mk_popup("cat_5","Protection agencies dealing with soil/groundwater protection, waste, environmental management, occupational health and safety, preservation order, regional development");
        mk_popup("cat_6","NGOs, grassroots movement");
        mk_popup("cat_7","Market actors re-use planners");
        mk_popup("cat_8","Local residents and business users dealing with social issues");
        mk_popup("cat_9","Companies that develop, produce and sell innovative solutions for environmental problems, innovation seekers");
        mk_popup("cat_10","Designers, environmental experts, ecologists, town planners, marketing agents");
        mk_popup("cat_11","Public, private companies");
        mk_popup("cat_12","Companies providing remediation, infrastructure, construction, landscaping, worker’s health & safety");
        mk_popup("cat_13","Companies which support risk transfer, carrier of on-going risk, carrier of residual risk");
        mk_popup("cat_14","Occupiers, residents, businesses, leisure, and casual visitors");
        mk_popup("cat_15","Press (TV and Radio), web, other");
        mk_popup("cat_16","Students, natural science researchers, social science researchers, engineering science researchers, other");
        
        

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
<div id="login_title">New user registration</div>
<!--
<form id="login_form">
<p><label>Username:</label><input class="text_input" type="text" name="username" /></p>
<p><label>Password:</label><input class="text_input" type="password" name="password" /></p>
<input id="login" type="submit" name="login" value="Login" />
<input id="cancel" type="submit" name="login" value="Cancel" />
</form>
-->

<form method="post" id="user_info_form" action="user_registration_confirmation.php">
<table  style="width: 100%">
    <tr><td style="font-weight:bold; font-size:130%;" colspan="2" >Mandatory data</td><td>
            
<tr><td width="150">Email:</td><td>
        <span><input class="" id="email" type="text" name="email" size="40em" /></span></td></tr>
<tr><td>Password:</td><td>
        <input class="" id="password" type="password" name="password" value="" size="40em"  /></td></tr>
<tr><td>Confirm Password:</td><td>
        <input class="" id="confirm_password" type="password" name="confirm_password" value="" size="40em"  /></td></tr>
<tr><td>Preferred language:</td><td><select name="nationality" class="selectinsertinput" >
                    <option></option>
                    <?php
                    $langs = explode(",", file_get_contents("utils/languages.txt"));
                    foreach ($langs as $l)
                        echo "<option>" . $l . "</option>";
                    ?>
                </select></td></tr>
<tr><td>&nbsp;</td><td></td></tr>
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
                    <td><input class="autofill" name="cat_8" value="Local community group (neighbourhood, districts)" type="checkbox"><label>Local community group (neighbour-hood, districts)</label></td>
                    <td><input class="autofill" name="cat_16" value="Scientific community and researcher" type="checkbox"><label>Scientific community and researcher</label></td>
                </tr>                
                <tr>
                    <td>&nbsp;</td>
                    <td><input class="autofill" name="cat_17" id="other_checkbox" value="Other:" type="checkbox"><label>Other:</label> <input class="autofill" name="" id="other_text" type="textbox" onkeyup="update_other()"></td>
                </tr>    
            </tbody>
        </table>
    </td>
</tr>


<tr><td style="font-weight:bold; font-size:130%;">Optional data</td></tr>
<tr><td width="150">Name:</td><td>
        <span><input class="" id="email" type="text" name="name" size="40em" /></span></td></tr>
<tr><td width="150">Surname:</td><td>
        <span><input class="" id="email" type="text" name="surname" size="40em" /></span></td></tr>
<tr><td width="150">Organisation you work for:</td><td>
        <span><input class="" id="email" type="text" name="organization" size="40em" /></span></td></tr>
<tr><td width="150">Other:</td><td>
        <span><input class="" id="email" type="text" name="otherdata" size="40em" /></span></td></tr>

<tr><td>&nbsp;</td></tr>
<tr><td><input type="button" name="cancel" id="cancel_button" value="Cancel" /></td><td>
<input id="Ok" type="submit" name="confirm" value="Confirm" style="float:right;" />
</td></tr>
</table>
</form>
</div>


</body>
</html>