<?php
$link = mysql_connect('localhost', 'root', 'root');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';

mysql_select_db("ultimate_rdf2db", $link);

//$sql = "SELECT * FROM 06Language";
//$query = mysql_query($sql, $link) OR die(mysql_error());

for ($i=0; $i<155; $i++){
    $sql = "UPDATE 12Subject SET `07userUILg`='{$i}',`07userDefLg`='{$i}' WHERE (`id`%'154')='{$i}'";
    $query = mysql_query($sql, $link) OR die(mysql_error());
}

mysql_close($link);
?>

