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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
namespace oat\generis\test\unit\oatbox\log;

use oat\oatbox\log\LoggerService;
use oat\oatbox\log\logger\TaoLog;
use oat\generis\test\TestCase;

class LoggerServiceTest extends TestCase {
	
	const RUNS = 1000;
    
    protected function setUp()
    {
	}
	
	public function testFileAppender()
	{
		$dfile = tempnam(sys_get_temp_dir(), "logtest");
		$ifile = tempnam(sys_get_temp_dir(), "logtest");
		$wfile = tempnam(sys_get_temp_dir(), "logtest");
		$efile = tempnam(sys_get_temp_dir(), "logtest");
		$cfile = tempnam(sys_get_temp_dir(), "logtest");

        $logger = new LoggerService(array(
            'logger' => new TaoLog(array(
                'appenders' => array(
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::DEBUG_LEVEL,
                        'file'			=> $dfile,
                    ),
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::INFO_LEVEL,
                        'file'			=> $ifile,
                    ),
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::WARNING_LEVEL,
                        'file'			=> $wfile,
                    ),
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::ERROR_LEVEL,
                        'file'			=> $efile,
                    ),
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::FATAL_LEVEL,
                        'file'			=> $cfile,
                    ),
                )
            ))
        ));

        $logger->logDebug('message');
        $this->assertEntriesInFile($dfile, 1);
		$this->assertEntriesInFile($ifile, 0);
		$this->assertEntriesInFile($wfile, 0);
		$this->assertEntriesInFile($efile, 0);
		$this->assertEntriesInFile($cfile, 0);

		$logger->logInfo('message');
        $this->assertEntriesInFile($dfile, 2);
		$this->assertEntriesInFile($ifile, 1);
		$this->assertEntriesInFile($wfile, 0);
		$this->assertEntriesInFile($efile, 0);
		$this->assertEntriesInFile($cfile, 0);

		$logger->logWarning('message');
        $this->assertEntriesInFile($dfile, 3);
		$this->assertEntriesInFile($ifile, 2);
		$this->assertEntriesInFile($wfile, 1);
		$this->assertEntriesInFile($efile, 0);
		$this->assertEntriesInFile($cfile, 0);

        $logger->logError('message');
        $this->assertEntriesInFile($dfile, 4);
        $this->assertEntriesInFile($ifile, 3);
        $this->assertEntriesInFile($wfile, 2);
        $this->assertEntriesInFile($efile, 1);
        $this->assertEntriesInFile($cfile, 0);

        $logger->logAlert('message');
        $this->assertEntriesInFile($dfile, 5);
        $this->assertEntriesInFile($ifile, 4);
        $this->assertEntriesInFile($wfile, 3);
        $this->assertEntriesInFile($efile, 2);
        $this->assertEntriesInFile($cfile, 1);

        //destroy logger object to release files
        unset($logger);

        unlink($dfile);
        unlink($ifile);
        unlink($wfile);
        unlink($efile);
        unlink($cfile);
    }
	
	public function testLogTags()
	{
        $dfile = tempnam(sys_get_temp_dir(), "logtest");
        $this->assertEntriesInFile($dfile, 0);

        $logger = new LoggerService(array(
            'logger' => new TaoLog(array(
                'appenders' => array(
                    array(
                        'class'			=> 'SingleFileAppender',
                        'threshold'		=> \common_Logger::DEBUG_LEVEL,
                        'file'			=> $dfile,
                        'tags'			=> 'CORRECTTAG'
                    ),
                )
            ))
        ));

        $logger->logDebug('message');
		$this->assertEntriesInFile($dfile, 0);
		
        $logger->logDebug('message', ['WRONGTAG']);
		$this->assertEntriesInFile($dfile, 0);

        $logger->logDebug('message', ['CORRECTTAG']);
		$this->assertEntriesInFile($dfile, 1);

        $logger->logDebug('message', array('WRONGTAG', 'CORRECTTAG'));
		$this->assertEntriesInFile($dfile, 2);

        $logger->logDebug('message', array('WRONGTAG', 'WRONGTAG2'));
		$this->assertEntriesInFile($dfile, 2);
        //destroy logger object to release files
        unset($logger);

        unlink($dfile);
    }
	
	public function assertEntriesInFile($pFile, $pCount) {
		if (file_exists($pFile)) {
			$count = count(file($pFile));
		} else {
			$count = 0;
		}
		$this->assertEquals($count, $pCount, 'Expected count '.$pCount.', had '.$count.' in file '.$pFile);
	}
}