<?php


namespace oat\oatbox\extension;


class Composer
{
    const COMPOSER_JSON = 'composer.json';
    const COMPOSER_LOCK = 'composer.lock';

    /**
     * @param $folder
     * @return array
     * @throws \common_exception_FileReadFailedException
     */
    public function getComposerJson($folder): array
    {
        $file = realpath($folder).DIRECTORY_SEPARATOR.self::COMPOSER_JSON;
        if (!file_exists($file)) {
            throw new \ common_exception_FileReadFailedException($file.' file not found');
        }
        $content = file_get_contents($file);
        return json_decode($content, true);
    }

    /**
     * @param $folder
     * @return array
     * @throws \common_exception_FileReadFailedException
     */
    public function getComposerLock($folder): array
    {
        $file = realpath($folder).DIRECTORY_SEPARATOR.self::COMPOSER_LOCK;
        if (!file_exists($file)) {
            throw new \ common_exception_FileReadFailedException($file.' file not found');
        }
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
}