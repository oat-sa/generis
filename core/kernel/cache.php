<?php


class rdfs_cache
{
	var $model;
		
	function rdfs_cache($model)
	{
		$this->model=$model;
		$sql = "
		CREATE TABLE IF NOT EXISTS `_cache_rdfs` (
		  `service` varchar(255) NOT NULL default '',
		  `params` varchar(255) NOT NULL default '',
		  `response` longtext NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		
		$this->model->con->Execute($sql);
	}
	function cacheisValid() {}
 
	function isincache($service,$parameters) 
	{ 
		//while the cache is not fully implemented return false
		return false;
		$sql = "Select * from _cache_rdfs where service = '".$service."' and params = '".serialize($parameters)."'";
		$sqlresult = $this->model->con->Execute($sql);
		 while (!$sqlresult->EOF) {return true;} return false;
	}
	function setincache($service,$parameters,$answer)
	{			
		$sql = "INSERT INTO `_cache_rdfs` ( `service` , `params` , `response` )
		VALUES ('".$service."','".serialize($parameters)."','".mysql_real_escape_string(serialize($answer))."')";
		$this->model->con->Execute($sql);
	}
	
	function getincache($service,$parameters)
	{	
		$sql = "Select response from _cache_rdfs where service = '".$service."' and params = '".serialize($parameters)."'";
		$sqlresult = $this->model->con->Execute($sql);
		 while (!$sqlresult->EOF)
           {
			 return unserialize($sqlresult->fields[0]);
		   }
		 return false;
	}
} 
?>
