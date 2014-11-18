<?php
include 'utils/functions.php';
include 'utils/data.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-type: text/html; charset=utf-8');

session_init();

if (!isset($_SESSION['userid'])) {
    header("location: index.php");
    return;
};
$mysqli = new mysqli($host, $db_name, $pass, $db_host);
$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$username = $row['email'];


$query = "SELECT * FROM rel_users_stkhcategories s WHERE s.users_id=" . $_SESSION['userid'];
$result = $mysqli->query($query);
if (!$result->fetch_array())
    header("location: user_complete_profile.php");

//try to retrieve last session search settings {
$query = sprintf("select *  from usersessions where users_id=%s order by id desc LIMIT 1", $_SESSION['userid']);
$result = $mysqli->query($query);
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $_SESSION['searchaims'] = array();
    $_SESSION['searchaims']['prefered_country'] = $row['prefered_country'];
    $_SESSION['searchaims']['aims'] = $row['aims'];

    $query = sprintf("select *  from searchpreferences where usersessions_id=%d", $row['id']);
    $result = $mysqli->query($query);
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        //usersessions_id Ascending	category_id	category_name	sequential_order	relevance_order	comments
        $_SESSION['selectedcategories'][$row['sequential_order']]['category_id'] = $row['category_id'];
        $_SESSION['selectedcategories'][$row['sequential_order']]['category_name'] = $row['category_name'];
        $_SESSION['selectedcategories'][$row['sequential_order']]['relevance_order'] = $row['relevance_order'];
        $_SESSION['selectedcategories'][$row['sequential_order']]['comments'] = $row['comments'];
    }
}


$prefered_country = "";
$aims = "";
if (isset($_SESSION['searchaims'])) {
    $prefered_country = $_SESSION['searchaims']['prefered_country'];
    $aims = $_SESSION['searchaims']['aims'];
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>TIMBRE Project</title>
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/explore.css"> 
        <link rel="stylesheet" type="text/css" href="css/login.css">    

        <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css"> 
        <link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css"> 

        <script src="./js/ajax.js" type="text/javascript"></script>
        <script src="./js/jquery.js" type="text/javascript"></script>

        <script src="./js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
        <script src="./js/jquery.multiselect.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $(document).ready(function() {
                    $("label").each(
                            function(i, e)
                            {
                                var p = $(this).parent();
                                $(p.find("input")[0]).attr("value", ($(this).html()));
                                $(p.find("input")[0]).attr("name", "aims[]");
                                //alert($(e).parent().val());
                            }
                    );
                        
                    <?php
                    foreach(explode(';',$aims) as $aim)
                    {
                        if(strpos($aim,'OTHER:')===0)
                        {
                            $val = substr($aim, 6);
                            echo "$('#other_checkbox').attr('checked','true');\n$('#other_text').val('$val');\n";
                        }
                        else
                            echo "var a = $('[value=\"$aim\"]');\n a.attr('checked','true');\n";
                    }
                    ?> 
                });
                $("#help").click(function() {
                    //alert("yep");
                    $("#manual_popup").dialog({
                        resizable: false,
                        height: 250,
                        width: 420,
                        modal: true,
                        buttons: {}
                    });
                });
                

            });
        </script>

        <style>
            .sa tr td
            {
                padding-bottom: 5px;
            }

            label{float:left;width: 90%;}
            input[type="checkbox"]{float:left;width:15px}

            input[type="text"]{
                width: 90%;
            }

        </style>
    </head>
    <body>
        <div id="navigation_div">
            <span id="position"><a href="searchaims.php">Timbre</a> > search aim </span>
            <span id="profile"><a href="user_info.php"><?php echo $username ?></a> - <a href="logout.php">Logout</a>&nbsp;</span>
        </div>
        <div id="header">
            <img id="logo" src="img/logo2.png" />

            <img id="header_bkg" src="img/bkg_header.png" />

            <span id="timbrelink">the timbre project: <a target="_blank" href="http://www.timbre-project.eu/">http://www.timbre-project.eu/</a></span>
            <span id="helplink"><a target="_blank" href="javascript:void()" id='help'>Help</a></span>

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


        <div id="login_div" style="width: 780px; margin-left: -390px; padding-bottom: 30px;">
            <div id="login_title">Which is your search goal?</div>        
            <form style="padding: 10px 10px 0px 10px;" method="post" action="categoriesselection.php">
                <p class="question"><strong>Looking for information* on:</strong><br/><br/>
                    <!--<textarea name="searchaim" cols="70" rows="3" style="margin-bottom: 10px;"><?= $aims ?></textarea>-->
                <table class='sa' width="100%" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>BFs with the highest redevelopment potential</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Risk assessment</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>BFs regeneration barriers</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Characterization of soil and groundwater</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>National strategies and guidelines for BF regeneration</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Remediation</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Stakeholders analysis</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Reuse of building rubble</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Social benefits and impacts of BFs</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Waste management and control</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Economic benefits of BFs</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Monitoring</label></td>
                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Rural redevelopment of BFs</label></td>

                            <td><input class="autofill" name="" value="" type="checkbox"><label>Funds for BF regeneration</label></td>

                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Ecological issues related to BF management (e.g., presence of biotope, type of biotope, value of biotope, protected species, red-list species)</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>BF database (lists of BFs with related data, e.g. localization, area, typology, former/historical utilization, contamination, limitations in use, etc.)</label></td>

                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Residential redevelopment of BFs</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Best practices and successful case studies of BF regeneration</label></td>

                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>(Land regeneration) Sustainable BF regeneration</label></td>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>The adoption of Public Private Partnership strategies in BF management</label></td>

                        </tr>  
                        <tr>
                            <td><input class="autofill" name="" value="" type="checkbox"><label>Planning (land use information, regional/urban land use plans)</label></td>
                            <td><input class="autofill" name="aims[]" id="other_checkbox" value="" type="checkbox"><label>Other:</label> <input class="autofill" name="" id="other_text" type="text" onkeyup="$('#other_checkbox').val('OTHER:' + $(this).val());"></td>
                        </tr>
                        <tr><td colspan="2">
                                <br>
                                *i.e.: regulations/technical manuals/tools/case studies.                        
                            </td></tr>

                    </tbody>
                </table>


                </p>
<!--                <img src="img/fake_categories.png" width="580px"/>   -->
                <p class="question"><strong>In which country (if any) are you going to apply the needed information?</strong><br/>
                    <select name="nationality" class="selectinsertinput" >
                        <option></option>
                        <?php
                        $langs = explode(",", file_get_contents("utils/countries.txt"));
                        foreach ($langs as $l)
                            echo "<option " . ($l == $prefered_country ? "selected" : "") . " >$l</option>";
                        ?>
                    </select>
                </p>

                <input type="submit" name="skip" value="Skip" style="float: left;" />
                <input type="submit" name="submit" value="Proceed" style="float: right;" />
                <p>
                <p>
            </form>
        </div>
        
        <div id="manual_popup" title="User's Manual" style="display:none">
            <p><a href="manual/Timbre__IS_Manual_EN.pdf" target="_blank">English User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_DE.pdf" target="_blank">German User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_CZ.pdf" target="_blank">Czech User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_POL.pdf" target="_blank">Polish User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_RO.pdf" target="_blank">Romanian User's Manual</a></p>
        </div>
    </body>
</html>