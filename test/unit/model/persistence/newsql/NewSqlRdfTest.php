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
 * Copyright (c) (original work) 2020 Open Assessment Technologies SA
 *
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\persistence\newsql;

use common_persistence_sql_Platform;
use common_persistence_SqlPersistence;
use core_kernel_classes_Triple;
use Doctrine\DBAL\ParameterType;
use oat\generis\model\kernel\persistence\newsql\NewSqlOntology;
use oat\generis\model\kernel\persistence\newsql\NewSqlRdf;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NewSqlRdfTest extends TestCase
{
    public function testAdd()
    {
        $query = 'INSERT INTO statements ( id, modelId, subject, predicate, object, l_language, epoch, author) VALUES '
            . '( ?, ? , ? , ? , ? , ? , ?, ?);';

        $triple = new core_kernel_classes_Triple();
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

        $persistence = $this->getPersistence();
        $persistence
            ->expects($this->once())
            ->method('exec')
            ->with(
                $query,
                $this->callback(function ($value) use ($expected) {
                    return array_slice($value, 1) === $expected && is_string($value[0]);
                }),
                $this->getExpectedTripleParameterTypes()
            )
            ->willReturn(true);

        $this->assertTrue($this->createRdfSubject($persistence)->add($triple));
    }

    public function testAddTripleCollection()
    {
        $table = 'statements';

        $triple1 = new core_kernel_classes_Triple();
        $triple1->modelid = 11;
        $triple1->subject = 'subjectUri1';
        $triple1->predicate = 'predicateUri1';
        $triple1->object = 'objectUri1';
        $triple1->author = '';

        $triple2 = new core_kernel_classes_Triple();
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

        $persistence = $this->getPersistence();
        $persistence
            ->expects($this->once())
            ->method('insertMultiple')
            ->with(
                $this->equalTo($table),
                $this->callback(function ($value) use ($expectedValue) {
                    return array_slice($value[0], 1) === $expectedValue[0] && is_string($value[0]['id'])
                        && array_slice($value[1], 1) === $expectedValue[1] && is_string($value[1]['id']);
                }),
                $this->equalTo($types)
            );

        $this->createRdfSubject($persistence)->addTripleCollection($triples);
    }

    private function getPersistence(): common_persistence_SqlPersistence|MockObject
    {
        $platform = $this->createMock(common_persistence_sql_Platform::class);
        $platform
            ->method('getNowExpression')
            ->willReturn('now');

        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $persistence
            ->method('getPlatForm')
            ->willReturn($platform);

        return $persistence;
    }

    private function createRdfSubject($persistence): NewSqlRdf
    {
        $model = $this->createMock(NewSqlOntology::class);
        $model
            ->method('getPersistence')
            ->willReturn($persistence);

        return new NewSqlRdf($model);
    }

    private function getExpectedTripleParameterTypes(): array
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
