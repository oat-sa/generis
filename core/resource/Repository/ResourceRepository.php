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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\model\resource\Repository;

use RuntimeException;
use BadMethodCallException;
use InvalidArgumentException;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\generis\model\data\Ontology;
use core_kernel_persistence_ResourceInterface;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\data\event\ResourceDeleted;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;

class ResourceRepository implements ResourceRepositoryInterface
{
    /** @var Ontology */
    private $ontology;

    /** @var EventManager */
    private $eventManager;

    public function __construct(Ontology $ontology, EventManager $eventManager)
    {
        $this->ontology = $ontology;
        $this->eventManager = $eventManager;
    }

    public function findBy(ContextInterface $context): array
    {
        throw new BadMethodCallException(sprintf('Method %s not implemented.', __METHOD__));
    }

    public function delete(ContextInterface $context): void
    {
        /** @var core_kernel_classes_Resource|null $resource */
        $resource = $context->getParameter(ResourceRepositoryContext::PARAM_RESOURCE);

        if ($resource === null) {
            throw new InvalidArgumentException('Resource was not provided for deletion.');
        }

        $deleteReference = $context->getParameter(
            ResourceRepositoryContext::PARAM_DELETE_REFERENCE,
            false
        );

        if (!$this->getImplementation()->delete($resource, $deleteReference)) {
            throw new RuntimeException(
                sprintf(
                    'Resource "%s" ("%s") was not deleted.',
                    $resource->getLabel(),
                    $resource->getUri()
                )
            );
        }

        /** @var core_kernel_classes_Class|null $selectedClass */
        $selectedClass = $context->getParameter(ResourceRepositoryContext::PARAM_SELECTED_CLASS);
        /** @var core_kernel_classes_Class|null $selectedClass */
        $parentClass = $context->getParameter(ResourceRepositoryContext::PARAM_PARENT_CLASS);

        $resourceDeletedEvent = (new ResourceDeleted($resource->getUri()))
            ->setSelectedClass($selectedClass)
            ->setParentClass($parentClass);
        $this->eventManager->trigger($resourceDeletedEvent);
    }

    private function getImplementation(): core_kernel_persistence_ResourceInterface
    {
        return $this->ontology->getRdfsInterface()->getResourceImplementation();
    }
}
