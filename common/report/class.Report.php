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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\oatbox\reporting\RecursiveReportIterator;
use oat\oatbox\reporting\Report;
use oat\oatbox\reporting\ReportInterface;

/**
 * The Report allows to return a more detailed return value
 * then a simple boolean variable denoting the success
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @deprecated Please, use oat\oatbox\reporting\Report::class.
 */
class common_report_Report implements ReportInterface
{
    /**
     * Type of the report
     *
     * @var string
     */
    private $type;

    /**
     * Message of the report
     *
     * @var string
     */
    private $message;

    /**
     * Elements of the report
     *
     * @var array
     */
    private $children = [];

    /**
     * Attached to the report data
     *
     * @var mixed
     */
    private $data;

    /**
     * Report constructor.
     *
     * @param string $type
     * @param string $message
     * @param null   $data
     * @param array  $children
     *
     * @throws common_exception_Error
     */
    public function __construct(string $type, string $message = '', $data = null, array $children = [])
    {
        if (!self::isValidType($type)) {
            throw new OutOfBoundsException(sprintf('Type of the report `%s` is unsupported for creation', $type));
        }

        $this->type = $type;
        $this->message = $message;
        $this->data = $data;

        foreach ($children as $child) {
            $this->add($child);
        }
    }

    /**
     * Covers static helpers by the next template: Report::create<Type>
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return Report
     * @throws common_exception_Error
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        if (strpos($name, 'create') !== 0) {
            throw new BadMethodCallException(
                sprintf(
                    'Requested method `%s` is not found or is not allowed in class %s',
                    $name,
                    __CLASS__
                )
            );
        }

        $type = strtolower(str_replace('create', '', $name));

        if (!self::isValidType($type)) {
            throw new OutOfBoundsException(sprintf('Type of the report `%s` is unsupported for creation', $type));
        }

        return new static($type, ...$arguments);
    }

    /**
     * Covers helpers (methods) by defined templates
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return bool|array
     */
    public function __call(string $name, array $arguments)
    {
        /** Covers methods by template: get<Type>[e]s */
        if (0 === strpos($name, 'get')) {
            return $this->handleGetCalls($name, $arguments);
        }

        /** Covers methods by template: contains<Type> */
        if (0 === strpos($name, 'contains')) {
            return $this->handleContainsCalls($name, $arguments);
        }

        throw new BadMethodCallException(
            sprintf(
                'Requested method `%s` is not found or is not allowed in class %s',
                $name,
                __CLASS__
            )
        );
    }

    /**
     * Get message of the report
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAllMessages(): string
    {
        $messages = [$this->getMessage()];

        foreach ($this->children as $child) {
            $messages[] = $child->getAllMessages();
        }

        return implode(', ', $messages);
    }

    /**
     * Update message of the report
     *
     * @param string $message
     *
     * @return Report
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Return type of the report
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Update type of the report
     *
     * @param string $type
     *
     * @return Report
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Return attached data
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Attach data to the report
     *
     * @param mixed|null $data
     *
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Whenever or not there are child reports
     *
     * @return boolean
     */
    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * Return child reports
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Add something (children) to the report
     *
     * @param mixed $mixed accepts single values and arrays of Reports and UserReadableExceptions
     *
     * @return Report
     *
     * @throws common_exception_Error
     */
    public function add($mixed): self
    {
        $mixedArray = is_array($mixed) ? $mixed : [$mixed];

        foreach ($mixedArray as $element) {
            if ($element instanceof self) {
                $this->children[] = $element;
            } elseif ($element instanceof UserReadableException) {
                $this->children[] = new static(self::TYPE_ERROR, $element->getUserMessage());
            } else {
                throw new common_exception_Error(
                    'Tried to add ' . (is_object($element) ? get_class($element) : gettype($element)) . ' to the report'
                );
            }
        }

        return $this;
    }

    /**
     * Whenever or not the type can be found in the report
     *
     * @param string $type
     *
     * @return boolean
     */
    public function contains(string $type): bool
    {
        foreach ($this as $child) {
            if ($child->getType() === $type || $child->contains($type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all children based on type
     *
     * @param array $types
     * @param bool  $asFlat
     *
     * @return array
     */
    public function filterChildrenByTypes(array $types, $asFlat = false): array
    {
        $iterator = true === $asFlat
            ? $this->getRecursiveIterator()
            : $this;

        $found = [];

        foreach ($iterator as $element) {
            if (in_array($element->getType(), $types, true)) {
                $found[] = $element;
            }
        }

        return $found;
    }

    /**
     * Returns an iterator over the children
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getChildren());
    }

    /**
     * User feedback message
     */
    public function __toString(): string
    {
        return $this->getMessage();
    }

    /**
     * Recursively restores report object from json string or array
     *
     * @param string|array $data
     *
     * @return self|null
     *
     * @throws common_exception_Error
     */
    public static function jsonUnserialize($data): ?self
    {
        if (!is_array($data)) {
            $data = (array) json_decode((string) $data, true);
        }

        if (count(array_intersect(['type', 'message', 'data'], array_keys($data))) !== 3) {
            return null;
        }

        $report = new static($data['type'], $data['message'], $data['data']);

        $data['children'] = (!isset($data['children']) || !is_array($data['children'])) ? [] : $data['children'];
        foreach ($data['children'] as $child) {
            $report->add(static::jsonUnserialize($child));
        }

        return $report;
    }

    /**
     * Prepares object data for valid converting to json
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
            'children' => $this->getChildren()
        ];
    }

    /**
     * Returns array representation of the report, recursively
     */
    public function toArray(): array
    {
        return array_merge($this->jsonSerialize(), [
            'children' => array_map(static function (self $report) {
                return $report->toArray();
            }, $this->children)
        ]);
    }

    /**
     * Convenience method to create a simple failure report
     *
     * @param string $message
     * @param mixed  $errors
     * @param null   $data
     *
     * @return Report
     * @throws common_exception_Error
     * @deprecated Please, use `createError` method instead
     */
    public static function createFailure(string $message, array $errors = [], $data = null): self
    {
        return new static(self::TYPE_ERROR, $message, $data, $errors);
    }

    private function handleGetCalls(string $name, array $arguments): array
    {
        $type = strtolower(str_replace('get', '', $name));
        $type = rtrim(substr($type, 0, -1), 'e');

        if (!self::isValidType($type)) {
            throw new OutOfBoundsException(sprintf('Type of report `%s` is unsupported', $type));
        }

        return $this->filterChildrenByTypes([$type], ...$arguments);
    }

    private function handleContainsCalls(string $name, array $arguments): bool
    {
        $type = strtolower(str_replace('contains', '', $name));

        if (!self::isValidType($type)) {
            throw new OutOfBoundsException(sprintf('Type of report `%s` is unsupported', $type));
        }

        return $this->contains($type);
    }

    private function getRecursiveIterator(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveReportIterator($this->getIterator()),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    private static function isValidType(string $type): bool
    {
        return in_array($type, static::ALLOWED_TYPES);
    }
}
