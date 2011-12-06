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
 * backend de logging vers un port, untested
 * @package log 
 * @author Joel Bout
 */

class XMLAppender extends Appender
{
	public function write($logitem)
	{
		$doc = new DOMDocument();
		$success = @$doc->load($this->config);
		// create
		if ($success == false) {
			$root = $doc->createElement("events");
			$doc->appendChild($root);
		} else {
			$root = $doc->getElementsByTagName("events")->item(0);
		}
		
		$event = $doc->createElement("event");
		$root->formatOutput = true;
		
		$message = $doc->createElement("message");
		$message->appendChild(
				$doc->createTextNode($logitem->description)
		);
		$event->appendChild($message);
		
		$file = $doc->createElement("file");
		$file->appendChild(
				$doc->createTextNode($logitem->fichier)
		);
		$event->appendChild($file);
		
		$line = $doc->createElement("line");
		$line->appendChild(
				$doc->createTextNode($logitem->no_ligne)
		);
		$event->appendChild($line);
		
		$datetime = $doc->createElement("datetime");
		$datetime->appendChild(
				$doc->createTextNode($logitem->datetime)
		);
		$event->appendChild($datetime);
		
		$severity = $doc->createElement("severity");
		$severity->appendChild(
				$doc->createTextNode($logitem->gravite)
		);
		$event->appendChild($severity);
		
		$root->appendChild($event);
		$doc->save($this->config);

	}

}
?>
