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
 * Copyright (c) 2017 Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\service;
/**
 * Class ConfigurationService
 *
 * Wrapper of array configuration to accept only ConfigurableService as config
 *
 * @package oat\oatbox\service
 */
class ConfigurationService extends ConfigurableService
{
    const OPTION_CONFIG = 'config';

    /**
     * @var string Documentation header
     */
    protected $header;

    /**
     * Return the config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->hasOption(self::OPTION_CONFIG) ? $this->getOption(self::OPTION_CONFIG) : [];
    }

    /**
     * Return the documentation header

     * @param $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * Set the documentation header uses into config file
     *
     * @return string
     */
    public function getHeader()
    {
         if (is_null($this->header)) {
             return $this->getDefaultHeader();
         } else {
             return $this->header;
         }
    }
 
     /**
      * Get the default documentation header
      *
      * @return string
      */
     protected function getDefaultHeader()
     {
        return '<?php'.PHP_EOL
            .'/**'.PHP_EOL
            .' * Default config header created during install'.PHP_EOL
            .' */'.PHP_EOL;
     }
}