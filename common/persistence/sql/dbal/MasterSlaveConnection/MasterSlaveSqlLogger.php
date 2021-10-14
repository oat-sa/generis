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

use Doctrine\DBAL\Logging\SQLLogger;

class MasterSlaveSqlLogger implements SQLLogger
{
    private static $read = 0;
    private static $write = 0;

    private $label;

    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        if (stripos($sql, 'SELECT') === 0) {
            self::$read++;
            $target = 'slave';
        } else {
            self::$write++;
            $target = 'master';
        }

        if ($target !== $this->label) {
            \common_Logger::e(
                sprintf(
                    '[ERROR] %s [%s] %s',
                    debug_backtrace()[1]['function'],
                    $this->label,
                    $sql
                )
            );
        } else {
            \common_Logger::i(
                sprintf(
                    '[INFO] %s [%s] %s...',
                    debug_backtrace()[1]['function'],
                    $this->label,
                    substr($sql, 0, 10)
                )
            );
        }
    }

    public function __destruct()
    {
        \common_Logger::d(sprintf('[READ] - %s', self::$read));
        \common_Logger::d(sprintf('[WRITE] - %s', self::$write));
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }
}
