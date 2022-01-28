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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\model\resource\Repository;

use BadMethodCallException;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\search\helper\SupportedOperatorHelper;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\resource\Context\PropertyRepositoryContext;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;

class PropertyRepository implements ResourceRepositoryInterface
{
    /** @var ComplexSearchService */
    private $complexSearch;

    public function __construct(ComplexSearchService $complexSearch)
    {
        $this->complexSearch = $complexSearch;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(ContextInterface $context): array
    {
        $queryBuilder = $this->complexSearch->query();

        $query = $this->complexSearch->searchType($queryBuilder, OntologyRdf::RDF_PROPERTY, true);
        $query->addCriterion(
            OntologyRdf::RDF_TYPE,
            SupportedOperatorHelper::EQUAL,
            OntologyRdf::RDF_PROPERTY
        );

        if ($context->hasParameter(PropertyRepositoryContext::PARAM_ALIASES)) {
            $query->addCriterion(
                GenerisRdf::PROPERTY_ALIAS,
                SupportedOperatorHelper::IN,
                $context->getParameter(PropertyRepositoryContext::PARAM_ALIASES, [])
            );
        }

        $queryBuilder->setCriteria($query);

        return iterator_to_array($this->complexSearch->getGateway()->search($queryBuilder));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ContextInterface $context): void
    {
        throw new BadMethodCallException(sprintf('Method %s not implemented.', __METHOD__));
    }
}
