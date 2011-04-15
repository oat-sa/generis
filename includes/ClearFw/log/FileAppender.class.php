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
 * backend de logging vers un fichier
 * @package log 
 * @author Alain Vagner
 */

class FileAppender extends Appender
{
	public function write($logitem)
	{
		
		if ($logitem->fichier == '') {
			$logitem->fichier = '-';	
		}

		if ($logitem->no_ligne == '') {
			$logitem->no_ligne = '-';	
		}		
		
		$str = '['.$logitem->module.'] '. date('Y-m-d H:i:s',$logitem->datetime).' \''.$logitem->description. '\' '.$this->getLevel($logitem->gravite). ' '.$logitem->fichier.' '.$logitem->no_ligne."\r\n";
	
		$f = @fopen($this->config, 'a');
		@fwrite($f, $str);
		@fclose($f);
	
	}
}
?>
