<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use Doctrine\DBAL\Query\QueryBuilder;
use oat\oatbox\TaskQueue\MessageInterface;

/**
 * Storing messages/tasks in DB.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class DbalBroker extends AbstractMessageBroker
{
    const CONFIG_PERSISTENCE = 'persistence';

    /**
     * @var \common_persistence_SqlPersistence
     */
    protected $persistence;
    protected $tableName;

    /**
     * DbalBroker constructor.
     *
     * @param string $queueName
     * @param array  $config
     */
    public function __construct($queueName, array $config)
    {
        parent::__construct($queueName, $config);

        if(!isset($config[self::CONFIG_PERSISTENCE])) {
            throw new \InvalidArgumentException("Persistence id needs to be set.");
        }

        $this->persistence = \common_persistence_Manager::getPersistence($config[self::CONFIG_PERSISTENCE]);
        $this->tableName = strtolower($this->getNameWithPrefix());
    }

    /**
     * Create queue table if it does not exist
     */
    public function createQueue()
    {
        /** @var \common_persistence_sql_pdo_mysql_SchemaManager $schemaManager */
        $schemaManager = $this->persistence->getSchemaManager();

        /** @var \Doctrine\DBAL\Schema\MySqlSchemaManager $sm */
        $sm = $schemaManager->getSchemaManager();

        // if our table does not exist, let's create it
        if(false === $sm->tablesExist([$this->tableName])) {
            $fromSchema = $schemaManager->createSchema();
            $toSchema = clone $fromSchema;

            $table = $toSchema->createTable($this->tableName);
            $table->addOption('engine', 'InnoDB');
            $table->addColumn('id', 'integer', ["autoincrement" => true, "notnull" => true, "unsigned" => true]);
            $table->addColumn('message', 'text', ["notnull" => true]);
            $table->addColumn('visible', 'boolean', ["default" => true]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['created_at', 'visible'], 'IDX_created_at_visible');

            $queries = $this->persistence->getPlatForm()->getMigrateSchemaSql($fromSchema, $toSchema);
            foreach ($queries as $query) {
                $this->persistence->exec($query);
            }
        }
    }

    /**
     * Insert a new message into the queue table.
     *
     * @param MessageInterface $message
     * @return bool
     */
    public function pushMessage(MessageInterface $message)
    {
        return (bool) $this->persistence->insert($this->tableName, [
            'message' => json_encode($message),
            'created_at' => $this->persistence->getPlatForm()->getNowExpression()
        ]);
    }

    /**
     * Does the DBAL specific pop mechanism.
     */
    protected function doPop()
    {
        $this->persistence->getPlatform()->beginTransaction();

        $logContext = [
            'Queue' => $this->getNameWithPrefix()
        ];

        try {
            $qb = $this->getQueryBuilder()
                ->select('id, message')
                ->from($this->tableName)
                ->andWhere('visible = :visible')
                ->orderBy('created_at')
                ->setMaxResults($this->getMessagesToReceive());

            /**
             * SELECT ... FOR UPDATE is used for locking
             *
             * @see https://dev.mysql.com/doc/refman/5.6/en/innodb-locking-reads.html
             */
            $sql = $qb->getSQL() .' '. $this->persistence->getPlatForm()->getWriteLockSQL();

            if ($dbResult = $this->persistence->query($sql, ['visible' => true])->fetchAll(\PDO::FETCH_ASSOC)) {

                // set the received messages to invisible for other workers
                $qb = $this->getQueryBuilder()
                    ->update($this->tableName)
                    ->set('visible', ':visible')
                    ->where('id IN ('. implode(',', array_column($dbResult, 'id')) .')')
                    ->setParameter('visible', false, \PDO::PARAM_BOOL);

                $qb->execute();

                foreach ($dbResult as $row) {
                    if ($messageObject = $this->denormalizeMessage($row['message'], $row['id'], $logContext)) {
                        $messageObject->setMetadata('DbalMessageId', $row['id']);
                        $this->pushPreFetchedMessage($messageObject);
                    }
                }
            } else {
                $this->logDebug('No messages in queue.', $logContext);
            }

            $this->persistence->getPlatform()->commit();
        } catch (\Exception $e) {
            $this->persistence->getPlatform()->rollBack();
            $this->logError('Popping messages failed with MSG: '. $e->getMessage(), $logContext);
        }
    }

    /**
     * Delete the message after being processed by the worker.
     *
     * @param MessageInterface $message
     */
    public function acknowledgeMessage(MessageInterface $message)
    {
        $this->deleteMessage($message->getMetadata('DbalMessageId'), [
            'InternalMessageId' => $message->getId(),
            'DbalMessageId' => $message->getMetadata('DbalMessageId')
        ]);
    }

    /**
     * @param string $id
     * @param array  $logContext
     * @return int
     */
    protected function deleteMessage($id, array $logContext = [])
    {
        try {
            $this->getQueryBuilder()
                ->delete($this->tableName)
                ->where('id = :id')
                ->andWhere('visible = :visible')
                ->setParameter('id', (int) $id)
                ->setParameter('visible', false, \PDO::PARAM_BOOL)
                ->execute();
        } catch (\Exception $e) {
            $this->logError('Deleting message failed with MSG: '. $e->getMessage(), $logContext);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        try {
            $qb = $this->getQueryBuilder()
                ->select('COUNT(id)')
                ->from($this->tableName)
                ->andWhere('visible = :visible')
                ->setParameter('visible', true, \PDO::PARAM_BOOL);

            return (int) $qb->execute()->fetchColumn();
        } catch (\Exception $e) {
            $this->logError('Counting messages failed with MSG: '. $e->getMessage());
        }

        return 0;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->persistence->getPlatform()->getQueryBuilder();
    }
}