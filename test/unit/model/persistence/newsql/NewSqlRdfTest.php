<?php

declare(strict_types=1);

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
 * Copyright (c) (original work) 2020 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\model\persistence\newsql;

use Doctrine\DBAL\ParameterType;
use oat\generis\model\kernel\persistence\newsql\NewSqlOntology;
use oat\generis\model\kernel\persistence\newsql\NewSqlRdf;
use Prophecy\Argument;
use oat\generis\test\TestCase;

class NewSqlRdfTest extends TestCase
{
    public function testAdd()
    {
        $query = 'INSERT INTO statements ( id, modelId, subject, predicate, object, l_language, epoch, author) VALUES ( ?, ? , ? , ? , ? , ? , ?, ?);';

        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';

        $expected = [
            22,
            'subjectUri',
            'predicateUri',
            'objectUri',
            '',
            'now',
            ''
        ];

        $persistence = $this->getPersistenceProphecy();
        $persistence->exec(
            $query,
            Argument::that(function($value) use ($expected) {
               return array_slice($value, 1) == $expected && is_string($value[0]);

            }),
            $this->getExpectedTripleParameterTypes()
        )->shouldBeCalled()->willReturn(true);

        $this->assertTrue($this->createRdfSubject($persistence->reveal())->add($triple));
    }

    public function testAddTripleCollection()
    {
        $table = 'statements';

        $triple1 = new \core_kernel_classes_Triple();
        $triple1->modelid = 11;
        $triple1->subject = 'subjectUri1';
        $triple1->predicate = 'predicateUri1';
        $triple1->object = 'objectUri1';
        $triple1->author = '';

        $triple2 = new \core_kernel_classes_Triple();
        $triple2->modelid = 22;
        $triple2->subject = 'subjectUri2';
        $triple2->predicate = 'predicateUri2';
        $triple2->object = 'objectUri2';
        $triple2->author = 'JohnDoe2';

        $triples = [$triple1, $triple2];

        $types = $this->getExpectedTripleParameterTypes();
        array_push($types, ...$types);

        $expectedValue = [
            [
                'modelid' => 11,
                'subject' => 'subjectUri1',
                'predicate' => 'predicateUri1',
                'object' => 'objectUri1',
                'l_language' => '',
                'author' => '',
                'epoch' => 'now',
            ],
            [
                'modelid' => 22,
                'subject' => 'subjectUri2',
                'predicate' => 'predicateUri2',
                'object' => 'objectUri2',
                'l_language' => '',
                'author' => 'JohnDoe2',
                'epoch' => 'now',
            ]
        ];

        $persistence = $this->getPersistenceProphecy();
        $persistence->insertMultiple(
            Argument::exact($table),
            Argument::that(function($value) use ($expectedValue) {
                return array_slice($value[0], 1) == $expectedValue[0] && is_string($value[0]['id'])
                    && array_slice($value[1], 1) == $expectedValue[1] && is_string($value[1]['id']);
            }),
            Argument::exact($types)
        )->shouldBeCalled();

        $this->createRdfSubject($persistence->reveal())->addTripleCollection($triples);
    }

    protected function getPersistenceProphecy()
    {
        $platform = $this->prophesize(\common_persistence_sql_Platform::class);
        $platform->getNowExpression()->willReturn('now');

        $persistence = $this->prophesize(\common_persistence_SqlPersistence::class);
        $persistence->getPlatForm()->willReturn($platform->reveal());

        return $persistence;
    }

    protected function createRdfSubject($persistence)
    {
        $model = $this->prophesize(NewSqlOntology::class);
        $model->getPersistence()->willReturn($persistence);

        return new NewSqlRdf($model->reveal());;
    }

    protected function getExpectedTripleParameterTypes()
    {
        return [
            ParameterType::STRING,
            ParameterType::INTEGER,
            ParameterType::STRING,
            ParameterType::STRING,
            ParameterType::STRING,
            ParameterType::STRING,
            ParameterType::STRING,
            ParameterType::STRING,
        ];
    }
}
