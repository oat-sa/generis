<?php

namespace oat\oatbox\TaskQueue;

use common_report_Report as Report;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\task\Task;

class TaskLog extends ConfigurableService implements TaskLogInterface
{
    use LoggerAwareTrait;

    /**
     * @var \common_persistence_SqlPersistence
     */
    protected $persistence;
    protected $tablePrefix = 'tq';

    /**
     * @return string
     */
    protected function getTableName()
    {
        return strtolower($this->tablePrefix .'_'. $this->getOption(self::CONFIG_CONTAINER_NAME));
    }

    /**
     * @return \common_persistence_Persistence|\common_persistence_SqlPersistence
     */
    protected function getPersistence()
    {
        if (is_null($this->persistence)) {
            $this->persistence = \common_persistence_Manager::getPersistence($this->getOption(self::CONFIG_PERSISTENCE));
        }

        return $this->persistence;
    }

    /**
     * Creates the new container.
     * @return void
     */
    public function createContainer()
    {
        /** @var \common_persistence_sql_pdo_mysql_SchemaManager $schemaManager */
        $schemaManager = $this->getPersistence()->getSchemaManager();

        /** @var \Doctrine\DBAL\Schema\MySqlSchemaManager $sm */
        $sm = $schemaManager->getSchemaManager();

        // if our table does not exist, let's create it
        if(false === $sm->tablesExist($this->getTableName())) {
            $fromSchema = $schemaManager->createSchema();
            $toSchema = clone $fromSchema;

            $table = $toSchema->createTable($this->getTableName());
            $table->addOption('engine', 'InnoDB');
            $table->addColumn('id', 'string', ["notnull" => true, "length" => 255]);
            $table->addColumn('task_name', 'string', ["notnull" => true, "length" => 255]);
            $table->addColumn('status', 'string', ["notnull" => true, "length" => 50]);
            $table->addColumn('owner', 'string', ["notnull" => false, "length" => 255, "default" => null]);
            $table->addColumn('report', 'text', ["notnull" => false, "default" => null]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['status'], 'IDX_status');

            $queries = $this->persistence->getPlatForm()->getMigrateSchemaSql($fromSchema, $toSchema);
            foreach ($queries as $query) {
                $this->persistence->exec($query);
            }
        }
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function add(Task $task)
    {
        $dateNow = $this->getPersistence()->getPlatForm()->getNowExpression();

        try {
            return (bool) $this->getPersistence()->insert($this->getTableName(), [
                'id'   => (string) $task->getId(),
                'task_name' => is_object($task->getInvocable()) ? get_class($task->getInvocable()) : (string) $task->getInvocable(),
                'status' => $task->getStatus() ?: Task::STATUS_CREATED,
                'owner' => (string) $task->getOwner(),
                'created_at' => $task->getCreationDate() ?: $dateNow,
                'updated_at' => $dateNow
            ]);
        } catch (\Exception $e) {
            $this->logError('Adding log for task '. $task->getId() .' failed with MSG: '. $e->getMessage());
        }

        return false;
    }

    /**
     * @param string $id
     * @param string $newStatus
     * @return int
     */
    public function setStatus($id, $newStatus)
    {
        try {
            $qb = $this->getQueryBuilder()
                ->update($this->getTableName())
                ->set('status', ':status_new')
                ->set('updated_at', ':updated_at')
                ->where('id = :id')
                ->setParameter('id', (string) $id)
                ->setParameter('status_new', $newStatus)
                ->setParameter('updated_at', $this->getPersistence()->getPlatForm()->getNowExpression());

            return (int) $qb->execute();
        } catch (\Exception $e) {
            $this->logError('Setting the status for task '. $id .' failed with MSG: '. $e->getMessage());
        }

        return 0;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getStatus($id)
    {
        try {
            $qb = $this->getQueryBuilder()
                ->select('status')
                ->from($this->getTableName())
                ->andWhere('id = :id')
                ->setParameter('id', $id);

            return $qb->execute()->fetchColumn();
        } catch (\Exception $e) {
            $this->logError('Getting status for task '. $id .' failed with MSG: '. $e->getMessage());
        }

        return '';
    }

    /**
     * @param string $id
     * @param Report $report
     * @return bool
     */
    public function setReport($id, Report $report)
    {
        try{
            $qb = $this->getQueryBuilder()
                ->update($this->getTableName())
                ->set('report', ':report')
                ->set('updated_at', ':updated_at')
                ->where('id = :id')
                ->setParameter('id', (string) $id)
                ->setParameter('report', json_encode($report))
                ->setParameter('updated_at', $this->getPersistence()->getPlatForm()->getNowExpression());

            return (bool) $qb->execute();
        } catch (\Exception $e) {
            $this->logError('Setting report for item '. $id .' failed with MSG: '. $e->getMessage());
        }

        return false;
    }

    /**
     * @param string $id
     * @return Report|null
     */
    public function getReport($id)
    {
        try{
            $qb = $this->getQueryBuilder()
                ->select('report')
                ->from($this->getTableName())
                ->where('id = :id')
                ->setParameter('id', (string) $id);

            if (($reportJson = $qb->execute()->fetchColumn())
                && ($reportData = json_decode($reportJson, true)) !== null
                && json_last_error() === JSON_ERROR_NONE
            ) {
                // if we have a valid JSON string and no JSON error, let's restore the report object
                return Report::jsonUnserialize($reportData);
            }
        } catch (\Exception $e) {
            $this->logError('Getting report for task '. $id .' failed with MSG: '. $e->getMessage());
        }

        return null;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->getPersistence()->getPlatform()->getQueryBuilder();
    }
}