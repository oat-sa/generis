<?php

namespace oat\generis\model\data;

interface ModelPermissionInterface
{
    /**
     * @return int[]
     */
    public function getReadableModels();

    /**
     * @return int[]
     */
    public function getWritableModels();

    /**
     * @param int $modelId
     */
    public function addReadableModel($modelId);

    /**
     * @param int $modelId
     */
    public function addWritableModel($modelId);
}
