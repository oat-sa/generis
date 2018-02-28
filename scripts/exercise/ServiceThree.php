<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 21.02.18
 * Time: 11:48
 */

namespace oat\generis\scripts\exercise;


class ServiceThree extends ConfigurableServiceWrapper implements ExerciseServiceInterface
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->parentService->getLabel();
    }

    /**
     * @return string
     */
    public function getParameterOfThird()
    {
        return $this->getOption('config')['whatever'];
    }

    public function setDescription($description)
    {
        $this->setOption('ss', $description);
        $this->parentService->setDescription($description);
    }
}
