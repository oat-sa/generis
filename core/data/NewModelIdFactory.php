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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\data;

use common_ext_ExtensionException;
use common_ext_ExtensionsManager;

class NewModelIdFactory
{
    const MIN_INDEX = 100;

    /**
     * @return int
     *
     * @throws common_ext_ExtensionException
     */
    public function create()
    {
        // TODO: should be injected [sergii.chernenko]
        $extensionsManager = common_ext_ExtensionsManager::singleton();

        $installedExtensions = $extensionsManager
            ->getExtensionById('generis')
            ->getConfig(common_ext_ExtensionsManager::EXTENSIONS_CONFIG_KEY);

        // No extension installed
        if ($installedExtensions === false) {
            return self::MIN_INDEX;
        }

        $greatestModelId = self::MIN_INDEX;

        foreach ($installedExtensions as $extension) {
            if (isset($extension['extension_numeric_id'])
                && $greatestModelId < (int)$extension['extension_numeric_id']
            ) {
                $greatestModelId = (int)$extension['extension_numeric_id'];
            }
        }

        return ++$greatestModelId;
    }
}
