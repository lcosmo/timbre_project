
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

    header("Location: explore.php");
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
        <script src="js/jquery.qtip/jquery.qtip-1.0.0-rc3.js"   type="text/javascript"></script>

        <script type="text/javascript">

<? include "js/explore.js" ?>
            $(document).ready(function() {

                rememberno = false;

                $(window).resize(resizeMap);
                resizeMap();

                getExploreItems();

                $(".typ").click(function() {
                    current_typ = $(this).text();
                    current_phase = current_phase == "Category" ? "<?= $phases[0] ?>" : current_phase;
                    update_selectors();
                });

                $("#framework_link").click(function() {
                    //current_phase="Category";
                    //current_typ="Typology";
                    //show_insert(false);
                    //setSearchMode(false);

                    document.location.href = 'searchaims.php';
                    //update_selectors();
                });

                $("area").attr("href", "javascript:void(0);");
                $("area").click(function() {
                    current_phase = $(this).attr("phase");
                    current_typ = current_typ == "Typology" ? "Regulation" : current_typ;
                    update_selectors();
                });

                $("#phase_select, #insert_phase_select").change(function() {
                    //if($(this).val()=='Category') return;
                    current_phase = $(this).val();
                    update_selectors();
                });

                $("#typ_select, #insert_typ_select").change(function() {
                    //if($(this).val()=='Typology') return;
                    current_typ = $(this).val();
                    update_selectors();
                });

                $("#country_filter").change(function() {
                    //if($(this).val()=='Typology') return;

                    update_selectors();
                });

                update_selectors();

                $.validator.addMethod("notEqual", function(value, element, param) {
                    return this.optional(element) || value !== param;
                }, "Please choose a value!");

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
                                                update_selectors(true);
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
                            getSearchedItems($("#query").val());
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

                initsideMenu();
                blinkIn();

            });

            function superuserpopup()
            {
                $("#superuser_popup").dialog({
                    resizable: false,
                    height: 300,
                    width: 420,
                    modal: true,
                    buttons: {
                        "Cancel": function() {
                            $(this).dialog("close");
                        },
                        "Submit Request": function() {
                            document.location.href = "user_info.php";
                        }
                    }
                });

//            if(c)
//                document.location.href = "searchaims.php?new=true";
            }


            function initsideMenu()
            {
                $(".categorynotselected").click(function()
                {
                    var phase = $(this).attr("phase");
                    var phid = $(this).attr("phid");
                    if (rememberno)
                    {
                        $.get('utils/update_categories.php?cat=' + phase + "&catid=" + phid, function(data)
                        {
                            $("#side_menu_list").html(data);
                            initsideMenu();
                        });
                        return;
                    }

                    $("#newsearch_popup").dialog({
                        resizable: false,
                        height: 220,
                        width: 420,
                        modal: true,
                        buttons: {
                            "Yes": function() {
                                document.location.href = "searchaims.php?new=true";
                            },
                            "No": function() {
                                $.get('utils/update_categories.php?cat=' + phase + "&catid=" + phid, function(data)
                                {
                                    $("#side_menu_list").html(data);
                                    initsideMenu();
                                });
                                $(this).dialog("close");
                            },
                            "No, do not ask again": function() {
                                rememberno = true;
                                $(this).dialog("close");
                                $.get('utils/update_categories.php?cat=' + phase + "&catid=" + phid, function(data)
                                {
                                    $("#side_menu_list").html(data);
                                    initsideMenu();
                                });
                            }
                        }
                    });
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

                $(".phase").click(function() {
                    current_phase = $(this).text();
                    current_typ = current_typ == "Typology" ? "Regulation" : current_typ;
                    update_selectors();
                });
                
                mk_popup("sust_filter","Sustainable brownfield regeneration and remediation");
                $('#sust_filter').click(function()
                {
                    var active = $(this).attr('active')=='true';
                    $(this).attr('active',!active);
                    
                    if(!active)
                        $(this).find('img').attr('src','img/suston.png');
                    else
                        $(this).find('img').attr('src','img/sustoff.png');
                    
                    getSearchedItems($("#query").val());
                });
            }
        </script>
    </head>
    <body>
        <div id="navigation_div">
            <span id="position"><a href="explore.php">Timbre</a> > explore</span>
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
                <input align="right" type="button" value="Links Control Panel" id="show_search_div" onClick="document.location = 'control_panel.php'" />
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

        <div id="insert_div">
<? include 'insert_form.php' ?>
        </div>
        <script>$('#insert_div').hide();</script>

        <div id="explore_div">
            <div id="side_menu">
                <span id="framework_link"><a class="selected"><span>Start new search</span></a></span>
                <ul id="side_menu_list">
                    <li class="search_all" style=""><a class='phase' href="javascript:void(0);"><span>All</span></a></li>
<?
$i = 0;
for ($i = 0; $i < sizeof($phases); $i++) {
    $p = $phases[$ord_phases[$i]];
    $phid = $ord_phases[$i] + 1;
    if ($sel_phases[$ord_phases[$i]] == 0)
        echo "<li><a class='phase' href=\"javascript:void(0);\"><span class='categorynotselected' phase='$p' phid='$phid'>" . $p . "</span></a></li>";
    else
        echo "<li><a class='phase' href=\"javascript:void(0);\"><span class='prefered' phase='$p' phid='$phid'>" . $p . "</span></a></li>";
}
?>
                </ul>
            </div>
            <div id="tab_menu">
                <div id="tabs">
                    <ul>
                        <li><a class='typ search_all' href="javascript:void(0);" style=""><span class="button">All</span></a></li>
                        <li><a class='typ selected' href="javascript:void(0);"><span class="button">Regulation</span></a></li>
                        <li><a class='typ' href="javascript:void(0);"><span class="button">Technical manuals</span></a></li>
                        <li><a class='typ' href="javascript:void(0);"><span class="button">Tools</span></a></li>
                        <li><a class='typ' href="javascript:void(0);"><span class="button">Case studies</span></a></li>
                    </ul>

                    <span id="countryselect">    
                        <span id="sust_filter" active='false'><img src='img/sustoff.png' width='25px'></img></span>
                        <span>
                            <select name="country_filter" multiple="multiple"  id="country_filter">
                            <?
                            foreach ($countriesofreference as $p)
                                echo "<option id='cof_opt_$p'>$p</option>";
                            ?>
                            </select>
                        </span>
                        <span class="label"> Country of reference:</span>
                        
                    </span>
                    <span id="insertbutton">     

                        <span id="search_results_block" style="display:none">
                            <span id="foundcount"></span>
                        </span>
                    </span>
                </div>

                <div id="explore_content">
                </div>
            </div>
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

        <div id="rating_container" class="popup" style="display:none" >
            <!--        <div id="rating_container">-->
            <div class="moreinfo_top_bar" ><a href="javascript:void(0)"><img src="img/close.png" onClick="$('#rating_container').fadeOut();" alt="X" /></a></div>
            <div id="rating_div">

            </div>
        </div>

        <div id="manual_popup" title="User's Manual" style="display:none">
            <p><a href="manual/Timbre__IS_Manual_EN.pdf" target="_blank">English User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_DE.pdf" target="_blank">German User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_CZ.pdf" target="_blank">Czech User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_POL.pdf" target="_blank">Polish User's Manual</a></p>
            <p><a href="manual/Timbre__IS_Manual_RO.pdf" target="_blank">Romanian User's Manual</a></p>
        </div>
        <div id="newsearch_popup" title="Start new search?" style="display:none">
            <p>Do you want to change your search aims?</p>
        </div>
        <div id="superuser_popup" title="Expert User credentials required" style="display:none">
            <p>You are not allowed to modify/delete the web link.<br>
                If you need to modify/delete the web link, first you must be authorised by the IS administrator and get specific credentials.<br>
                If you want these credentials please submit a request!</p>
        </div>
        <div id="debug_w" style="display:none; position:absolute; top:10px; left:10px; width: 90%; height: 90%;">...</div>
    </body>
</html>