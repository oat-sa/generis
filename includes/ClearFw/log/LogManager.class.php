<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "ClearFw".
# Copyright (c) 2007 CRP Henri Tudor and contributors.
# All rights reserved.
#
# "ClearFw" is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as published by
# the Free Software Foundation.
# 
# "ClearFw" is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with "ClearFw"; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
/**
 * classe de gestion du processus de logging 
 * @package log 
 * @author Alain Vagner
 */

class LogManager
{

	private $appenders; # tableau contenant les appenders
	private static $instance;
	
	function __construct() {
		$this->appenders = array();
		if (!isset($GLOBALS['config_log'])) {
			throw new Exception('log library: undefined variable $GLOBALS[\'config_log\']');
		}
		foreach ($GLOBALS['config_log'] as $appender) {
			$nom = $appender['nom'];
			$level = $appender['level'];
			$config = $appender['config'];	
			$newappender = new $nom($level, $config);
			$this->appenders[] = $newappender; 	
		}
	}

	public static function getInstance() {
	
		if (!isset(self::$instance)) {
			self::$instance = new LogManager();
		}
		return self::$instance;
	}	

	function log($logitem) {
		foreach ($this->appenders as $a) {
			$a->log($logitem);
		} 	
	}	
}

?>
