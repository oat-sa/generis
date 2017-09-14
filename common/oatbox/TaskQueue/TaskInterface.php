<?php

namespace oat\oatbox\TaskQueue;

/**
 * TaskInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface TaskInterface extends MessageInterface
{
    const JSON_PARAMETERS_KEY = 'parameters';

    /**
     * @return \common_report_Report
     */
    public function __invoke();

    /**
     * Set task parameter
     *
     * Non-destructive setting of message metadata; always adds to the metadata, never overwrites
     * the entire metadata container.
     *
     * @param  string|int|array|\Traversable $spec
     * @param  mixed $value
     */
    public function setParameter($spec, $value = null);

    /**
     * @param null|string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function getParameter($key = null, $default = null);
}