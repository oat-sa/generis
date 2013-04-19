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
 * Profiler config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 * @subpackage conf
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

$defaultConfig = array(
	'context' => array(
		'active' => true
	),
	'timer' => array(
		'active' => true,
		'flags' => array()//empty => all (not impl yet)
	),
	'memoryPeak' => array(
		'active' => true
	),
	'countQueries' => array(
		'active' => true
	),
	'slowQueries' => array(
		'active' => true,
		'threshold'=> 100,//ms
	),
	'slowestQueries' => array(
		'active' => true,
		'count' => 3
	),
	'queries' => array(
		'active' => false,
		'count'=> 10,//most used queries?
	)
);
$GLOBALS['COMMON_PROFILER_CONFIG'] = array(
	/*array_merge(
		array(
			'class'		=> 'LoggerAppender',
			'tag'		=> 'PROFILER'
		), 
		$defaultConfig,
		array()
	)
	,array_merge(
		array(
			'class'		=> 'SystemProfileAppender'
		), 
		$defaultConfig,
		array()
	)
	*/
);