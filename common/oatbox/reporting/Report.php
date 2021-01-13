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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types = 1);

namespace oat\oatbox\reporting;

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

}
