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
 * backend de logging vers une boite mail 
 * @package log 
 * @author Alain Vagner
 */
class MailAppender extends Appender
{
	public function write($logitem)
	{		
		
		$str  = 'Module: '.$logitem->module."\r\n";
		$str .= 'Date: '.date('Y-m-d H:i:s',$logitem->datetime)."\r\n";
		$str .= 'Description: '.$logitem->description."\r\n";
		$str .= 'Severity: '.$logitem->gravite."\r\n";
		$str .= 'File: '.$logitem->fichier."\r\n";
		$str .= 'Line: '.$logitem->no_ligne."\r\n";
	
		@mail($this->config, 'Warning', $str);
	
	}
}
?>
