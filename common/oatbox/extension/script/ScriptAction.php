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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\oatbox\extension\script;

use oat\oatbox\extension\AbstractAction;
use common_report_Report as Report;

/**
 * abstract base for extension scripts.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
abstract class ScriptAction extends AbstractAction
{
    private $options;
    
    public abstract function describeOptions();
    
    /**
     * Run Script.
     * 
     * Run the userland script.
     * 
     * @return \common_report_Report;
     */
    public abstract function run();
    
    public function __invoke($params)
    {
        // Build option container.
        try {
            $this->options = new OptionContainer(
                $this->describeOptions(), 
                $params
            );
        } catch (\Exception $e) {
            return new Report(
                Report::TYPE_ERROR,
                $e->getMessage()
            );
        }
        
        // Run the userland script.
        return $this->run();
    }
    
    protected function hasOption($optionName)
    {
        return $this->options->has($optionName);
    }
    
    protected function getOption($optionName)
    {
        return $this->options->get($optionName);
    }
}
