<?php

namespace oat\oatbox\TaskQueue\MessageLogBroker;

use common_report_Report as Report;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\oatbox\TaskQueue\ActionTaskInterface;
use oat\oatbox\TaskQueue\MessageInterface;
use oat\oatbox\TaskQueue\Queue;

/**
 * Storing message logs in MySql.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class MySqlLogBroker implements MessageLogBrokerInterface
{
    const CONFIG_PERSISTENCE = 'persistence';

    /**
     * @var \common_persistence_SqlPersistence
     */
    protected $persistence;
    protected $tableName;

    /**
     * MySqlLogBroker constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if(!isset($config[self::CONFIG_PERSISTENCE])) {
            throw new \InvalidArgumentException("Persistence id needs to be set.");
        }

        if(!isset($config[self::CONFIG_CONTAINER_NAME])) {
            $config[self::CONFIG_CONTAINER_NAME] = 'message_log';
        }

        $this->persistence = \common_persistence_Manager::getPersistence($config[self::CONFIG_PERSISTENCE]);
        $this->tableName = strtolower(Queue::QUEUE_PREFIX .'_'. $config[self::CONFIG_CONTAINER_NAME]);
    }

    /**
     * Creates the mysql table if it does not exist.
     */
    public function createContainer()
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
            $table->addColumn('id', 'string', ["notnull" => true, "length" => 255]);
            $table->addColumn('task_fqcn', 'string', ["notnull" => true, "length" => 255]);
            $table->addColumn('status', 'string', ["notnull" => true, "length" => 50]);
            $table->addColumn('owner', 'string', ["notnull" => false, "length" => 255, "default" => null]);
            $table->addColumn('report', 'text', ["notnull" => false, "default" => null]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['task_fqcn', 'owner'], 'IDX_task_fqcn_owner');
            $table->addIndex(['status'], 'IDX_status');

            $queries = $this->persistence->getPlatForm()->getMigrateSchemaSql($fromSchema, $toSchema);
            foreach ($queries as $query) {
                $this->persistence->exec($query);
            }
        }
    }

    /**
     * @param MessageInterface $message
     * @param string           $status
     */
    public function add(MessageInterface $message, $status)
    {
        $this->persistence->insert($this->tableName, [
            'id'   => (string) $message->getId(),
            'task_fqcn' => $message instanceof ActionTaskInterface ? get_class($message->getAction()) : get_class($message),
            'status' => $status,
            'owner' => (string) $message->getOwner(),
            'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->persistence->getPlatForm()->getNowExpression()
        ]);
    }

    /**
     * @param string $messageId
     * @return bool|string
     */
    public function getStatus($messageId)
    {
        $qb = $this->getQueryBuilder()
            ->select('status')
            ->from($this->tableName)
            ->andWhere('id = :id')
            ->setParameter('id', $messageId);

        return $qb->execute()->fetchColumn();
    }

    /**
     * Updating the status.
     * .
     * @param string $messageId
     * @param string $newStatus
     * @param string $prevStatus
     * @return int count of touched records
     */
    public function updateStatus($messageId, $newStatus, $prevStatus = null)
    {
        $qb = $this->getQueryBuilder()
            ->update($this->tableName)
            ->set('status', ':status_new')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameter('id', $messageId)
            ->setParameter('status_new', $newStatus)
            ->setParameter('updated_at', $this->persistence->getPlatForm()->getNowExpression());

        if ($prevStatus) {
            $qb->andWhere('status = :status_prev')
                ->setParameter('status_prev', $prevStatus);
        }

        return $qb->execute();
    }

    /**
     * @param string $messageId
     * @param Report $report
     * @param null   $status
     * @return int
     */
    public function addReport($messageId, Report $report, $status = null)
    {
        $qb = $this->getQueryBuilder()
            ->update($this->tableName)
            ->set('report', ':report')
            ->set('status', ':status_new')
            ->set('updated_at', ':updated_at')
            ->andWhere('id = :id')
            ->setParameter('id', $messageId)
            ->setParameter('report', json_encode($report))
            ->setParameter('status_new', $status)
            ->setParameter('updated_at', $this->persistence->getPlatForm()->getNowExpression());

        return $qb->execute();
    }

    /**
     * @param string $messageId
     * @return Report|null
     */
    public function getReport($messageId)
    {
        $qb = $this->getQueryBuilder()
            ->select('report')
            ->from($this->tableName)
            ->andWhere('id = :id')
            ->setParameter('id', $messageId);

        if (($reportJson = $qb->execute()->fetchColumn())
            && ($reportData = json_decode($reportJson, true)) !== null
            && json_last_error() === JSON_ERROR_NONE
        ) {
            // if we have a valid JSON string and no JSON error, let's restore the report object
            return Report::jsonUnserialize($reportData);
        }

        return null;
    }

    /**
     * @param string $messageId
     * @return array
     */
    public function findById($messageId)
    {
        $qb = $this->getQueryBuilder()
            ->select('*')
            ->from($this->tableName)
            ->andWhere('id = :id')
            ->setParameter('id', $messageId);

        return $qb->execute()->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        /**@var \common_persistence_sql_pdo_mysql_Driver $driver */
        return $this->persistence->getPlatform()->getQueryBuilder();
    }
}