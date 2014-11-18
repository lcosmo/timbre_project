<?php
/*
 * Author - Rob Thomson <rob@marotori.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once 'utils/functions.php';

session_init();
ob_start();

/* config settings */
if(!(isset($_POST["url"]) || isset($_GET["url"])))
{
	$base = "http://157.138.20.57:8887";  //set this to the url you want to scrape
	$ckfile = '/tmp/simpleproxy-cookie-'.session_id();  //this can be set to anywhere you fancy!  just make sure it is secure.



	/* all system code happens below - you should not need to edit it! */

	//work out cookie domain
	$cookiedomain = str_replace("http://www.","",$base);
	$cookiedomain = str_replace("https://www.","",$cookiedomain);
	$cookiedomain = str_replace("www.","",$cookiedomain);

	$url = $base . str_replace("~timbre/js/jquery-validation/phpproxy/","",$_SERVER['REQUEST_URI']);
}
else
{
	$url=isset($_POST["url"])?$_POST["url"]:$_GET["url"];
}
echo "<p><form method='POST'><strong>URL:<input type='text' size='100' name='url' value='".$url."' ></strong></form></p>";
$handle = fopen($url,"rt");
$source_code = fread($handle,9000);

//echo $source_code;
echo str_replace("href=\"", "href=\"?url=http://157.138.20.57:8887/", $source_code);
?>