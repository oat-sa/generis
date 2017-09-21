<?php

namespace oat\oatbox\TaskQueue;

/**
 * Class AbstractTask
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
abstract class AbstractTask extends Message implements TaskInterface
{
    private $parameters = [];

    /**
     * @return string
     */
    public function __toString()
    {
        return 'TASK '. get_called_class() .' ['. $this->getId() .']';
    }

    /**
     * Set task parameter
     *
     * Non-destructive setting of message metadata; always adds to the metadata, never overwrites
     * the entire metadata container.
     *
     * @param  string|int|array|\Traversable $spec
     * @param  mixed $value
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setParameter($spec, $value = null)
    {
        if (is_scalar($spec)) {
            $this->parameters[$spec] = $value;
            return $this;
        }

        if (!is_array($spec) && !$spec instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Expected a string, array, or Traversable argument in first position; received "%s"',
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        foreach ($spec as $key => $value) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    /**
     * Retrieve all parameters or a single parameter as specified by key
     *
     * @param  null|string|int $key
     * @param  null|mixed $default
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getParameter($key = null, $default = null)
    {
        if (null === $key) {
            return $this->parameters;
        }

        if (!is_scalar($key)) {
            throw new \InvalidArgumentException('Non-scalar argument provided for key');
        }

        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $this->setBody(get_called_class());

        return array_merge(parent::jsonSerialize(), [
            self::JSON_PARAMETERS_KEY => $this->getParameter()
        ]);
    }
}