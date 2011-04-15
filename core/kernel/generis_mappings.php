<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/

$GLOBALS["mappings"] = array(

"http://www.tao.lu/Ontologies/generis.rdf" => "<i>generis:</i>",

"http://www.w3.org/2000/01/rdf-schema" => "<i>rdfs:</i>",

"http://www.tao.lu/Ontologies/TAO.rdf" => "tao:",

"http://www.tao.lu/Ontologies/TAOSubject.rdf" => "<span style=color:blue>Subject:</span>",

"https://bscw.ercim.org/bscw/bscw.cgi/d204590/collaboration.rdfs" => "<span style=color:blue>Collaboration:</span>",

"https://bscw.ercim.org/bscw/bscw.cgi/d204606/process-activity.rdfs" => "<span style=color:red>Process-Activity:</span>",
"https://bscw.ercim.org/bscw/bscw.cgi/d204594/competency.rdfs" => "<span style=color:brown>Competency:</span>",
"https://bscw.ercim.org/bscw/bscw.cgi/d204598/learner.rdfs" => "<span style=color:green>Learner:</span>",

"https://bscw.ercim.org/bscw/bscw.cgi/d204602/lessonsLearnt.rdfs" => "<span style=color:#FF9933>LessonsLearnt:</span>",
"" => "local:"


);


function getPrefix($uri){error_reporting(0);return $GLOBALS["mappings"][substr($uri,0,strpos($uri,"#"))];}
?>
