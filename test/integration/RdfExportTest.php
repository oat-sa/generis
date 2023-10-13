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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use oat\generis\model\data\ModelManager;
use oat\generis\test\GenerisPhpUnitTestRunner;

class RdfExportTest extends GenerisPhpUnitTestRunner
{
    public function testFullExport()
    {
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $result = $dbWrapper->query(
            'SELECT count(*) as count FROM (SELECT DISTINCT subject, predicate, object, l_language FROM statements)'
                . ' as supercount'
        )->fetch();
        $triples = $result['count'];


        $descriptions = core_kernel_api_ModelExporter::exportModels(
            ModelManager::getModel()->getReadableModels(),
            'php'
        );

        $count = 0;
        foreach ($descriptions as $description) {
            foreach ($description as $child) {
                $count += count($child);
            }
        }

        static::assertEquals($triples, $count);
    }
}
