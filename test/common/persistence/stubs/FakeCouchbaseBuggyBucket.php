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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Konstantin Sasim <sasim@1pt.com>
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\test\common\persistence\stubs;

/**
 * Class FakeCouchbaseBuggyBucket
 *
 * Emulates CouchbaseBucket class of couchbase.so extension; buggy behaviour
 *
 * @package oat\generis\test
 */
class FakeCouchbaseBuggyBucket extends FakeCouchbaseBucket {

    protected function doNastyCouchbaseBug()
    {
        throw new \CouchbaseException("General bucket error", 1);
    }

    public function upsert($id, $value)
    {
        $this->doNastyCouchbaseBug();
    }

    public function get($id)
    {
        $this->doNastyCouchbaseBug();
    }

    public function remove($id)
    {
        $this->doNastyCouchbaseBug();
    }

}