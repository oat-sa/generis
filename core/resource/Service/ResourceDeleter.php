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

namespace oat\generis\model\resource\Service;

use Throwable;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdf;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\Contract\ResourceDeleterInterface;
use oat\generis\model\resource\Exception\ResourceDeletionException;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;

class ResourceDeleter implements ResourceDeleterInterface
{
    /** @var ResourceRepositoryInterface */
    private $resourceRepository;

    public function __construct(ResourceRepositoryInterface $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    public function delete(core_kernel_classes_Resource $resource): void
    {
        try {
            $context = new ResourceRepositoryContext([ResourceRepositoryContext::PARAM_RESOURCE => $resource]);
            $parentClass = $this->getParentClass($resource);

            if ($parentClass !== null) {
                $context->setParameter(ResourceRepositoryContext::PARAM_PARENT_CLASS, $parentClass);
            }

            $this->resourceRepository->delete($context);
        } catch (Throwable $exception) {
            throw new ResourceDeletionException(
                sprintf(
                    'Unable to delete resource "%s::%s" (%s).',
                    $resource->getLabel(),
                    $resource->getUri(),
                    $exception->getMessage()
                ),
                __('Unable to delete the selected resource')
            );
        }
    }

    private function getParentClass(core_kernel_classes_Resource $resource): ?core_kernel_classes_Class
    {
        $parentClassUri = $resource->getOnePropertyValue($resource->getProperty(OntologyRdf::RDF_TYPE));

        return $parentClassUri !== null
            ? $resource->getClass($parentClassUri)
            : null;
    }
}
