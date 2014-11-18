<?php
include 'utils/functions.php';
include 'utils/data.php';

        ini_set('display_errors',1);
        ini_set('display_startup_errors',1);
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

$query = sprintf("select id, rating, numrates, title from project where id=%d", $_GET['id']);

//echo $query;
$result = $mysqli->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);

if (isset($_GET['submit'])) {

    $numrates = $row['numrates'];
    //$rating = $row['rating'] * $numrates++ + ($_POST['valstar1'] + $_POST['valstar2'] + $_POST['valstar3'] + $_POST['valstar4']) / 4.0;
    for($i=1;$i<=4;$i++)
    {
        if(isset($_POST['valstar'.$i]))
            $stars[$i] = $_POST['valstar'.$i];
        else
            $stars[$i] = 'NULL';
    }
    var_dump($row);echo "<br>";
    $array = array(
        'user_id' => $_SESSION['userid'],
        'project_id' => $row['id'],
        'question1' => $_POST['question1']=='Yes'?1:0,
        'question2' => $_POST['question2']=='Yes'?1:0,
        'explanation' => $_POST['explanation'],
        'rating1' => $stars[1],
        'rating2' => $stars[2],
        'rating3' => $stars[3],
        'rating4' => $stars[4]
    );
    insertOrUpdate($mysqli, 'rating', array_keys($array), array_values($array), array(2,3,4,5,6,7,8));
    var_dump($row);echo "<br>";
    $pid = $row['id'];
    var_dump($pid);echo "<br>";
    $result = $mysqli->query(
<<<EOF
   SELECT p.id, p.rating1, p.rating2, p.rating3, 
          count(r.rating1) as c, 
          avg(r.rating1) as r1, avg(r.rating2) as r2, avg(r.rating3) as r3, avg(r.rating4) as r4  
          FROM project p LEFT JOIN (SELECT * FROM rating r WHERE r.question1=1 AND r.question2=1) r
          ON r.rating1>0 AND r.project_id=p.id 
          WHERE p.id=$pid 
          GROUP BY p.id
EOF
);
    $row = $result->fetch_assoc();
    $rating =  (($row['rating1'] + $row['rating2'] + $row['rating3'])/3 + ($row['r1'] + $row['r2'] + $row['r3'] + $row['r4'])*$row['c']/4)/($row['c']+1);
    var_dump($row);
    var_dump($rating);
    
    $array = array(
      'id' => $pid,
      'rating'  => $rating
    );
    insertOrUpdate($mysqli, "project", array_keys($array), array_values($array), array(1));
    
    exit;
}
?>
<strong>Rating: </strong><em><?= $row['title'] ?></em>


<form id="ratingform" action="rating.php?submit=true&id=<?= $_GET['id'] ?>" method="post">

    <p>Was the information you found related to the search you were performing?<br/>
        <input id="q1" type="radio" name="question1" value="Yes"> Yes
        <input type="radio" name="question1" value=No"> No
    </p>

    <p>Do you think that the content of the link was appropriate for the category of concern?<br/>
        <input id="q2" type="radio" name="question2" value="Yes"> Yes
        <input type="radio" name="question2" value=No"> No
    </p>

    <p>Please, provide an explanation:<br/>
        <textarea name="explanation" cols="70" rows="2" ></textarea>
    </p>

    <p id="rate_section">
        Please, rate the information you just visualised:<br />
    <table>
        <tr>
            <td>
                Usefulness<br />
                <small><em>
                        Is the information in the e-link useful to achieve your job objectives?                
                    </em></small> 
            </td>
            <td><span style="position:relative; top:5px;" class="stars" name="star1" style=""></span></td>
        </tr>
        <tr>
            <td>
                Clarity<br />
                <small><em>
                        Is the information in the e-link clear in the description of concepts and in the use of the specific vocabulary?

                    </em></small> 
            </td>
            <td><span style="position:relative; top:5px;" class="stars" name="star2" style=""></span></td>
        </tr>
        <tr>
            <td>
                Reliability and accuracy<br />
                <small><em>
                        Are the source of information or the authors “officially” recognised ( e.g., well known scientists, public authorities) and the information accurate and trustworthy?             
                    </em></small> 
            </td>
            <td><span style="position:relative; top:5px;" class="stars" name="star3" style=""></span></td>
        </tr>
        <tr>
            <td>
                Updating<br />
                <small><em>
                        Is the information in the e-link up to date and in line with the latest regulatory prescriptions?            
                    </em></small> 
            </td>
            <td><span style="position:relative; top:5px;" class="stars" name="star4" style=""></span></td>
        </tr>
    </table>
</p>

<input type="button" name="submit" id="submit" value="Send" style="float: right;" />


<script>
    $('.stars').each(function( index ) {
        $(this).after("<input type='text' name='val"+$(this).attr('name')+"' id='val"+$(this).attr('name')+"' />");
    });  
             
    $('.stars').each(function( index ) {
        $(this).raty({
            path:  'js/jquery.raty/img/',
            width: '150px',
            target     : '#val'+$(this).attr('name'),
            targetType : 'number',
            targetKeep : true
        });
    });
    
    $('.stars').each(function( index ) { $('#val'+$(this).attr('name')).hide();});
        
        
    $("#submit").click(function(){
        //verify completeness...
        var rating_completed=true;
        for(var i=1; i<=4; i++)
            {                
                if($("#valstar"+i).val()<=0 || $("#valstar"+i).val()=='')
                    rating_completed=false;
            }
        if((""+$("#q1").attr("checked")!="undefined") && (""+$("#q2").attr("checked")!="undefined") && !rating_completed)
            {
                alert("You must rate all the criteria!")
                return;
            }
        animaterating(<?= $_GET['id'] ?>,false);
        $("#rating_container").fadeOut();
        $.post("rating.php?submit=true&id=<?= $_GET['id'] ?>", $("#ratingform").serialize(), function(data, ts, jq){} );
    });
    
    function updateRateSection()
    {
        var visible = (""+$("#q1").attr("checked")!="undefined") && (""+$("#q2").attr("checked")!="undefined");
        if(visible)
            $("#rate_section").slideDown();
        else
            $("#rate_section").slideUp();
        
    }
    $("#rate_section").hide();
    
    $("[type=radio]").change(updateRateSection);
</script>
</form>