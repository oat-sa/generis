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
 *               2017      (update and modification) Open Assessment Technologies SA;
 *
 */

use oat\generis\test\TestCase;
use oat\oatbox\extension\ComposerInfo;
use oat\oatbox\extension\Manifest;

class ManifestTest extends TestCase
{
    public const SAMPLES_PATH = '/../../test/samples/manifests/';
    public const MANIFEST_PATH_DOES_NOT_EXIST = 'idonotexist.php';
    public const MANIFEST_PATH_LIGHTWEIGHT = 'lightweightManifest.php';
    public const MANIFEST_PATH_COMPLEX = 'complexManifest.php';

    private function getComposerInfoMock()
    {
        $composerInfo = $this->getMockBuilder(ComposerInfo::class)->getMock();

        $composerInfo->method('extractExtensionDependencies')->willReturn([
              'taoItemBank' => '*',
              'taoDocuments' => '*'
        ]);

        return $composerInfo;
    }

    public function testManifestLoading()
    {
        $extensionsManager = $this->getMockBuilder(\common_ext_ExtensionsManager::class)->getMock();
        $extensionsManager->method('getAvailablePackages')->willReturn([
            'oat-sa/extension-tao-taoItemBank' => 'taoItemBank',
            'oat-sa/extension-tao-taoDocuments' => 'taoDocuments'
        ]);
        $serviceLocator = $this->getServiceLocatorMock([
            common_cache_Cache::SERVICE_ID => new \common_cache_NoCache(),
            common_ext_ExtensionsManager::class => $extensionsManager
        ]);
        $currentPath = dirname(__FILE__);
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', $currentPath . self::SAMPLES_PATH);
        }
        $composerInfo = $this->getComposerInfoMock();
        // try to load a manifest that does not exists.
        try {
            $manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_DOES_NOT_EXIST;
            $manifest = new Manifest($manifestPath, $composerInfo);
            $this->assertTrue(false, "Trying to load a manifest that does not exist should raise an exception");
        } catch (Exception $e) {
            $this->assertInstanceOf(oat\oatbox\extension\exception\ManifestNotFoundException::class, $e);
        }

        // Load a simple lightweight manifest that exists and is well formed.
        $manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_LIGHTWEIGHT;
        $manifest = new Manifest($manifestPath, $composerInfo);
        $manifest->setServiceLocator($serviceLocator);
        $this->assertInstanceOf(Manifest::class, $manifest);
        $this->assertEquals('lightweight', $manifest->getName());
        $this->assertEquals('lightweight testing manifest', $manifest->getDescription());
        $this->assertEquals('TAO Team', $manifest->getAuthor());

        // Load a more complex manifest that exists and is well formed.
        $manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_COMPLEX;
        $manifest = new Manifest($manifestPath, $composerInfo);
        $manifest->setServiceLocator($serviceLocator);
        $this->assertInstanceOf(Manifest::class, $manifest);
        $this->assertEquals('complex', $manifest->getName());
        $this->assertEquals('complex testing manifest', $manifest->getDescription());
        $this->assertEquals('TAO Team', $manifest->getAuthor());
        $this->assertEquals(['taoItemBank', 'taoDocuments'], array_keys($manifest->getDependencies()));
        $this->assertEquals(
            [
            '/extension/path/models/ontology/taofuncacl.rdf',
            '/extension/path/models/ontology/taoitembank.rdf'
            ],
            $manifest->getInstallModelFiles()
        );
        $this->assertEquals(
            [
                'WS_ENDPOINT_TWITTER' => 'http://twitter.com/statuses/',
                'WS_ENDPOINT_FACEBOOK' => 'http://api.facebook.com/restserver.php'
            ],
            $manifest->getConstants()
        );
    }
}
