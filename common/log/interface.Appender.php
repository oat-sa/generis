<?php

/**
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * Short description of class common_log_Appender
 *
 * @access public
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 * @package generis
 */
interface common_log_Appender
{
    // --- OPERATIONS ---

    /**
     * decides whenever the Item should be logged by doLog
     *
     * @access public
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     *
     * @param  Item item
     *
     * @return mixed
     *
     * @see doLog
     */
    public function log(common_log_Item $item);

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     *
     * @return int
     */
    public function getLogThreshold();

    /**
     * Short description of method init
     *
     * @access public
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     *
     * @param  array configuration
     * @param mixed $configuration
     *
     * @return boolean
     */
    public function init($configuration);
} /* end of interface common_log_Appender */
