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
 * abstraction d'un backend de sortie de log
 * @package log 
 * @author Alain Vagner
 */
abstract class Appender
{
	protected $level;
	protected $config;
	
	public function __construct($level, $config)
	{
		$this->level = $level;
		$this->config = $config;	
	}
	
	public function log($logitem)
	{
			if ($logitem->gravite >= $this->level) {
				 $this->write($logitem);
			}	
		
	}
	
	abstract public function write($logitem);

	
	public function getLevel($i)
	{
		 switch ($i) {
			case Logger::debug_level:
				return "DEBUG";
			case Logger::info_level:
				return "INFO";
			case Logger::warning_level:
				return "WARNING";
			case Logger::error_level:
				return "ERROR";
			case Logger::fatal_level:
				return "FATAL";	 	
		 } 
	}
}
?>
