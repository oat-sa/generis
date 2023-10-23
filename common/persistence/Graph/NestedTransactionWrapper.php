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

use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Databags\SummarizedResult;

class NestedTransactionWrapper implements TransactionManagerInterface
{
    private TransactionManagerInterface $nestedTransactionManager;
    private int $transactionNestingLevel = 0;
    private bool $isRollbackOnly = false;

    public function __construct(TransactionManagerInterface $nestedManager)
    {
        $this->nestedTransactionManager = $nestedManager;
    }

    public function beginTransaction(): void
    {
        $this->transactionNestingLevel++;

        if ($this->transactionNestingLevel === 1) {
            $this->nestedTransactionManager->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->transactionNestingLevel === 0) {
            throw new GraphTransactionException('Transaction should be started first.');
        }

        if ($this->isRollbackOnly) {
            throw new GraphTransactionException(
                'Nested transaction failed, so all data should be rolled back now.'
            );
        }

        if ($this->transactionNestingLevel === 1) {
            $this->nestedTransactionManager->commit();
        }

        $this->transactionNestingLevel--;
    }

    public function rollback(): void
    {
        if ($this->transactionNestingLevel === 0) {
            throw new GraphTransactionException('Transaction should be started first.');
        }

        if ($this->transactionNestingLevel === 1) {
            $this->nestedTransactionManager->rollBack();
            $this->isRollbackOnly = false;
        } else {
            $this->isRollbackOnly = true;
        }

        $this->transactionNestingLevel--;
    }

    public function run(string $statement, iterable $parameters = []): SummarizedResult
    {
        return $this->nestedTransactionManager->run($statement, $parameters);
    }

    public function runStatement(Statement $statement): SummarizedResult
    {
        return $this->nestedTransactionManager->runStatement($statement);
    }
}
