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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\model\persistence\smoothsql;

use common_Exception;
use \core_kernel_persistence_smoothsql_SmoothRdf;
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

        $persistence->exec($query, [
            22,
            'subjectUri',
            'predicateUri',
            'objectUri',
            '',
            'now',
            ''
        ])->willReturn(true);

        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getReadableModels()->willReturn([22]);
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

        $persistence->exec($query, [
            22,
            'subjectUri',
            'predicateUri',
            'objectUri',
            '',
            'now',
            'JohnDoe'
        ])->willReturn(true);

        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getReadableModels()->willReturn([22]);
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
        ])->willReturn(true);

        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());


        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());

        $this->assertTrue($rdf->remove($triple));
    }
}
