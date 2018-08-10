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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\common\http;

use \common_http_Request;
use oat\generis\test\TestCase;

/**
 * Test the \common_http_Request class
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class RequestTest extends TestCase {

    /**
     * Test the method getHeaderValue
     *
     * @dataProvider headerProvider
     */
    public function testGetHeaderValue($headers, $headerName, $expect)
    {
        $request = new common_http_Request('http://foo.bar', 'POST', [], $headers);
        $result  = $request->getHeaderValue($headerName);
        $this->assertEquals($expect, $result);
    }

    /**
     * Provides data for the getHeaderValue test case
     * @return array the data
     */
    public function headerProvider()
    {
        return [
            [ ['Content-Type' => 'application/json' ], 'Content-Type', 'application/json' ],
            [ ['Content-Type' => 'application/json' ], 'Accept', false ],
            [ ['Content-Type' => 'application/json' ], 'content-type', 'application/json' ],
            [ ['ACCEPT' => 'application/json' ], 'Accept', 'application/json' ],
            [ ['accept' => 'application/json' ], 'Accept', 'application/json' ],
            [ ['Accept' => 'application/json' ], 'ACCEPT', 'application/json' ],
        ];
    }

}
