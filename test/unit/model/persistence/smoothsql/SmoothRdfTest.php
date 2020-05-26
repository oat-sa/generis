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
 * Copyright (c) (original work) 2015-2020 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\model\persistence\smoothsql;

use common_Exception;
use \core_kernel_persistence_smoothsql_SmoothRdf;
use Doctrine\DBAL\ParameterType;
use Prophecy\Argument;
use Prophecy\Prophet;
use oat\generis\test\TestCase;

class SmoothRdfTest extends TestCase
{
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet()
    {
        $this->expectException(common_Exception::class);
        $this->expectExceptionMessage('Not implemented');
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');

        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        $rdf->get(null, null);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSearch()
    {
        $this->expectException(common_Exception::class);
        $this->expectExceptionMessage('Not implemented');
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');

        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        $rdf->search(null, null);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAdd()
    {
        $platform = $this->prophesize('\common_persistence_sql_Platform');
        $platform->getNowExpression()->willReturn('now');

        $persistence = $this->prophesize('\common_persistence_SqlPersistence');
        $persistence->getPlatForm()->willReturn($platform->reveal());
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language, epoch, author) VALUES ( ? , ? , ? , ? , ? , ?, ?);";

        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';

        $persistence->exec(
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
        )->shouldBeCalled()->willReturn(true);

        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());

        $this->assertTrue($rdf->add($triple));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAddWithAuthor()
    {
        $platform = $this->prophesize('\common_persistence_sql_Platform');
        $platform->getNowExpression()->willReturn('now');

        $persistence = $this->prophesize('\common_persistence_SqlPersistence');
        $persistence->getPlatForm()->willReturn($platform->reveal());
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language, epoch, author) VALUES ( ? , ? , ? , ? , ? , ?, ?);";

        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        $triple->author = 'JohnDoe';

        $persistence->exec(
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
        )->shouldBeCalled()->willReturn(true);

        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());

        $this->assertTrue($rdf->add($triple));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove()
    {
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";

        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';

        $persistence->exec($query, [
            'subjectUri',
            'predicateUri',
            'objectUri',
            ''
        ])->shouldBeCalled()->willReturn(true);

        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());


        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());

        $this->assertTrue($rdf->remove($triple));
    }

    public function testAddTripleCollection()
    {
        $platform = $this->prophesize('\common_persistence_sql_Platform');
        $platform->getNowExpression()->willReturn('now');

        $persistence = $this->prophesize('\common_persistence_SqlPersistence');
        $persistence->getPlatForm()->willReturn($platform->reveal());

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

        $persistence->insertMultiple(
            Argument::exact($table),
            Argument::exact($expectedValue),
            Argument::exact($types)
        )->shouldBeCalled();

        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());

        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());

        $rdf->addTripleCollection($triples);
    }

    protected function getExpectedTripleParameterTypes()
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
