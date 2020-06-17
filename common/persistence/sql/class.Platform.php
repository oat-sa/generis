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
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 *
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;

class common_persistence_sql_Platform
{
    
    const TRANSACTION_PLATFORM_DEFAULT = 0;
    
    const TRANSACTION_READ_UNCOMMITTED = Connection::TRANSACTION_READ_UNCOMMITTED;
    
    const TRANSACTION_READ_COMMITTED = Connection::TRANSACTION_READ_COMMITTED;
    
    const TRANSACTION_REPEATABLE_READ = Connection::TRANSACTION_REPEATABLE_READ;
    
    const TRANSACTION_SERIALIZABLE = Connection::TRANSACTION_SERIALIZABLE;
    
    protected $dbalPlatform;

    /** @var \Doctrine\DBAL\Connection */
    protected $dbalConnection;

    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param $dbalConnection \Doctrine\DBAL\Connection
     */
    public function __construct($dbalConnection)
    {
        $this->dbalPlatform = $dbalConnection->getDatabasePlatform();
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->dbalConnection->createQueryBuilder();
    }

    /**
     * Appends the correct LIMIT statement depending on the implementation of
     * wrapper. For instance, limiting results in SQL statements are different
     * mySQL and postgres.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string $statement The statement to limit
     * @param  int $limit Limit lower bound.
     * @param  int $offset Limit upper bound.
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0)
    {
        return $this->dbalPlatform->modifyLimitQuery($statement, $limit, $offset);
    }
    /**
     *  Dbal Text type  returnedf stream in oracle this method handle others DBMS
     *
     *  @param string $text
     *  @return string
     */
    public function getPhpTextValue($text)
    {
        return $text;
    }
    
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    public function getObjectTypeCondition()
    {
        return 'object ';
    }
    /**
     *
     * @return string
     */
    public function getNullString()
    {
        return "''";
    }
    
    /**
     *
     * @param string $columnName
     * @return string
     */
    public function isNullCondition($columnName)
    {
        return $columnName . ' = ' . $this->getNullString();
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $parameter
     * @return string
     */
    public function quoteIdentifier($parameter)
    {
        return $this->dbalPlatform->quoteIdentifier($parameter);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param Schema $schema
     * @return array
     */
    public function schemaToSql($schema)
    {
        return $schema->toSql($this->dbalPlatform);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param Schema $schema
     * @return array
     */
    public function toDropSql($schema)
    {
        return $schema->toDropSql($this->dbalPlatform);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param Schema $fromSchema
     * @param Schema $toSchema
     * @return array
     */
    public function getMigrateSchemaSql($fromSchema, $toSchema)
    {
        return $fromSchema->getMigrateToSql($toSchema, $this->dbalPlatform);
    }

    /**
     * Migrate Schema
     *
     * Migrate from $fromSchema to $toSchema. SQL queries to go from $fromSchema
     * to $toSchema will be automatically executed.
     *
     * @param Schema $fromSchema
     * @param Schema $toSchema
     * @return int
     * @throws DBALException
     */
    public function migrateSchema(Schema $fromSchema, Schema $toSchema): int
    {
        $queryCount = 0;

        $queries = $this->getMigrateSchemaSql($fromSchema, $toSchema);
        foreach ($queries as $query) {
            $this->dbalConnection->exec($query);
            $queryCount++;
        }

        return $queryCount;
    }
    
    /**
     * Return driver name mysql, postgresql, oracle, mssql
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function getName()
    {
        return $this->dbalPlatform->getName();
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    public function getNowExpression()
    {
        // We can't use $this->dbalPlatform->getNowExpression() because sqlite,
        // at least used for the tests, returns `datetime('now')` which can
        // not be parsed as a regular date.
        // We instead generate a date with php and the format it with
        // $this->dbalPlatform->getDateTimeTzFormatString(), to still have the
        // correct format to be inserted in db.

        $datetime = new \DateTime('now', new \DateTimeZone('UTC'));
        return $datetime->format($this->getDateTimeFormatString());
    }

    /**
     * Returns platform specific date formatting with timezone to store datetime field.
     * @return string
     */
    public function getDateTimeFormatString()
    {
        return $this->dbalPlatform->getDateTimeFormatString();
    }

    /**
     * Returns platform specific date formatting with timezone to store datetime field.
     * @return string
     */
    public function getDateTimeTzFormatString()
    {
        return $this->dbalPlatform->getDateTimeTzFormatString();
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $functionName
     * @return string
     */
    public function getSqlFunction($functionName)
    {
        return "SELECT " . $functionName . '(?)';
    }

    /**
     * Returns the SQL snippet to append to any SELECT statement which obtains an exclusive lock on the rows.
     *
     * The semantics of this lock mode should equal the SELECT .. FOR UPDATE of the ANSI SQL standard.
     *
     * @see https://dev.mysql.com/doc/refman/5.7/en/innodb-locking-reads.html
     * @see https://www.postgresql.org/docs/9.0/static/sql-select.html#SQL-FOR-UPDATE-SHARE
     * @return string
     */
    public function getWriteLockSQL()
    {
        return $this->dbalPlatform->getWriteLockSQL();
    }

    /**
     * Returns the SQL snippet to append to any SELECT statement which locks rows in shared read lock.
     *
     * This defaults to the ANSI SQL "FOR UPDATE", which is an exclusive lock (Write). Some database
     * vendors allow to lighten this constraint up to be a real read lock.
     *
     * @return string
     */
    public function getReadLockSQL()
    {
        return $this->dbalPlatform->getReadLockSQL();
    }

    /**
     * Starts a transaction by suspending auto-commit mode.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->dbalConnection->beginTransaction();
    }
    
    /**
     * Sets the transaction isolation level for the current connection.
     *
     * Transaction levels are:
     *
     * * common_persistence_sql_Platform::TRANSACTION_PLATFORM_DEFAULT
     * * common_persistence_sql_Platform::TRANSACTION_READ_UNCOMMITTED
     * * common_persistence_sql_Platform::TRANSACTION_READ_COMMITTED
     * * common_persistence_sql_Platform::TRANSACTION_REPEATABLE_READ
     * * common_persistence_sql_Platform::TRANSACTION_SERIALIZABLE
     *
     * IT IS EXTREMELY IMPORTANT than after calling commit() or rollback(),
     * or in error handly, the developer sets back the initial transaction
     * level that was in force prior the call to beginTransaction().
     *
     * @param integer $level The level to set.
     *
     * @return integer
     */
    public function setTransactionIsolation($level)
    {
        if ($level === self::TRANSACTION_PLATFORM_DEFAULT) {
            $level = $this->dbalPlatform->getDefaultTransactionIsolationLevel();
        }
        
        $this->dbalConnection->setTransactionIsolation($level);
    }
    
    /**
     * Gets the currently active transaction isolation level for the current sesson.
     *
     * @return integer The current transaction isolation level for the current session.
     */
    public function getTransactionIsolation()
    {
        return $this->dbalConnection->getTransactionIsolation();
    }
    
    /**
     * Checks whether or not a transaction is currently active.
     *
     * @return boolean true if a transaction is currently active for the current session, otherwise false.
     */
    public function isTransactionActive()
    {
        return $this->dbalConnection->isTransactionActive();
    }

    /**
     * Cancels any database changes done during the current transaction.
     *
     * @throws \Doctrine\DBAL\ConnectionException If the rollback operation failed.
     */
    public function rollBack()
    {
        $this->dbalConnection->rollBack();
    }

    /**
     * Commits the current transaction.
     *
     * @return void
     * @throws \Doctrine\DBAL\ConnectionException If the commit failed due to no active transaction or because the transaction was marked for rollback only.
     * @throws DBALException
     * @throws common_persistence_sql_SerializationException In case of SerializationFailure (SQLSTATE 40001).
     */
    public function commit()
    {
        try {
            $this->dbalConnection->commit();
        } catch (DBALException $e) {
            // Surprisingly, DBAL's commit throws a PDOExeption in case
            // of serialization issue (not documented).
            if (($code = $e->getCode()) == '40001') {
                // Serialization failure (SQLSTATE 40001 for at least mysql, pgsql, sqlsrv).
                throw new common_persistence_sql_SerializationException(
                    "SQL Transaction Serialization Failure. See previous exception(s) for more information.",
                    intval($code),
                    $e
                );
            } else {
                // Another kind of error. Re-throw!
                throw $e;
            }
        }
    }
    
    public function getTruncateTableSql($tableName)
    {
        return $this->dbalPlatform->getTruncateTableSql($tableName);
    }
}
