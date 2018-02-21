<?php

namespace oat\generis\scripts;

use oat\oatbox\extension\AbstractAction;

class EchoWord extends AbstractAction
{
    public function __invoke($params)
    {
        /** @var Printer $printer */
        $printer = $this->getServiceLocator()->get(Printer::SERVICE_ID);
        $printer->printWord();
        return \common_report_Report::createSuccess('Done');
    }

}