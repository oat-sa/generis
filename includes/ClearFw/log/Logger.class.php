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
 * Deprecated, see common_Logger
 *
 */

class Logger
{
	const debug_level 	= common_Logger::DEBUG_LEVEL;
	const info_level 	= common_Logger::INFO_LEVEL;
	const warning_level = common_Logger::WARNING_LEVEL;
	const error_level	= common_Logger::ERROR_LEVEL;
	const fatal_level 	= common_Logger::FATAL_LEVEL;
	
	private $module;
	private $min_level;
	
	
	public function __construct($module, $min_level = common_Logger::DEBUG_LEVEL) {
		$this->module = $module;
		$this->min_level = $min_level;
	}
	
	public function debug($msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($this->min_level >= common_Logger::DEBUG_LEVEL) {
			common_Logger::d($msg.' - '.$fichier.' - '.$no_ligne, array($module == '' ? $this->module : $module));
		}
	}
	
	public function info($msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($this->min_level >= common_Logger::INFO_LEVEL) {
			common_Logger::i($msg.' - '.$fichier.' - '.$no_ligne, array($module == '' ? $this->module : $module));
		}
	}
	
	public function warning($msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($this->min_level >= common_Logger::WARNING_LEVEL) {
			common_Logger::w($msg.' - '.$fichier.' - '.$no_ligne, array($module == '' ? $this->module : $module));
		}
	}
	
	public function error($msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($this->min_level >= common_Logger::ERROR_LEVEL) {
			common_Logger::e($msg.' - '.$fichier.' - '.$no_ligne, array($module == '' ? $this->module : $module));
		}
	}
	
	public function fatal($msg, $fichier = '', $no_ligne = '', $module = '') {
		if ($this->min_level >= common_Logger::FATAL_LEVEL) {
			common_Logger::f($msg.' - '.$fichier.' - '.$no_ligne, array($module == '' ? $this->module : $module));
		}
	}
				
}
?>
