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
 * Copyright (c) 2020-2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\oatbox\reporting;

use common_exception_Error;
use common_report_Report;

/**
 * The Report allows to return a more detailed return value
 * then a simple boolean variable denoting the success.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @author Ivan Klimchuk, <ivan.klimchuk@1pt.com>
 *
 * @method static self createInfo(string $message, $data = null, array $children = []): self
 * @method static self createSuccess(string $message, $data = null, array $children = []): self
 * @method static self createWarning(string $message, $data = null, array $children = []): self
 * @method static self createError(string $message, $data = null, array $children = []): self
 *
 * @method $this getInfos(bool $asFlat = false): array
 * @method $this getSuccesses(bool $asFlat = false): array
 * @method $this getWarnings(bool $asFlat = false): array
 * @method $this getErrors(bool $asFlat = false): array
 *
 * @method $this containsInfo() Whenever or not the report contains info messages
 * @method $this containsSuccess() Whenever or not the report contains successes
 * @method $this containsWarning() Whenever or not the report contains warnings
 * @method $this containsError() Whenever or not the report contains errors
 */
class Report extends common_report_Report
{
    /** @var string */
    private $interpolationMessage;

    /** @var array */
    private $interpolationData;

    /**
     * Create Report with translations support
     *
     * @throws common_exception_Error
     */
    public static function create(string $type, string $interpolationMessage, array $interpolationData = []): Report
    {
        return (new self($type, sprintf($interpolationMessage, ...$interpolationData)))
            ->setInterpolationMessage($interpolationMessage, $interpolationData);
    }

    public function setInterpolationMessage(string $interpolationMessage, array $interpolationData = []): self
    {
        $this->interpolationMessage = $interpolationMessage;
        $this->interpolationData = $interpolationData;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        if ($this->interpolationMessage) {
            $data['interpolationMessage'] = $this->interpolationMessage;
        }

        if ($this->interpolationData || $this->interpolationMessage) {
            $data['interpolationData'] = $this->interpolationData;
        }

        return $data;
    }

    public static function jsonUnserialize($data): ?common_report_Report
    {
        /** @var Report $report */
        $report = parent::jsonUnserialize($data);

        if (isset($data['interpolationMessage'])) {
            $report->setInterpolationMessage(
                (string) $data['interpolationMessage'],
                (array) ($data['interpolationData'] ?? [])
            );
        }

        return $report;
    }

    public function translateMessage(): string
    {
        if ($this->interpolationMessage && count($this->interpolationData) > 0) {
            return __($this->interpolationMessage, ...$this->interpolationData);
        }

        return __($this->interpolationMessage ?? $this->getMessage());
    }
}
