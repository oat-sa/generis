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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfsInterface;

class core_kernel_persistence_starsql_StarRdfs implements RdfsInterface
{
    /**
     * @var core_kernel_persistence_starsql_StarModel
     */
    private $model;

    public function __construct(core_kernel_persistence_starsql_StarModel $model)
    {
        $this->model = $model;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getClassImplementation()
     */
    public function getClassImplementation()
    {
        return new \core_kernel_persistence_starsql_Class($this->model);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getResourceImplementation()
     */
    public function getResourceImplementation()
    {
        return new \core_kernel_persistence_starsql_Resource($this->model);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getPropertyImplementation()
     */
    public function getPropertyImplementation()
    {
        return new  \core_kernel_persistence_starsql_Property($this->model);
    }

    /**
     * @return Ontology
     */
    protected function getModel()
    {
        return $this->model;
    }
}
