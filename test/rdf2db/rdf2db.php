<?php 

$dbName = "mytao";
$importDataTest = true;

// create the rdf2db tables
echo ('CREATE HARD DB ');
echo system("mysql -u root --password=root {$dbName} < ./create_tables.sql");
echo ('[OK]<br/>');

// import a test database
if ($importDataTest){
	echo ('IMPORT DATA TEST ');
	system ("mysql -u root --password=root {$dbName} < ./mytao_dump.sql");
	echo ('[OK]<br/>');	
}

///////////////////////////////////////////////////////
//	EXTRACT DATA
///////////////////////////////////////////////////////

echo ('EXTRACT SUBJECT');
echo system('mysql -u root --password=root mytao < 12Subject/extract_12Subject.sql > tmp/12Subject.txt');
echo ('[OK]<br/>');

echo ('FORMAT SUBJECT');
echo exec('rdf2db/format_csv.sh tmp/12Subject.txt tmp/12Subject.csv');

?>
