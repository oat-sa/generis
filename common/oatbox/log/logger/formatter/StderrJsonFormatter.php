<?php

declare(strict_types=1);

namespace oat\oatbox\log\logger\formatter;

use Monolog\Formatter\FormatterInterface;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * One JSON object per line for container stderr (GCP / Groundcover friendly).
 *
 * Hoists OpenTelemetry trace context from PSR-3 context to top-level trace_id / span_id.
 *
 * Options (single constructor array, see generis log.conf.php):
 * - includeContext (bool, default false): emit a "context" object for remaining PSR-3 keys
 * - includeExtra (bool, default false): emit Monolog "extra" data
 * - excludeContextKeys (string[]): keys stripped from "context" on non-DEBUG levels (see below)
 * - includeContextKeys (string[]|null): when set, only these keys are kept in "context"
 * - maxContextJsonLength (int|null): truncate serialized context above this size (when includeContext)
 *
 * DEBUG: full PSR-3 context is kept (only maxContextJsonLength may truncate).
 * Non-DEBUG: excludeContextKeys defaults to DEFAULT_EXCLUDE_CONTEXT_KEYS.
 */
class StderrJsonFormatter implements FormatterInterface
{
    public const DEFAULT_EXCLUDE_CONTEXT_KEYS = [
        'traceId',
        'spanId',
        'file',
        'line',
        'allowedRoles',
        'contextUserData',
    ];

    private bool $includeContext;

    private bool $includeExtra;

    /** @var list<string> */
    private array $excludeContextKeys;

    /** @var list<string>|null */
    private ?array $includeContextKeys;

    private ?int $maxContextJsonLength;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->includeContext = (bool) ($options['includeContext'] ?? false);
        $this->includeExtra = (bool) ($options['includeExtra'] ?? false);
        $this->excludeContextKeys = isset($options['excludeContextKeys']) && is_array($options['excludeContextKeys'])
            ? array_values($options['excludeContextKeys'])
            : self::DEFAULT_EXCLUDE_CONTEXT_KEYS;
        $this->includeContextKeys = isset($options['includeContextKeys']) && is_array($options['includeContextKeys'])
            ? array_values($options['includeContextKeys'])
            : null;
        $maxLength = $options['maxContextJsonLength'] ?? null;
        $this->maxContextJsonLength = is_numeric($maxLength) ? (int) $maxLength : null;
    }

    /**
     * @return string JSON line with trailing newline
     */
    public function format(LogRecord $record): string
    {
        $json = json_encode(
            $this->buildPayload($record),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        return $json . PHP_EOL;
    }

    /**
     * @param array<LogRecord> $records
     *
     * @return string
     */
    public function formatBatch(array $records): string
    {
        $lines = [];
        foreach ($records as $record) {
            $lines[] = rtrim($this->format($record), PHP_EOL);
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(LogRecord $record): array
    {
        $context = $record->context;

        $payload = [
            'timestamp' => $record->datetime->format(\DateTimeInterface::ATOM),
            'level' => $record->level->getName(),
            'message' => $record->message,
            'channel' => $record->channel,
        ];

        if (!empty($context['traceId'])) {
            $payload['trace_id'] = (string) $context['traceId'];
        }

        if (!empty($context['spanId'])) {
            $payload['span_id'] = (string) $context['spanId'];
        }

        foreach (['file', 'line', 'request'] as $field) {
            if (isset($context[$field]) && $context[$field] !== '') {
                $payload[$field] = $context[$field];
            }
        }

        if ($this->includeContext) {
            $filteredContext = $this->filterContext($context, $record);
            if ($filteredContext !== []) {
                $payload['context'] = $this->applyContextLengthLimit($filteredContext);
            }
        }

        if ($this->includeExtra && $record->extra !== []) {
            $payload['extra'] = $record->extra;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    private function filterContext(array $context, LogRecord $record): array
    {
        $excludeKeys = $record->level === Level::Debug ? [] : $this->excludeContextKeys;

        $filtered = array_diff_key($context, array_flip($excludeKeys));

        if ($this->includeContextKeys === null) {
            return $filtered;
        }

        return array_intersect_key($filtered, array_flip($this->includeContextKeys));
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>|string
     */
    private function applyContextLengthLimit(array $context)
    {
        if ($this->maxContextJsonLength === null) {
            return $context;
        }

        $encoded = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded === false || strlen($encoded) <= $this->maxContextJsonLength) {
            return $context;
        }

        return [
            '_truncated' => true,
            '_preview' => substr($encoded, 0, $this->maxContextJsonLength),
        ];
    }
}
