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
 * Class FakeCouchbaseCluster
 *
 * Emulates CouchbaseCluster class of couchbase.so extension
 *
 * @package oat\generis\test
 */
class FakeCouchbaseCluster {

    const CLUSTER_MODE_NORMAL        = 0;
    const CLUSTER_MODE_BROKEN        = 1;
    const CLUSTER_MODE_BROKEN_BUCKET = 2;
    const CLUSTER_MODE_BUGGY_BUCKET  = 3;
    /** @var string */
    protected $cluster;

    /** @var string */
    protected $validBucket;
    /** @var string */
    protected $validPassword;

    /** @var FakeCouchbaseBucket */
    protected $bucket;

    /** @var  int */
    protected $mode;
    /**
     * @param string $cluster
     */
    public function __construct($cluster, $mode)
    {
        $this->mode = $mode;
        $this->cluster = $cluster;

        if( $this->mode === self::CLUSTER_MODE_BROKEN ){
            throw new \CouchbaseException("General cluster error");
        }
    }

    public function openBucket($name, $password='')
    {
        if( $this->mode === self::CLUSTER_MODE_BROKEN_BUCKET ){
            throw new \CouchbaseException("Unable to get bucket due to general error");
        }

        if( $name !== $this->validBucket ){
            throw new \CouchbaseException("Bucket not found");
        }

        if( $password !== $this->validPassword ){
            throw new \CouchbaseException("Bucket auth error");
        }

        if( $this->mode === self::CLUSTER_MODE_BUGGY_BUCKET ){
            $this->bucket = new FakeCouchbaseBuggyBucket($name, $password);
        } else {
            $this->bucket = new FakeCouchbaseBucket($name, $password);
        }

        return $this->bucket;
    }

    /**
     * @param string $validBucket
     */
    public function __setValidBucket($validBucket)
    {
        $this->validBucket = $validBucket;
    }

    /**
     * @param string $validPassword
     */
    public function __setValidPassword($validPassword)
    {
        $this->validPassword = $validPassword;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function __bucketDocumentExists($id)
    {
        return $this->bucket->__documentExists($id);
    }

    /**
     * @param $id
     * @return \CouchbaseMetaDoc|null
     */
    public function __getBucketDocument($id)
    {
        return $this->bucket->__getDocument($id);
    }

    public function __createBucketDocument($id, $value)
    {
        return $this->bucket->__createDocument($id, $value);
    }

    public function __setMode( $mode )
    {
        $this->mode = $mode;
    }
}