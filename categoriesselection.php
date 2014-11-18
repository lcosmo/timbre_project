<?php
include 'utils/functions.php';
include 'utils/data.php';

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

if (isset($_POST['submit'])) {
    $_SESSION['searchaims'] = array(
        'prefered_country' => $_POST['nationality'],
        'aims' => implode(";", $_POST['aims'])
    );
        
    header("location: categoriesselection.php");
    die();
}

/*
$array = array(
    'usersessions_id' => $_SESSION['sessionid'],
    'category_id' => $i,
    'category_name' => $_POST['catname_b'.$i],
    'sequential_order' => $_POST['order_b'.$i],
    'relevance_order' => $_POST['relevance_b'.$i],
    'comments' => $_POST['fw_b'.$i]
);
$_SESSION['selectedcategories'][$array['sequential_order']] = $array;
 */ 

include 'utils/strings.php';
?>


<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>TIMBRE Project</title>
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/explore.css"> 
        <link rel="stylesheet" type="text/css" href="css/login.css">    
        <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css"> 
        
        <script src="./js/ajax.js"   type="text/javascript"></script>
        <script src="./js/jquery-1.10.2.js" type="text/javascript"></script>
        <script src="./js/jquery-migrate-1.2.1.js" type="text/javascript"></script>

        <script src="./js/jquery-ui.min.js"                     type="text/javascript"></script>
        <script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>

        <script src="js/framework_handler.js"   type="text/javascript"></script>

        <script src="js/jquery.qtip/jquery.qtip-1.0.0-rc3.js"   type="text/javascript"></script>
        <script src="js/autogrow.js"                            type="text/javascript"></script>

        <script src="./js/jquery.raty/js/jquery.raty.min.js" type="text/javascript"></script>
                
        <script type="text/javascript">
            $(document).ready(function() {
                mk_popup("c_sp","<?= $S_Desc_strategicplanning ?>");
                mk_popup("c_i","<?= $S_Desc_investigation ?>");
                mk_popup("c_ra","<?= $S_Desc_riskAssessment ?>");
                mk_popup("c_rs","<?= $S_Desc_remediationStrat ?>");
                mk_popup("c_rt","<?= $S_Desc_remediationTech ?>");
                mk_popup("c_bi","<?= $S_Desc_buildingAndInfr ?>");
                mk_popup("c_d","<?= $S_Desc_deconstruction ?>");
                mk_popup("c_w","<?= $S_Desc_wasteManagement ?>");
                mk_popup("c_req","<?= $S_Desc_requalificationPlan ?>");
                mk_popup("c_impl","<?= $S_Desc_ImplementationControlMon ?>");
                mk_popup("c_sea","<?= $S_Desc_SocioeconomicAss ?>");
                mk_popup("c_ff","<?= $S_Desc_FundingAndFinancing ?>");
                mk_popup("c_dm","<?= $S_Desc_Decisionmaking ?>");  
                
                $("#show_instruction_button").click(ShowHideInstructions);
                $("#instructions").hide();
                
                $("#categories tbody").sortable({ 
                    axis: "y", 
                    cursor: 'pointer',
                    forcePlaceholderSize: true,
                    update: updateRows
                });
                
                $("#help").click(function() {
                    $("#manual_popup").dialog({
                        resizable: false,
                        height: 220,
                        width: 420,
                        modal: true,
                        buttons: {}
                    });
                });
                
                
                //HANDLE PREV INSERTED
                <?php
                
                if(isset($_SESSION['selectedcategories']) && sizeof($_SESSION['selectedcategories'])>0)
                for($i=1; $i<=sizeof($_SESSION['selectedcategories']); $i++)
                {
                    
                    $row=$_SESSION['selectedcategories'][$i];
                    echo "$('#b".$row['category_id']."_add').click();\n";
                    echo "$('#comments_b".$row['category_id']."').val('".$row['comments']."');";
                    echo "$('#relevance_stars_b".$row['category_id']." [alt=".$row['relevance_order']."]').click();\n";
                    echo "$('#relevance_stars_b".$row['category_id']." [alt=".$row['relevance_order']."]').mouseenter();\n";
                }
                ?>       
            });
            
            var instructionVisible=false;
            function ShowHideInstructions()
            {
                if(!instructionVisible)
                {
                    $("#instructions").slideDown({
                        complete: function(){updateCategories();},
                        progress: function(){updateCategories();}
                    });
                    $("#show_instruction_button").html("Hide Instructions");
                }
                else
                {
                    $("#instructions").slideUp({
                        complete: function(){updateCategories();},
                        progress: function(){updateCategories();}
                    });
                    $("#show_instruction_button").html("Show Instructions");
                }
                
                instructionVisible=!instructionVisible;
            }
        </script>
    </head>
    <body>
        <div id="navigation_div">
            <span id="position"><a href="searchaims.php">Timbre</a> > categories selection</span>
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
        
        <div id="login_div" style="width: 800; margin-left: -400px;">
            <div id="login_title">Categories selection</div>

            <a id="show_instruction_button" href="javascript:void(0);">Show Instructions</a>
            <ul id="instructions">
                <li>Please click the green checkmark to activate the categories of information of brownfield regeneration that you need for your search (to deactivate an activated box click the red cross);</li>
                <li>The activated categories of information will be included in the table below;</li>
                <li>You can drag the table rows to adjust the sequential information order at your convenience;</li>
                <li>Please indicate the relevance of the information categories in the “Relevance score” column;</li>
                <li>If you need to add a specific comment on the selected categories of information you can use the "Comments" column of the table.</li>
                <!--<li><a href="">Information categories descriptions</a></li>-->
            </ul>

            <div id="fram_image" >
                <img id="fq" src="img/framework/framework_background.png" border="0" width="600" height="605" /><br/>
                <img id="lo" src="img/framework/framework_lang_overlay.png" border="0" style="margin-top:-605px"  width="600" height="605" /><br/>
                <img id="bu" src="img/framework/framework_buttons.png" usemap="#imap" border="0" style="margin-top:-605px"  width="600" height="605" /><br/>
            </div>
            <map id="imap" name="imap" style="display: none">
                <area shape="rect" coords="395,26,427,58" href="javascript:void(0);"  id="b1_add" class="img_add"   />
                <area shape="rect" coords="427,26,459,58" href="javascript:void(0);"  id="b1_rem" class="img_rem"   />
                <area shape="rect" coords="538,110,570,142" href="javascript:void(0);"  id="b2_add" class="img_add"   />
                <area shape="rect" coords="570,110,602,142" href="javascript:void(0);"  id="b2_rem" class="img_rem"   />
                <area shape="rect" coords="615,224,647,256" href="javascript:void(0);"  id="b3_add" class="img_add"   />
                <area shape="rect" coords="647,224,679,256" href="javascript:void(0);"  id="b3_rem" class="img_rem"   />
                <area shape="rect" coords="646,337,678,369" href="javascript:void(0);"  id="b4_add" class="img_add"   />
                <area shape="rect" coords="678,337,710,369" href="javascript:void(0);"  id="b4_rem" class="img_rem"   />
                <area shape="rect" coords="607,458,639,490" href="javascript:void(0);"  id="b5_add" class="img_add"   />
                <area shape="rect" coords="639,458,671,490" href="javascript:void(0);"  id="b5_rem" class="img_rem"   />
                <area shape="rect" coords="403,539,435,571" href="javascript:void(0);"  id="b6_add" class="img_add"   />
                <area shape="rect" coords="435,539,467,571" href="javascript:void(0);"  id="b6_rem" class="img_rem"   />
                <area shape="rect" coords="86,451,118,483" href="javascript:void(0);"  id="b7_add" class="img_add"   />
                <area shape="rect" coords="118,451,150,483" href="javascript:void(0);"  id="b7_rem" class="img_rem"   />
                <area shape="rect" coords="22,330,54,362" href="javascript:void(0);"  id="b8_add" class="img_add"   />
                <area shape="rect" coords="54,330,86,362" href="javascript:void(0);"  id="b8_rem" class="img_rem"   />
                <area shape="rect" coords="161,223,193,255" href="javascript:void(0);"  id="b9_add" class="img_add"   />
                <area shape="rect" coords="193,223,225,255" href="javascript:void(0);"  id="b9_rem" class="img_rem"   />
                <area shape="rect" coords="251,87,283,119" href="javascript:void(0);"  id="b10_add" class="img_add"   />
                <area shape="rect" coords="283,87,315,119" href="javascript:void(0);"  id="b10_rem" class="img_rem"   />
                <area shape="rect" coords="218,331,250,363" href="javascript:void(0);"  id="b11_add" class="img_add"   />
                <area shape="rect" coords="250,331,282,363" href="javascript:void(0);"  id="b11_rem" class="img_rem"   />
                <area shape="rect" coords="425,394,457,426" href="javascript:void(0);"  id="b12_add" class="img_add"   />
                <area shape="rect" coords="457,394,489,426" href="javascript:void(0);"  id="b12_rem" class="img_rem"   />
                <area shape="rect" coords="373,351,405,383" href="javascript:void(0);"  id="b13_add" class="img_add"   />
                <area shape="rect" coords="405,351,437,383" href="javascript:void(0);"  id="b13_rem" class="img_rem"   />

                <area shape="rect" coords="318,50,417,105"   id="b1"   alt="" phase="<?= $S_strategicplanning ?>"  class="quad c_sp"/>
                <area shape="rect" coords="428,132,571,218" id="b2"  alt="" phase="<?= $S_investigation ?>"  class="quad c_i"  />
                <area shape="rect" coords="523,245,687,334" id="b3"  alt="" phase="<?= $S_riskAssessment ?>"  class="quad c_ra"  />
                <area shape="rect" coords="544,355,679,448" id="b4"  alt="" phase="<?= $S_remediationStrat ?>"  class="quad c_rs"  />
                <area shape="rect" coords="478,480,628,583"  id="b5"  alt="" phase="<?= $S_remediationTech ?>"  class="quad c_rt"  />
                <area shape="rect" coords="269,569,458,660"  id="b6"  alt="" phase="<?= $S_buildingAndInfr ?>"  class="quad c_bi"  />
                <area shape="rect" coords="91,480,242,582"  id="b7"   alt="" phase="<?= $S_deconstruction ?>"   class="quad c_d" />
                <area shape="rect" coords="25,358,170,418"    id="b8"   alt="" phase="<?= $S_wasteManagement ?>"   class="quad c_w" />
                <area shape="rect" coords="26,246,176,335"  id="b9"   alt="" phase="<?= $S_requalificationPlan ?>"   class="quad c_req" />
                <area shape="rect" coords="131,114,312,210" id="b10"   alt="" phase="<?= $S_ImplementationControlMon ?>"   class="quad c_impl" />
                <area class="c_dm" coords="265,367, 291,313, 341,282, 394,292, 426,323, 446,368, 427,414, 395,441, 359,449, 325,444, 298,427, 278,403" shape="poly" id="b13"   alt="" phase="<?= $S_Decisionmaking ?>"   />
                <area  class="c_sea" coords="229,367, 483,367, 471,326, 454,294, 428,269, 396,252, 361,244, 328,248, 291,261, 261,281, 243,309, 225,344" shape="poly" id="b11"   alt="" phase="<?= $S_SocioeconomicAss ?>"   />
                <area  class="c_ff" coords="226,369, 482,368, 473,394, 462,426, 441,456, 405,478, 364,490 ,317,484, 281,464, 252,437, 233,406" shape="poly" id="b12"   alt="" phase="<?= $S_FundingAndFinancing ?>"   />
            </map>
            <form action="explore.php" method="POST">
            <div id="framework_comments" >
                <div id="categories_div" style="width:100%;">
                    <table id="categories" style="width:100%">
                        <th>&nbsp;</th>
                        <th>Sequential order</th>
                        <th>Category of information</th>
                        <th>Relevance score</th>
                        <th>Comments</th>  
                        <tbody>
                            <tr id="emptyrow" style="height:20px"><td></td><td></td><td></td><td></td><td></td></tr>
                           </tbody>
                    </table>
                    <!--<input id="update_cat" type="button" value="Update Table" onclick="checkCategories(true);" style="margin-left:650px; width:148px; background-color: #ddd;" />-->
                </div>
            </div>
                <br/><br/>
                <input type="submit" name="skip" value="Skip" style="margin:5px; " />
                <input type="submit" name="submit" value="Proceed" style="float: right; margin:5px" />
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