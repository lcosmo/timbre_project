
<?php
include 'utils/functions.php';
include 'utils/data.php';

header('Content-type: text/html; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

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

$query = "SELECT DISTINCT country FROM project order by country";
$result = $mysqli->query($query);
$i = 0;
while ($row = $result->fetch_array(MYSQLI_ASSOC))
    $countriesofreference[$i++] = $row['country'];


//CATEGORY PREFERENCE
//Insert Search Session
if (isset($_POST['submit']) || isset($_POST['skip'])) {
    $array = array(
        'users_id' => $_SESSION['userid'],
    );
    insert($mysqli, 'usersessions', array_keys($array), array_values($array));

    $_SESSION['sessionid'] = $mysqli->insert_id;
    $_SESSION['searchaims']['id'] = $_SESSION['sessionid'];
    //Insert aims
    if (isset($_SESSION['searchaims']))
        insertOrUpdate($mysqli, 'usersessions', array_keys($_SESSION['searchaims']), array_values($_SESSION['searchaims']), array(0, 1));

    //Insert categories
    //var_dump($_POST);
    $_SESSION['selectedcategories'] = array();
    for ($i = 0; $i <= 13; $i++) {
        if (isset($_POST['catname_b' . $i])) {
            $array = array(
                'usersessions_id' => $_SESSION['sessionid'],
                'category_id' => $i,
                'category_name' => $_POST['catname_b' . $i],
                'sequential_order' => $_POST['order_b' . $i],
                'relevance_order' => $_POST['relevance_b' . $i],
                'comments' => $_POST['fw_b' . $i]
            );
            $_SESSION['selectedcategories'][$array['sequential_order']] = $array;
            insert($mysqli, 'searchpreferences', array_keys($array), array_values($array));
        }
    }

    header("Location: control_panel.php");
    die();
}
//END

if (!isset($_SESSION['sessionid'])) {
    header("location: searchaims.php");
    die();
}

$query = "SELECT category_id FROM searchpreferences s WHERE s.usersessions_id = " . $_SESSION['sessionid'] . " order by sequential_order";
$result = $mysqli->query($query);

$sel_phases = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$ord_phases = array();
$i = 0;
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

    $ord_phases[sizeof($ord_phases)] = $row['category_id'] - 1;
    $sel_phases[$row['category_id'] - 1] = 1;
}
for ($i = 0; $i < 13; $i++) {
    if ($sel_phases[$i] == 0)
        $ord_phases[sizeof($ord_phases)] = $i;
}


if (isset($_POST['insertitem'])) {
    $phase = $mysqli->real_escape_string($_POST['insert_phase_select']);
    $typology = $mysqli->real_escape_string($_POST['insert_typ_select']);
    $subcategory = $mysqli->real_escape_string($_POST['subcategory']);
    $country = $mysqli->real_escape_string($_POST['country']);
    $influence = $mysqli->real_escape_string($_POST['influence']);
    //$avinen = $mysqli->real_escape_string($_POST['avineng'])=="Yes";
    $title = $mysqli->real_escape_string($_POST['title']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $lang_ori = $mysqli->real_escape_string($_POST['language']);
    $link_ori = $mysqli->real_escape_string($_POST['link_ori']);
    $link_eng = $mysqli->real_escape_string($_POST['link_eng']);
    $link_other = $mysqli->real_escape_string($_POST['link_other']);

    $more_info = "";
    if (isset($_POST['moreinfo']) && $_POST['moreinfo'] == 'yes')
        $more_info = serialize($_POST);

    insert($mysqli, "project", array('user', 'phase', 'typology', 'subcategory', 'country', 'influence', 'title', 'description', 'lang_ori', 'link_ori', 'link_eng', 'link_other', 'moreinfo', 'date'), array($_SESSION['userid'], $phase, $typology, $subcategory, $country, $influence, $title, $description, $lang_ori, $link_ori, $link_eng, $link_other, $more_info, date("Y/m/d H:i:s")));
}
?>


<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>TIMBRE Project</title>
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/explore.css"> 

        <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css"> 
        <link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css"> 


        <script src="./js/ajax.js" type="text/javascript"></script>
        <script src="./js/jquery.js" type="text/javascript"></script>

        <script src="./js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
        <script src="./js/jquery.multiselect.js" type="text/javascript"></script>

        <script src="./js/jquery.raty/js/jquery.raty.min.js" type="text/javascript"></script>


        <script type="text/javascript">

<? include "js/explore.js" ?>

            $(document).ready(function() {

                current_phase = "All";
                current_typ = "All";

                rememberno = false;
                getUserItems('',<?= $_SESSION['userid'] ?>);

                //update_selectors(); 

                $.validator.addMethod("notEqual", function(value, element, param) {
                    return this.optional(element) || value !== param;
                }, "Please choose a value!");

                $("#help").click(function() {
                    $("#manual_popup").dialog({
                        resizable: false,
                        height: 220,
                        width: 420,
                        modal: true,
                        buttons: {}
                    });
                });

                $("#insertform").validate(
                        {
                            rules:
                                    {
                                        title: "required",
                                        link: "required",
                                        insert_phase_select: {
                                            notEqual: "Category"
                                        },
                                        insert_typ_select: {
                                            notEqual: "Typology"
                                        }
                                    },
                            submitHandler: function(form) {
                                if (confirm("Insert new item?"))
                                {
                                    $.post("modify.php?insertitem=true&", $('#insertform').serialize(),
                                            function(data) {
                                                $("#debug_w").html("data");
                                                show_insert();
                                                //update_selectors(true);
                                            });
                                }
                            },
                            invalidHandler: function(form)
                            {
                                alert("There are one or more invalid fields!");
                            }
                        });


                $("#searchform").submit(
                        function() {
//            alert("form submitted");
//            setSearchMode(true);
                            getUserItems($("#query").val(),<?= $_SESSION['userid'] ?>);
                            return false;
                        });

                $('[type=button], [type=submit]').hover(function() {
                    $(this).css('cursor', 'pointer');
                }, function() {
                    $(this).css('cursor', 'auto');
                });

                $('#country_filter').multiselect(
                        {
                            noneSelectedText: "All",
                            selectedList: 1,
                            uncheckAllText: "Clear selection"
                        });

//            initsideMenu();
//            blinkIn();

            });



        </script>
    </head>
    <body>
        <div id="navigation_div">
            <span id="position"><a href="control_panel.php">Timbre</a> > Control Panel</span>
            <span id="profile"><a href="user_info.php"><?php echo $username ?></a> - <a href="logout.php">Logout</a>&nbsp;</span>
        </div>
        <div id="header">
            <img id="logo" src="img/logo2.png" />

            <img id="header_bkg" src="img/bkg_header.png" />

            <div class="search_block">
                <span id="searchspan" class="search_span">
                    <span>
                        <form id="searchform" method="POST" action="explore.php" >
                            <input type="text" id="query" class="green" name ="query" value="search into the database"/>
                            <input align="right" id="submit_query" type="button" value="Search" onClick="$('#searchform').submit()"/>
                        </form>
                    </span>
                </span>
            </div>



            <span id="helplink"><a target="_blank" href="javascript:void()" id='help'>Help</a></span>

            <span  id="insert_block">
                <input align="right" type="button" value="Back to Explore" id="show_search_div" onClick="document.location = 'explore.php'" />
                <input align="right" type="button" value="Insert new item" id="show_search_div" onClick="show_insert(true);" />
            </span>

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

        <!--
        <div id="searchbar">
            <span id="searchspan">
                <form id="searchform" method="POST" action="explore.php" >
                    Search: <input type="text" id="query" class="green" name ="query" />
                </form>
            </span>
            <span id="tabselector">
                Select one table: 
                <select class="green"  id="phase_select">
<?
foreach ($phases as $p)
    echo "<option>" . $p . "</option>";
?>
                </select>
                <select class="green"  id="typ_select">
                    <option>Typology</option>
                    <option>Regulation</option>
                    <option>Technical manuals</option>
                    <option>Tools</option>
                    <option>Case studies</option>
                </select>

                <input align="right" type="button" value="Insert new item" id="show_search_div" onClick="show_insert(true);" />
            </span>
        </div>
        -->
        <div id="insert_div">
<? include 'insert_form.php' ?>
        </div>
        <script>$('#insert_div').hide();</script>


        <div id="tab_menu">
            <div id="tabs">
                <h2 style="color: rgb(110,70,67); padding-left: 5px;" > User Links Control Panel </h2>
            </div>
            <div id="explore_content" style="left:10px; width:100%; background-color: red;" ></div>
        </div>



        <div id="footer">
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
<!--        <script>getExploreItems()</script>-->


        <div id="moreinfo_container" style="display:none">
            <div class="moreinfo_top_bar"><a href="javascript:void(0)"><img src="img/close.png" onClick="$('#moreinfo_container').fadeOut();" alt="X" /></a></div>
            <div id="moreinfo_div"></div>
        </div>

        <div id="manual_popup" title="User's Manual" style="display:none">
            <p><a href="manual/Timbre__IS_Manual_EN.pdf" target="_blank">English User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_DE.pdf" target="_blank">German User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_RO.pdf" target="_blank">Romanian User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_CZ.pdf" target="_blank">Czech User's Manual</a></p>
        </div>     
    </body>
</html>