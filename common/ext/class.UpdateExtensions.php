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
 */

declare(strict_types = 1);

use oat\oatbox\action\Action;
use oat\oatbox\log\LoggerAwareTrait;
use common_report_Report as Report;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Psr\Log\LoggerAwareInterface;
use oat\oatbox\cache\SimpleCache;
/**
 * Run the extension updater
 *
 * @access public
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_ext_UpdateExtensions implements Action, ServiceLocatorAwareInterface, LoggerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use LoggerAwareTrait;
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\action\Action::__invoke()
     */
    public function __invoke($params)
    {
        $extManager = $this->getExtensionManager();
        $merged = array_merge(
            $extManager->getInstalledExtensions(),
            $this->getMissingExtensions()
        );

        $sorted = \helpers_ExtensionHelper::sortByDependencies($merged);
        $report = new Report(Report::TYPE_INFO, 'Running extension update');

        foreach ($sorted as $ext) {
            try {
                if (!$extManager->isInstalled($ext->getId())) {
                    $installer = new \tao_install_ExtensionInstaller($ext);
                    $installer->install();
                    $report->add(new Report(Report::TYPE_SUCCESS, 'Installed ' . $ext->getName()));
                } else {
                    $report->add($this->updateExtension($ext));
                }
            } catch (common_ext_MissingExtensionException $ex) {
                $report->add(new Report(Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (common_ext_OutdatedVersionException $ex) {
                $report->add(new Report(Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (Exception $e) {
                $this->logError('Exception during update of ' . $ext->getId() . ': ' . get_class($e) . ' "' . $e->getMessage() . '"');
                $report->setType(Report::TYPE_ERROR);
                $report->setTitle('Update failed');
                $report->add(new Report(Report::TYPE_ERROR, 'Exception during update of ' . $ext->getId() . '.'));
                break;
            }
        }
        $this->logInfo(helpers_Report::renderToCommandline($report, false));
        return $report;
    }

    /**
     * Update a specific extension
     *
     * @param common_ext_Extension $ext
     * @return Report
     * @throws common_exception_Error
     * @throws common_ext_ManifestNotFoundException
     * @throws common_ext_MissingExtensionException
     * @throws common_ext_OutdatedVersionException
     */
    protected function updateExtension(common_ext_Extension $ext)
    {
        helpers_ExtensionHelper::checkRequiredExtensions($ext);
        $installed = $this->getExtensionManager()->getInstalledVersion($ext->getId());
        $codeVersion = $ext->getVersion();
        if ($installed !== $codeVersion) {
            $report = new Report(Report::TYPE_INFO, $ext->getName() . ' requires update from ' . $installed . ' to ' . $codeVersion);
            try {
                $updater = $ext->getUpdater();
                $returnedVersion = $updater->update($installed);
                $currentVersion = $this->getExtensionManager()->getInstalledVersion($ext->getId());

                if (!is_null($returnedVersion) && $returnedVersion != $currentVersion) {
                    $this->getExtensionManager()->updateVersion($ext, $returnedVersion);
                    $report->add(new Report(Report::TYPE_WARNING, 'Manually saved extension version'));
                    $currentVersion = $returnedVersion;
                }

                if ($currentVersion == $codeVersion) {
                    $versionReport = new Report(Report::TYPE_SUCCESS, 'Successfully updated ' . $ext->getName() . ' to ' . $currentVersion);
                } else {
                    $versionReport = new Report(Report::TYPE_WARNING, 'Update of ' . $ext->getName() . ' exited with version ' . $currentVersion);
                }

                foreach ($updater->getReports() as $updaterReport) {
                    $versionReport->add($updaterReport);
                }

                $report->add($versionReport);

                $this->getServiceLocator()->get(SimpleCache::SERVICE_ID)->clear();
            } catch (common_ext_ManifestException $e) {
                $report = new Report(Report::TYPE_WARNING, $e->getMessage());
            }
        } else {
            $report = new Report(Report::TYPE_INFO, $ext->getName() . ' already up to date');
        }
        return $report;
    }

    protected function getMissingExtensions()
    {
        $missingId = \helpers_ExtensionHelper::getMissingExtensionIds($this->getExtensionManager()->getInstalledExtensions());
        
        $missingExt = [];
        foreach ($missingId as $extId) {
            $ext = $this->getExtensionManager()->getExtensionById($extId);
            $missingExt[$extId] = $ext;
        }
        return $missingExt;
    }

    /**
     * @return common_ext_ExtensionsManager
     */
    private function getExtensionManager()
    {
        return $this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID);
    }

}
