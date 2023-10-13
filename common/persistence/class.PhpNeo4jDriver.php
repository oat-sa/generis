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

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class common_persistence_PhpNeo4jDriver implements common_persistence_Driver
{
    private ClientInterface $client;

    public function connect($id, array $params): common_persistence_Persistence
    {
        $auth = Authenticate::basic($params['user'], $params['password']);

        $this->client = ClientBuilder::create()
            ->withDriver('bolt', sprintf('bolt://%s', $params['host']), $auth)
            ->withDefaultDriver('bolt')
            ->build();

        return new common_persistence_GraphPersistence($params, $this);
    }

    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }
}
