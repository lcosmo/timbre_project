<?php 	include 'utils/functions.php';
	include 'utils/data.php';
	session_init();
	if(!isset($_SESSION['userid'])) { header("location: index.php"); return; };
	$mysqli = new mysqli($host, $db_name, $pass, $db_host);
	$query = sprintf("select *  from user where id=%s", $_SESSION['userid']);
	$result = $mysqli->query($query);	
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$username = $row['username'];

?>

<html>
<head>

<title>TIMBRE Project</title>
<link rel="stylesheet" type="text/css" href="css/header.css">
<link rel="stylesheet" type="text/css" href="css/explore.css">    
<script src="./js/ajax.js" type="text/javascript"></script>
<script src="./js/jquery.js" type="text/javascript"></script>
<script src="./js/jquery-validation/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">

current_phase="All";
current_typ = "All";

function update_selectors()
{
	//sidebar and tabs
	$(".phase").removeClass("selected");
	$(".phase:contains('"+current_phase+"')").addClass("selected");
	$(".typ").removeClass("selected");
	$(".typ:contains('"+current_typ+"')").addClass("selected");
	
	//search_select
	$("#phase_select").val(current_phase).attr("selected", "selected");
	$("#typ_select").val(current_typ).attr("selected", "selected");

	getSearchedItems($("#query").val());
}


   $(document).ready(function() {
   
    $(".phase").click(function () {
	current_phase = $(this).text();	
	update_selectors();
	});
	
    $(".typ").click(function () {
	current_typ = $(this).text();	
	update_selectors();
	});
	
    $("#phase_select").change(function(){
	if($(this).val()=='Phase') return;
	current_phase = $(this).val();
	update_selectors(); 
    });
    
    $("#typ_select").change(function(){
	if($(this).val()=='Typology') return;
	current_typ = $(this).val();
	update_selectors();
    });
    
    update_selectors(); 


    $.validator.addMethod("notEqual", function(value, element, param) {        
	return this.optional(element) || value !== param;
	}, "Please choose a value!");
	    
    
    $("#searchform").validate({ submitHandler: function(){getSearchedItems($("#query").val());} });
    
});
</script>
</head>
<body>
<!--HEAD-->
<div id="navigation_div">
<span id="position"><a href="searchaims.php">Timbre</a> > <a href="explore.php">explore</a> > search </span>
<span id="profile"><a href="user_info.php"><?php echo $username ?></a> - <a href="logout.php">Logout</a>&nbsp;</span>
</div>
<div id="header">
<img src="img/logo.jpg" />
<div id="h1">Search into database</div>
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

<div id="searchbar">
<span id="searchspan">
<form id="searchform" >
Search: <input type="text" id="query" name ="query" id="query" value="<?php echo isset($_POST['query'])?$_POST['query']:"" ?>" />
</form>
</span>
<span id="tabselector">
Select one table: 
<select id="phase_select">
	<option>Phase</option>
	<option>Planning</option>
	<option>Characterization</option>
	<option>Qualitative Risk</option>
	<option>Detailed Risk</option>
	<option>Remediation strategies</option>
	<option>Remediation technologies</option>
	<option>Remediation plan</option>
	<option>De-costruction reuse</option>
	<option>Socio-economic</option>
	<option>Decision making</option>
	<option>Implementation</option>
	<option>Funding and financing</option>
</select>
<select id="typ_select">
	<option>Typology</option>
	<option>Regulation</option>
	<option>Technical manuals</option>
	<option>Tools</option>
	<option>Case studies</option>
</select>
</span>
</div>

<div id="search_results_div">
<span id="foundcount">
Found 0 items
</span>
<span id="exitsearch"><a href="explore.php">Exit search</a></span>
</div>

<div id="explore_div">
	<div id="side_menu">
	<span id="framework_link"><a class=""><span style="">&nbsp;</span><a></span>
	<ul id="side_menu_list">	
	<li><a class='selected phase'><span>All</span></a></li>
	<li><a class='phase'><span>Planning</span></a></li>
	<li><a class='phase'><span>Characterization</span></a></li>
	<li><a class='phase'><span>Qualitative Risk</span></a></li>
	<li><a class='phase'><span>Detailed Risk</span></a></li>
	<li><a class='phase'><span>Remediation strategies</span></a></li>
	<li><a class='phase'><span>Remediation technologies</span></a></li>
	<li><a class='phase'><span>Remediation plan</span></a></li>
	<li><a class='phase'><span>De-costruction reuse</span></a></li>
	<li><a class='phase'><span>Socio-economic</span></a></li>
	<li><a class='phase'><span>Decision making</span></a></li>
	<li><a class='phase'><span>Implementation</span></a></li>
	<li><a class='phase'><span>Funding and financing</span></a></li>	
	</ul>
	</div>
	<div id="tab_menu">
		<div id="tabs"><ul>
		<li><a class='typ selected'><span>All</span></a></li>
		<li><a class='typ'><span>Regulation</span></a></li>
		<li><a class='typ'><span>Technical manuals</span></a></li>
		<li><a class='typ'><span>Tools</span></a></li>
		<li><a class='typ'><span>Case studies</span></a></li>
		</ul></div>
		
		<div id="explore_content">---</div>
	</div>
</div>

</body>
</html>