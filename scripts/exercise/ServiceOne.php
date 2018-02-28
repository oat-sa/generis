<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 21.02.18
 * Time: 11:01
 */

namespace oat\generis\scripts\exercise;

use oat\oatbox\service\ConfigurableService;

class ServiceOne extends ConfigurableService implements ExerciseServiceInterface
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getOption(ExerciseServiceInterface::LABEL_FIELD_NAME);
    }

    public function setDescription($description)
    {
        echo "Set <description> $description\n";
        $this->setOption('description', $description);
        echo "Set <description> " . $this->getOption('description') . PHP_EOL;
    }
}