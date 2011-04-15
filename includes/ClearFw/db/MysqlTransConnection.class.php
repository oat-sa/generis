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
// transactions and prepared statements handling
class MysqlTransConnection extends mysqlConnection {


	protected $autoCommit = true;
	protected $inTransaction = false;
	
	public function setAutoCommit($status) {
		if (!$this->autoCommit && $status && $this->inTransaction) {
			$this->commit(); 
		}		
		$this->autoCommit = $status; 
	}
	
	public function select($req, $params = null) {
		if (!$this->autoCommit && !$this->inTransaction) {
			$this->begin(); 
		}
		if ($params !== null) {
			$req = $this->emuPrepStmt($req, $params);
		}
		return parent::select($req);		
	}

	public function execute($req, $params = null) {
		if (!in_array($req, array('BEGIN', 'COMMIT', 'ROLLBACK'))) {
			if (!$this->autoCommit && !$this->inTransaction) {
				$this->begin(); 
			}
			if ($params !== null) {
				$req = $this->emuPrepStmt($req, $params);
			}
		}
		return parent::execute($req);	
	}
	
	public function begin() {
		$this->inTransaction = true;
		parent::begin();
	}
	
	public function commit() {
		$this->inTransaction = false;
		parent::commit();		
	}
	
	public function rollback() {
		$this->inTransaction = false;
		parent::rollback();		
	}	

	private function emuPrepStmt($req, $params) {
		$nb = substr_count($req, '?');
		if ($nb != count($params)) {
			throw new Exception('emuPrepStmt: incorrect arguments number');
		}
		
		// we escape all parameters
		$params = array_map(array($this, 'db_escape_string'), $params);
		$req = str_replace('?', '\'%s\'', $req);
		$req = vsprintf($req, $params);
		return $req;
	}
}
?>