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
 *
 */
/**
 * Generis Object Oriented API - common/exception/class.InvalidArgumentType.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.01.2012, 16:44:05 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */


require_once('common/exception/class.Error.php');

/* user defined includes */
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-includes begin
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-includes end

/* user defined constants */
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-constants begin
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-constants end

/**
 * 
 * @access public
 * @author Patrick Plichart
 * @package common
 * @subpackage exception
 */
abstract class common_exception_BadRequest
    extends common_exception_ClientException
{
    // --- ASSOCIATIONS ---
    // --- ATTRIBUTES ---
  public function handle($clientType="HTTP"){
	header("HTTP/1.0 400 Bad Request" );
	return $this;
    }

} /* end of class common_exception_InvalidArgumentType */

?>