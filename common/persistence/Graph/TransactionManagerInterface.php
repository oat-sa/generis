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

interface TransactionManagerInterface
{
    /**
     * @return void
     *
     * @throws GraphTransactionException when starting transaction failed.
     */
    public function beginTransaction(): void;

    /**
     * @return void
     *
     * @throws GraphTransactionException when transaction commit failed.
     */
    public function commit(): void;

    /**
     * @return void
     *
     * @throws GraphTransactionException when transaction was not rolled back.
     */
    public function rollback(): void;

    /**
     * @param string $statement
     * @param iterable $parameters
     *
     * @return SummarizedResult
     *
     * @throws GraphTransactionException
     */
    public function run(string $statement, iterable $parameters = []): SummarizedResult;

    /**
     * @param Statement $statement
     *
     * @return SummarizedResult
     *
     * @throws GraphTransactionException
     */
    public function runStatement(Statement $statement): SummarizedResult;
}
