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

class DbLayerTransSingleton extends dbLayerSingleton {
	
	/**
	Static function to use to init database layer. Returns a object extending
	dbLayer.
	
	@param	driver		<b>string</b>		Driver name
	@param	host			<b>string</b>		Database hostname
	@param	database		<b>string</b>		Database name
	@param	user			<b>string</b>		User ID
	@param	password		<b>string</b>		Password
	@param	persistent	<b>boolean</b>		Persistent connection (false)
	@return	<b>object</b>
	*/
	public static function init($driver,$host,$database,$user='',$password='',$persistent=false)
	{
		$driver = Camelizer::firstToUpper($driver);

		if (file_exists(dirname(__FILE__).'/'.$driver.'TransConnection.class.php')) {
			require_once dirname(__FILE__).'/'.$driver.'TransConnection.class.php';
			$driver_class = $driver.'TransConnection';
		} else {
			trigger_error('Unable to load DB layer for '.$driver,E_USER_ERROR);
			exit(1);
		}
		

		
		return new $driver_class($host,$database,$user,$password,$persistent);
	}


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

}
?>