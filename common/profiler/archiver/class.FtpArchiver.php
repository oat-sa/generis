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
 * 
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage profiler
 */
class common_profiler_archiver_FtpArchiver implements common_profiler_archiver_Archiver
{
	
	protected $ftpServer = '127.0.0.1';
	protected $ftpPort = '21';
	protected $ftpUser = 'ftpUser';
	protected $ftpPassword = '123456';
	
	public function init($configuration){
		
		$returnValue = false;
		
		if(isset($configuration['ftp_server']) && !empty($configuration['ftp_server'])){
			$this->ftpServer = (string)$configuration['ftp_server'];
			$returnValue = true;
		}
		if(isset($configuration['ftp_port']) && !empty($configuration['ftp_port'])){
			$this->ftpPort = intval($configuration['ftp_port']);
		}
		if(isset($configuration['ftp_user']) && !empty($configuration['ftp_user'])){
			$this->ftpUser = (string)($configuration['ftp_user']);
		}
		if(isset($configuration['ftp_password']) && !empty($configuration['ftp_password'])){
			$this->ftpPassword = intval($configuration['ftp_password']);
		}
		
		return $returnValue;
	}
	
	public function store($filePath){
		
		// set up a connection or die
		$ftpStream = ftp_connect($this->ftpServer, $this->ftpPort);
		if ($ftpStream == false) {
			common_Logger::d('cannot connect to profiling server', 'PROFILER');
		} else {
			if (ftp_login($ftpStream, $this->ftpUser, $this->ftpPassword)) {
				$system = new common_profiler_System();
				$remoteFile = 'taoProfile_'.$system->getComputerId().'_'.time();
				$res = ftp_nb_put($ftpStream, $remoteFile, $filePath, FTP_ASCII);
				if (!$res) {
					common_Logger::d('profiles upload failed!', 'PROFILER');
				}
				common_Logger::d('profiles uploaded to analysis server', 'PROFILER');
			} else {
				common_Logger::d('cannot log into ftp server: ' . $this->ftpServer, 'PROFILER');
			}
		}
		
	}

}