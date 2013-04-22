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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 *	Represent the context of current action to be logged
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage log
 */
class common_profiler_Context
{
	public function __construct(){
		$context = Context::getInstance();
		$this->extension = $context->getExtensionName();
		$this->module = $context->getModuleName();
		$this->action = $context->getActionName();
		$this->epoch = time();
		$this->user = wfEngine_models_classes_UserService::singleton()->getCurrentUser();
		$this->script = $_SERVER['PHP_SELF'];
		$this->system = new common_profiler_System();
	}
	
	public function getCalledUrl(){
		$returnValue = $this->script;
		if(!empty($this->extension) && !empty($this->module) && !empty($this->action)){
			$returnValue = $this->extension.'/'.$this->module.'/'.$this->action;
		}
		return $returnValue;
	}
	
	public function toArray(){
		$returnValue = get_object_vars($this);
		$returnValue['user'] = is_null($this->user)?'n/a':$this->user->getUri();
		$returnValue['system'] = $this->system->toArray();
		return $returnValue;
	}
}
