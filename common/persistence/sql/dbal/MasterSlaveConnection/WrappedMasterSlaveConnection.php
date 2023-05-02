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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\generis\persistence\sql\dbal\MasterSlaveConnection;

use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use InvalidArgumentException;

class WrappedMasterSlaveConnection extends MasterSlaveConnection
{
    public function connect($connectionName = null)
    {
        $requestedConnectionChange = ($connectionName !== null);
        $connectionName = $connectionName ?: 'slave';

        if ($connectionName !== 'slave' && $connectionName !== 'master') {
            throw new InvalidArgumentException('Invalid option to connect(), only master or slave allowed.');
        }

        // If we have a connection open, and this is not an explicit connection
        // change request, then abort right here, because we are already done.
        // This prevents writes to the slave in case of "keepSlave" option enabled.
        if ($this->_conn !== null && ! $requestedConnectionChange) {
            return false;
        }

        $forceMasterAsSlave = false;

        if ($this->getTransactionNestingLevel() > 0) {
            $connectionName = 'master';
            $forceMasterAsSlave = true;
        }

        if (isset($this->connections[$connectionName])) {
            $this->_conn = $this->connections[$connectionName];

            if ($forceMasterAsSlave && ! $this->keepSlave) {
                $this->connections['slave'] = $this->_conn;
            }
            $this->configureLogger($connectionName);

            return false;
        }

        if ($connectionName === 'master') {
            $this->connections['master'] = $this->_conn = $this->connectTo($connectionName);

            // Set slave connection to master to avoid invalid reads
            if (! $this->keepSlave) {
                $this->connections['slave'] = $this->connections['master'];
            }
        } else {
            $this->connections['slave'] = $this->_conn = $this->connectTo($connectionName);
        }

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $eventArgs = new ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }
        $this->configureLogger($connectionName);

        return true;
    }

    private function configureLogger($label)
    {
        $logger = $this->_config->getSQLLogger();

        if ($logger && $logger instanceof MasterSlaveSqlLogger) {
            $logger->setLabel($label);
            $this->_config->setSQLLogger($logger);
        }
    }
}
