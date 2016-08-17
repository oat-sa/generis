<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\oatbox\service\config;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceInjector;

/**
 * Description of ServiceInjectorRegistry
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ServiceInjectorRegistry extends ConfigurableService {
    
    /**
     * set up a new ServiceInjector from its options
     * @return ServiceInjector
     */
    public function factory() {
        return new ServiceInjector($this->getOptions());
    }
    
    /**
     * merge new config with default config
     * @param array $overLoadOptions
     * @return $this
     */
    public function overLoad(array $overLoadOptions = []) {
        
        $this->setOptions(
                array_merge_recursive($this->getOptions() , $overLoadOptions)
                );
        return  $this;
    }
}
