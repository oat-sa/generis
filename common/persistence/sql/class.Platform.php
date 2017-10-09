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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 
 *
 */
class common_persistence_sql_Platform{
    
    protected  $dbalPlatform;

    /** @var \Doctrine\DBAL\Connection */
    protected  $dbalConnection;

    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param $dbalConnection \Doctrine\DBAL\Connection
     */
    public function __construct($dbalConnection){
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
    public function limitStatement($statement, $limit, $offset = 0){
        return $this->dbalPlatform->modifyLimitQuery($statement, $limit, $offset);
    }
    /**
     *  Dbal Text type  returnedf stream in oracle this method handle others DBMS
     *  
     *  @param string $text
     *  @return string
     */
    public function getPhpTextValue($text){
    	return $text;
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
	public function getObjectTypeCondition(){
		return 'object ';
	}
    /**
     * 
     * @return string
     */
    public function getNullString(){
    	return "''";
    }
    
    /**
     * 
     * @param string $columnName
     * @return string
     */
    public function isNullCondition($columnName){
    	return $columnName . ' = ' .$this->getNullString(); 
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $parameter
     * @return string
     */
    public function quoteIdentifier($parameter){
        return $this->dbalPlatform->quoteIdentifier($parameter);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @return array
     */
    public function schemaToSql($schema){
        return $schema->toSql($this->dbalPlatform);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param \Doctrine\DBAL\Schema\Schema $fromSchema
     * @param \Doctrine\DBAL\Schema\Schema $toSchema
     * @return array
     */
    public function getMigrateSchemaSql($fromSchema,$toSchema){
        return $fromSchema->getMigrateToSql($toSchema,$this->dbalPlatform);     
    }
    
    /**
     * Return driver name mysql, postgresql, oracle, mssql
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function getName(){
        return $this->dbalPlatform->getName();
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    public function getNowExpression(){
        $datetime = new DateTime();
        $date = $datetime->format('Y-m-d H:i:s');
       // return $this->dbalPlatform->getNowExpression();
       return $date;
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $functionName
     * @return string
     */
    public function getSqlFunction($functionName){
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
     * Starts a transaction by suspending auto-commit mode.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->dbalConnection->beginTransaction();
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
     * @throws \Doctrine\DBAL\ConnectionException If the commit failed due to no active transaction or
     *                                            because the transaction was marked for rollback only.
     */
    public function commit()
    {
        $this->dbalConnection->commit();
    }
}
