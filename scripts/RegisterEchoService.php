<?php

namespace oat\generis\scripts;

use oat\oatbox\extension\InstallAction;

class RegisterEchoService extends InstallAction
{
    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_Exception
     */
    public function __invoke($params)
    {
        $this->registerService(Printer::SERVICE_ID, new EchoService(array(
            EchoService::OPTION_WORD => 'Hello world'
        )));

        return \common_report_Report::createSuccess('Echo service successfully registered.');
    }

}