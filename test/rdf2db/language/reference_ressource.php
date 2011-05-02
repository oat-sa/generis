<?php
$link = mysql_connect('localhost', 'root', 'root');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';

mysql_select_db("ultimate_rdf2db", $link);

$sql = "SELECT uri FROM 06Languages";
$query = mysql_query($sql, $link) OR die(mysql_error());

while($res = mysql_fetch_array($query)){
    $sqlInsert = "INSERT INTO `ressource_to_table` (`uri`,`table`) VALUES ('".$res['uri']."','06Languages')";
    $queryInsert = mysql_query($sqlInsert, $link) OR die(mysql_error());
}

mysql_close($link);
?>

