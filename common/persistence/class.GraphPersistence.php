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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Databags\SummarizedResult;
use oat\generis\persistence\Graph\BasicTransactionManager;
use oat\generis\persistence\Graph\NestedTransactionWrapper;
use oat\generis\persistence\Graph\TransactionManagerInterface;

class common_persistence_GraphPersistence extends common_persistence_Persistence implements
    common_persistence_Transactional
{
    private TransactionManagerInterface $transactionManager;

    public function run(string $statement, iterable $parameters = []): SummarizedResult
    {
        return $this->getConnection()->run($statement, $parameters);
    }

    public function runStatement(Statement $statement): SummarizedResult
    {
        return $this->getConnection()->runStatement($statement);
    }

    public function transactional(Closure $func)
    {
        $transactionManager = $this->getConnection();

        $transactionManager->beginTransaction();
        try {
            $res = $func();
            $transactionManager->commit();

            return $res;
        } catch (\Throwable $e) {
            $transactionManager->rollBack();

            throw $e;
        }
    }

    private function getConnection(): TransactionManagerInterface
    {
        if (!isset($this->transactionManager)) {
            /** @var ClientInterface $client */
            $client = $this->getDriver()->getClient();
            $this->transactionManager = new NestedTransactionWrapper(
                new BasicTransactionManager($client)
            );
        }

        return $this->transactionManager;
    }
}
