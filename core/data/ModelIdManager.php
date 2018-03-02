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

use oat\oatbox\service\ConfigurableService;

class ModelIdManager extends ConfigurableService implements ModelIdManagerInterface
{
    const MODEL_ID_OPTION   = 'modelIds';
    const USER_MODEL_ID_KEY = 'userSpace';
    const USER_MODEL_ID     = 1;
    const MINIMAL_MODEL_ID  = 100;
    const SERVICE_ID        = 'generis/modelIdManager';

    /**
     * @param string $extensionId
     * @param int $modelId
     *
     * @return int
     *
     * @throws \common_Exception
     */
    public function setModelId($extensionId, $modelId = null)
    {
        if (!is_int($modelId)) {
            if (!is_null($modelId)) {
                throw new \InvalidArgumentException('$modelId must be int');
            }

            $modelId = $this->createNewModelId();
        }

        $modelIds = $this->getModelIds();
        $modelIds[$extensionId] = $modelId;

        $this->setOption(self::MODEL_ID_OPTION, $modelIds);

        $this->registerService(self::SERVICE_ID, $this, true);

        return $modelId;
    }

    /**
     * @return int
     */
    private function createNewModelId()
    {
        $modelIds = $this->getModelIds();

        if (empty($modelIds)) {
            return self::MINIMAL_MODEL_ID;
        }

        $maxModelId = max($modelIds);

        return $maxModelId >= self::MINIMAL_MODEL_ID ? ++$maxModelId : self::MINIMAL_MODEL_ID;
    }

    /**
     * @param string[]|null $keys
     *
     * @return int[]
     */
    public function getModelIds(array $keys = null)
    {
        $modelIds = $this->getOption(self::MODEL_ID_OPTION);

        if (is_array($keys) && !empty($keys)) {
            $result = [];

            foreach ($keys as $key) {
                $result[$key] = isset($modelIds[$key]) ? $modelIds[$key] : null;
            }

            $modelIds = $result;
        }

        return is_array($modelIds) ? $modelIds : [];
    }

    /**
     * @return int
     *
     * @throws ModelIdNotFoundException
     */
    public function getUserModelId()
    {
        return $this->getModelId(self::USER_MODEL_ID_KEY);
    }

    /**
     * @param string $key
     *
     * @return int
     *
     * @throws ModelIdNotFoundException
     */
    public function getModelId($key)
    {
        $modelIds = $this->getModelIds();

        if (!$this->hasModelId($key)) {
            throw new ModelIdNotFoundException($key);
        }

        return $modelIds[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasModelId($key)
    {
        $modelIds = $this->getModelIds();

        return isset($modelIds[$key]);
    }
}
