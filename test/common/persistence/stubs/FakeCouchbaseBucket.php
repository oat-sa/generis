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
 * Class FakeCouchbaseBucket
 *
 * Emulates CouchbaseBucket class of couchbase.so extension
 *
 * @package oat\generis\test
 */
class FakeCouchbaseBucket {

    const DOCUMENT_NOT_FOUND_ERROR = 13;

    protected $name;
    protected $password;

    protected $data = array();

    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    public function upsert($id, $value)
    {
        if( !array_key_exists($id, $this->data) ){
            $this->__createDocument($id, $value);
        } else {
            $this->data[$id]->value = $value;
        }

        return $this->data[$id];
    }

    public function get($id )
    {
        if( !array_key_exists($id, $this->data) ){
            throw new \CouchbaseException("Document not found", self::DOCUMENT_NOT_FOUND_ERROR);
        }

        return $this->data[$id];
    }

    public function remove($id )
    {
        if( !array_key_exists($id, $this->data) ){
            throw new \CouchbaseException("Document not found", self::DOCUMENT_NOT_FOUND_ERROR);
        }

        unset($this->data[$id]);
    }

    public function __documentExists($id )
    {
        return array_key_exists($id, $this->data);
    }

    public function __getDocument($id )
    {
        return $this->__documentExists($id) ? $this->data[$id] : null;
    }

    public function __createDocument($id, $value)
    {
        $document = new \CouchbaseMetaDoc();
        $document->value = $value;

        $this->data[$id] = $document;

        return $document;
    }

}