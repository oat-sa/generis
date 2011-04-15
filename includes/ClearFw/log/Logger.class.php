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
 * classe de saisie d'information de logs 
 * @package log 
 * @author Alain Vagner
 */
 
/*
 *  exemple d'utilisation : 
 * 
 * 	$logger = new Logger('module1', Logger::debug_level);
 *	$logger->debug('test log', __FILE__, __LINE__, 'module1');
 *
 */

class Logger
{
	const debug_level 	= 0;
	const info_level 	= 1;
	const warning_level = 2;
	const error_level	= 3;
	const fatal_level 	= 4;
	
	private $manager;
	private $module;
	private $min_level;
	
	
	public function __construct($module, $min_level = Logger::debug_level) {
		$this->manager = LogManager::getInstance();
		$this->module = $module;
		$this->min_level = $min_level;
	}	
	
	public function log($level, $msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($module === '') {
			$module = $this->module;
		}
		if ($level >= $this->min_level) {
			$item = new LogItem($module, time(), $msg, $level, $fichier, $no_ligne);
			$this->manager->log($item);	
		}	
	} 
	
	public function debug($msg, $fichier = '', $no_ligne = '', $module = '') {		
		$this->log(Logger::debug_level, $msg, $fichier, $no_ligne, $module);
	}
	
	public function info($msg, $fichier = '', $no_ligne = '', $module = '') {
		$this->log(Logger::info_level, $msg, $fichier, $no_ligne, $module);
	}
	
	public function warning($msg, $fichier = '', $no_ligne = '', $module = '') {
		$this->log(Logger::warning_level, $msg, $fichier, $no_ligne, $module);
	}
	
	public function error($msg, $fichier = '', $no_ligne = '', $module = '') {
		$this->log(Logger::error_level, $msg, $fichier, $no_ligne, $module);	
	}
	
	public function fatal($msg, $fichier = '', $no_ligne = '', $module = '') {
		$this->log(Logger::fatal_level, $msg, $fichier, $no_ligne, $module);
	}
				
}
?>
