<?php
/**
 * Created by PhpStorm.
 * User: christophe
 * Date: 02/06/17
 * Time: 09:40
 */

namespace oat\oatbox\task\implementation;


use oat\oatbox\task\Task;
use oat\oatbox\task\TaskInterface\TaskPayLoad;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use oat\tao\model\datatable\DatatableRequest as DatatableRequestInterface;
use oat\tao\model\datatable\implementation\DatatableRequest;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\TaskLog\DataTablePayload instead.
 */
class TaskQueuePayload implements TaskPayLoad
{

    use ServiceLocatorAwareTrait;

    /**
     * @var DatatableRequest
     */
    protected $request;

    /**
     * @var TaskPersistenceInterface
     */
    protected $persistence;

    protected $currentUserId;

    public function getPayload()
    {

        $params = $this->request->getFilters();

        if(!empty($this->currentUserId)) {
            $params['owner'] = $this->currentUserId;
        }

        $page = $this->request->getPage();
        $rows = $this->request->getRows();

        $sortBy    = $this->request->getSortBy();
        $sortOrder = $this->request->getSortOrder();

        $iterator = $this->persistence->search($params ,  $rows , $page , $sortBy , $sortOrder );

        $taskList = [];

        /**
         * @var $taskData Task
         */
        foreach ($iterator as $taskData) {
            $taskList[] =
                [
                    "id"           => $taskData->getId(),
                    "label"        => $taskData->getLabel(),
                    "creationDate" => strtotime($taskData->getCreationDate()),
                    "status"       => $taskData->getStatus(),
                    "report"       => $taskData->getReport(),
                ];
        }
        $countTotal = $this->count();
        $rows = $this->request->getRows();
        $data = [
            'rows'    => $rows,
            'page'    => $page,
            'amount'  => count($taskList),
            'total'   => ceil($countTotal/$rows),
            'data'    => $taskList,
        ];

        return $data;

    }

    public function count() {
        $params = $this->request->getFilters();

        if(!empty($this->currentUserId)) {
            $params['owner'] = $this->currentUserId;
        }

        return $this->persistence->count($params);
    }

    public function __construct(TaskPersistenceInterface $persistence, $currentUserId = null, DatatableRequestInterface $request = null)
    {
        $this->persistence = $persistence;
        $this->currentUserId = $currentUserId;

        if ($request === null) {
            $request = DatatableRequest::fromGlobals();
        }

        $this->request = $request;
    }

    public function jsonSerialize()
    {
        return $this->getPayload();
    }


}