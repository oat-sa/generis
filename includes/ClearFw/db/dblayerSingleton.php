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
class dbLayerSingleton extends dbLayer {
	
	/**
	 * Singleton class 
	 */	
	protected static $instance;
	
	/**
	 * Gives an instance of dbLayer
	 * @param	string		$driver		
	 * @param	string		$host		
	 * @param	string		$database
	 * @param	string		$user
	 * @param	string		$password
	 * @param	boolean		$persistent
	 * @return	instance du driver	
	 */
	public static function getInstance($driver, $host, $database, $user='', $password='', $persistent=false) {
		if (!isset(self::$instance)) {
			self::$instance = self::init($driver, $host, $database, $user, $password, $persistent);
		}
		return self::$instance;
	}
	
	/**
	 * Returns the last inserted primary key in the database.
	 * (Only compatible with MySQL driver at the moment. Feel free to contribute !)
	 * FIXME All drivers support is needed. But shouldn't be supported inside of Clearbricks/Dblayer ?
	 */
	public static function insert_id() {
		return mysql_insert_id();
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
}
?>
