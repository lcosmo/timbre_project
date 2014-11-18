<form method="POST">
    <input type="submit" name="clearlog" value="Clear Log"/>
</form>
<pre>
<?php
if(isset($_POST['clearlog']))
{
    $myFile = "../query_log.txt";
    unlink ($myFile); 
}
echo implode("\n", array_reverse(explode("\n", file_get_contents('../query_log.txt'))));
?>
</pre>