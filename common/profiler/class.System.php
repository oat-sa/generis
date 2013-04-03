<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 * 
 */

/**
 *	Represent the system being profiled
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage log
 */
class common_profiler_System
{
	public function __construct(){
		$this->computerId = $this->getComputerId();
		$this->taoId = $this->getTaoInstanceId();
	}
	
	protected function getTaoInstanceId(){
		$key = LOCAL_NAMESPACE.GENERIS_INSTANCE_NAME.GENERIS_SESSION_NAME.SYS_USER_LOGIN.SYS_USER_PASS;
		return md5($key);
	}
	
	protected function getComputerId(){
		$key = $_SERVER['SERVER_SIGNATURE'].$_SERVER['SERVER_ADMIN'].$_SERVER['DOCUMENT_ROOT'];
		return md5($key);
	}
	
}
