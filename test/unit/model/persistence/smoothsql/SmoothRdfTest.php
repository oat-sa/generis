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
 * Copyright (c) (original work) 2015-2020 Open Assessment Technologies SA
 *
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\persistence\smoothsql;

use common_Exception;
use common_persistence_sql_Platform;
use common_persistence_SqlPersistence;
use core_kernel_classes_Triple;
use core_kernel_persistence_smoothsql_SmoothModel;
use core_kernel_persistence_smoothsql_SmoothRdf;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\TestCase;

class SmoothRdfTest extends TestCase
{
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet(): void
    {
        $this->expectException(common_Exception::class);
        $this->expectExceptionMessage('Not implemented');

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($this->createMock(common_persistence_SqlPersistence::class));

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);
        $rdf->get(null, null);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSearch(): void
    {
        $this->expectException(common_Exception::class);
        $this->expectExceptionMessage('Not implemented');

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($this->createMock(common_persistence_SqlPersistence::class));

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);
        $rdf->search(null, null);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAdd(): void
    {
        $platform = $this->createMock(common_persistence_sql_Platform::class);
        $platform
            ->method('getNowExpression')
            ->willReturn('now');

        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $persistence
            ->method('getPlatForm')
            ->willReturn($platform);

        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language, epoch, author) VALUES "
            . "( ? , ? , ? , ? , ? , ?, ?);";

        $triple = new core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';

        $persistence
            ->expects($this->once())
            ->method('exec')
            ->with(
                $query,
                [
                    22,
                    'subjectUri',
                    'predicateUri',
                    'objectUri',
                    '',
                    'now',
                    ''
                ],
                $this->getExpectedTripleParameterTypes()
            )
            ->willReturn(true);

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($persistence);

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);

        $this->assertTrue($rdf->add($triple));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAddWithAuthor(): void
    {
        $platform = $this->createMock(common_persistence_sql_Platform::class);
        $platform
            ->method('getNowExpression')
            ->willReturn('now');

        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $persistence
            ->method('getPlatForm')
            ->willReturn($platform);

        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language, epoch, author) VALUES "
            . "( ? , ? , ? , ? , ? , ?, ?);";

        $triple = new core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        $triple->author = 'JohnDoe';

        $persistence
            ->expects($this->once())
            ->method('exec')
            ->with(
                $query,
                [
                    22,
                    'subjectUri',
                    'predicateUri',
                    'objectUri',
                    '',
                    'now',
                    'JohnDoe'
                ],
                $this->getExpectedTripleParameterTypes()
            )
            ->willReturn(true);

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($persistence);

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);

        $this->assertTrue($rdf->add($triple));
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove(): void
    {
        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";

        $triple = new core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';

        $persistence
            ->expects($this->once())
            ->method('exec')
            ->with(
                $query,
                [
                    'subjectUri',
                    'predicateUri',
                    'objectUri',
                    ''
                ]
            )
            ->willReturn(true);

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($persistence);

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);

        $this->assertTrue($rdf->remove($triple));
    }

    public function testAddTripleCollection(): void
    {
        $platform = $this->createMock(common_persistence_sql_Platform::class);
        $platform
            ->method('getNowExpression')
            ->willReturn('now');

        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $persistence
            ->method('getPlatForm')
            ->willReturn($platform);

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

        $persistence
            ->expects($this->once())
            ->method('insertMultiple')
            ->with(
                $this->equalTo($table),
                $this->equalTo($expectedValue),
                $this->equalTo($types)
            );

        $model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $model
            ->method('getPersistence')
            ->willReturn($persistence);

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model);

        $rdf->addTripleCollection($triples);
    }

    protected function getExpectedTripleParameterTypes(): array
    {
        return [
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
