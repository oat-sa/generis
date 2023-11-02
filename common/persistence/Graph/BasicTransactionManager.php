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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\persistence\Graph;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\UnmanagedTransactionInterface;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Databags\SummarizedResult;

class BasicTransactionManager implements TransactionManagerInterface
{
    private ClientInterface $client;
    private UnmanagedTransactionInterface $transaction;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function beginTransaction(): void
    {
        try {
            $this->transaction = $this->client->beginTransaction();
        } catch (\Throwable $e) {
            throw new GraphTransactionException('Transaction was not started.', $e);
        }
    }

    public function commit(): void
    {
        try {
            if (isset($this->transaction)) {
                $this->transaction->commit();
                unset($this->transaction);
            }
        } catch (\Throwable $e) {
            throw new GraphTransactionException('Transaction was not committed.', $e);
        }
    }

    public function rollback(): void
    {
        try {
            if (isset($this->transaction)) {
                $this->transaction->rollback();
                unset($this->transaction);
            }
        } catch (\Throwable $e) {
            throw new GraphTransactionException('Transaction was not rolled back.', $e);
        }
    }

    public function run(string $statement, iterable $parameters = []): SummarizedResult
    {
        try {
            if (isset($this->transaction)) {
                $result = $this->transaction->run($statement, $parameters);
            } else {
                $result = $this->client->run($statement, $parameters);
            }
        } catch (\Throwable $e) {
            throw new GraphTransactionException(
                sprintf('Exception happen during query run: %s.', $e->getMessage()),
                $e
            );
        }

        return $result;
    }

    public function runStatement(Statement $statement): SummarizedResult
    {
        try {
            if (isset($this->transaction)) {
                $result = $this->transaction->runStatement($statement);
            } else {
                $result = $this->client->runStatement($statement);
            }
        } catch (\Throwable $e) {
            throw new GraphTransactionException(
                sprintf('Exception happen during statement run: %s.', $e->getMessage()),
                $e
            );
        }

        return $result;
    }
}
