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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\action;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Help implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string Extension id
     */
    private $extId;

    /**
     * Help constructor.
     * @param string $extId
     */
    public function __construct($extId = null)
    {
        $this->extId = $extId;
    }

    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_exception_Error
     */
    public function __invoke($params)
    {
        $actionResolver = $this->getServiceLocator()->get(ActionService::SERVICE_ID);
        $report = new \common_report_Report(\common_report_Report::TYPE_INFO, __('Available Actions:'));
        foreach ($actionResolver->getAvailableActions($this->extId) as $actionClass) {
            $actionDescription = "-  " . $actionClass . " - " . $this->getActionDescription($actionClass);
            $report->add(new \common_report_Report(\common_report_Report::TYPE_INFO, $actionDescription));
        }
        return $report;
    }

    /**
     * Get phpdoc provided for action class
     * @param $actionName
     * @return string
     */
    protected function getActionDescription($actionName)
    {
        $action = new $actionName;

        if ($action instanceof DescribedAction) {
            $result = $action->getDescription();
        } else {
            $rc = new \ReflectionClass($actionName);
            $doccomment = $rc->getDocComment();
            $doccomment = trim(substr($doccomment, 3, -2));
            $result = preg_replace('/^\s*\*\s*/mi', '', $doccomment);
        }
        return $result;
    }
}