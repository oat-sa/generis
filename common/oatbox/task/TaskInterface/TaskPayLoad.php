<?php
/**
 * Created by PhpStorm.
 * User: christophe
 * Date: 01/06/17
 * Time: 15:24
 */

namespace oat\oatbox\task\TaskInterface;


use oat\tao\model\datatable\DatatablePayload;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use oat\tao\model\datatable\DatatableRequest as DatatableRequestInterface;

interface TaskPayLoad extends DatatablePayload , ServiceLocatorAwareInterface
{

    public function __construct(TaskPersistenceInterface $persistence , $currentUserId = null , DatatableRequestInterface $request = null);

}