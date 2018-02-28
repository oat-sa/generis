<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 21.02.18
 * Time: 13:52
 */

namespace oat\generis\scripts\exercise;

use oat\oatbox\service\ConfigurableService;

abstract class ConfigurableServiceWrapper extends ConfigurableService
{
    /**
     * @var ExerciseServiceInterface
     */
    protected $parentService;

    /**
     * [
     *  'config' => [],
     *  'parent' => [
     *      'class' => '',
     *      'config' => ''
     * ]
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(array $options = array())
    {
        if (!isset(
            $options['config'],
            $options['parent'],
            $options['parent']['class'],
            $options['parent']['config']
        )) {
            throw new \Exception('wrong config');
        }

        parent::__construct($options);

        $parentClass = $options['parent']['class'];

        $this->parentService = new $parentClass($options['parent']['config']);
    }

    /**
     * @param ConfigurableService $wrappedService
     * @param array $options
     *
     * @return static
     *
     * @throws \Exception
     */
    public static function wrapService(ConfigurableService $wrappedService, array $options = [])
    {
        return new static(
            [
                'config' => $options,
                'parent' => [
                    'class'  => get_class($wrappedService),
                    'config' => $wrappedService->getOptions()
                ]
            ]
        );
    }

}