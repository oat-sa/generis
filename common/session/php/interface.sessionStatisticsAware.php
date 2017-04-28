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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
interface common_session_php_sessionStatisticsAware
{
    /**
     * Returns the unix timestamp of the start of the last call
     * to the system by a logged in user
     *
     * @return integer
     */
    public function getLastAccessTime();

    /**
     * Returns the number of non expired session of the current system
     * Will return -1 if the current session handling does not implement this
     *
     * @return integer
     */
    public function getTotalActiveSessions();
}