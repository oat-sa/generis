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

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;

class core_kernel_persistence_starsql_Property extends core_kernel_persistence_starsql_Resource implements
    core_kernel_persistence_PropertyInterface
{
    public static $instance = null;

    public function isLgDependent(core_kernel_classes_Resource $resource): bool
    {
        $lgDependentProperty = $this->getModel()->getProperty(GenerisRdf::PROPERTY_IS_LG_DEPENDENT);
        $lgDependentResource = $resource->getOnePropertyValue($lgDependentProperty);
        $lgDependent = !is_null($lgDependentResource)
            && $lgDependentResource instanceof \core_kernel_classes_Resource
            && $lgDependentResource->getUri() == GenerisRdf::GENERIS_TRUE;

        return (bool) $lgDependent;
    }

    public function isMultiple(core_kernel_classes_Resource $resource): bool
    {
        throw new core_kernel_persistence_ProhibitedFunctionException(
            "not implemented => The function (" . __METHOD__
            . ") is not available in this persistence implementation (" . __CLASS__ . ")"
        );
    }

    public function getRange(core_kernel_classes_Resource $resource): core_kernel_classes_Class
    {
        throw new core_kernel_persistence_ProhibitedFunctionException(
            "not implemented => The function (" . __METHOD__
            . ") is not available in this persistence implementation (" . __CLASS__ . ")"
        );
    }

    public function delete(core_kernel_classes_Resource $resource, $deleteReference = false): bool
    {
        $propertyNode = node()
            ->withProperties(['uri' => $resource->getUri()])
            ->withLabels(['Resource']);
        $query = query()
            ->match($propertyNode)
            ->detachDelete($propertyNode)
            ->build();

        $result = $this->getPersistence()->run($query);
        // @FIXME handle failure

        return true;
    }

    public function setRange(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class): ?bool
    {
        $rangeProp = new core_kernel_classes_Property(OntologyRdfs::RDFS_RANGE, __METHOD__);
        $returnValue = $this->setPropertyValue($resource, $rangeProp, $class->getUri());
        return $returnValue;
    }

    public function setDependsOnProperty(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property
    ): void {
        $dependsOnProperty = new core_kernel_classes_Property(
            GenerisRdf::PROPERTY_DEPENDS_ON_PROPERTY,
            __METHOD__
        );

        $this->setPropertyValue($resource, $dependsOnProperty, $property->getUri());
    }

    public function setMultiple(core_kernel_classes_Resource $resource, $isMultiple)
    {
        $multipleProperty = new core_kernel_classes_Property(GenerisRdf::PROPERTY_MULTIPLE);
        $value = ((bool)$isMultiple) ?  GenerisRdf::GENERIS_TRUE : GenerisRdf::GENERIS_FALSE ;
        $this->setPropertyValue($resource, $multipleProperty, $value);
    }

    public function setLgDependent(core_kernel_classes_Resource $resource, $isLgDependent)
    {
        $lgDependentProperty = new core_kernel_classes_Property(GenerisRdf::PROPERTY_IS_LG_DEPENDENT, __METHOD__);
        $value = ((bool)$isLgDependent) ?  GenerisRdf::GENERIS_TRUE : GenerisRdf::GENERIS_FALSE ;
        $this->setPropertyValue($resource, $lgDependentProperty, $value);
    }

    public static function singleton()
    {
        $returnValue = null;
        if (core_kernel_persistence_starsql_Property::$instance == null) {
            core_kernel_persistence_starsql_Property::$instance = new core_kernel_persistence_starsql_Property();
        }
        $returnValue = core_kernel_persistence_starsql_Property::$instance;
        return $returnValue;
    }
}
