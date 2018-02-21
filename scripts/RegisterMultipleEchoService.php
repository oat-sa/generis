<?php

namespace oat\generis\scripts;

use oat\oatbox\extension\InstallAction;

class RegisterMultipleEchoService extends InstallAction
{
    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_Exception
     */
    public function __invoke($params)
    {
        $oldService = $this->getServiceLocator()->get(Printer::SERVICE_ID);

        $this->registerService(Printer::SERVICE_ID, new MultipleEchoService(array(
            MultipleEchoService::OPTION_INNER => $oldService,
            MultipleEchoService::OPTION_OCCURRENCE => 10,
        )));

        return \common_report_Report::createSuccess('Multiple Echo service successfully registered.');
    }

}