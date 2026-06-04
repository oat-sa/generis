<?php

declare(strict_types=1);

namespace oat\generis\test\unit\oatbox\log\logger\formatter;

use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use oat\oatbox\log\logger\formatter\StderrJsonFormatter;
use PHPUnit\Framework\TestCase;

class StderrJsonFormatterTest extends TestCase
{
    public function testFormatOutputsJsonWithTraceFields(): void
    {
        $formatter = new StderrJsonFormatter();
        $record = new LogRecord(
            datetime: new DateTimeImmutable('2026-06-04T11:40:31+00:00'),
            channel: 'tao',
            level: Level::Info,
            message: 'Access denied.',
            context: [
                'traceId' => 'abc123',
                'spanId' => 'def456',
                'file' => '/var/www/html/test.php',
                'line' => 82,
            ],
            extra: [],
        );

        $line = $formatter->format($record);
        $decoded = json_decode(rtrim($line, PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('INFO', $decoded['level']);
        $this->assertSame('Access denied.', $decoded['message']);
        $this->assertSame('abc123', $decoded['trace_id']);
        $this->assertSame('def456', $decoded['span_id']);
        $this->assertSame('/var/www/html/test.php', $decoded['file']);
        $this->assertSame(82, $decoded['line']);
        $this->assertArrayNotHasKey('context', $decoded);
    }

    public function testFormatOmitsEmptyTraceFields(): void
    {
        $formatter = new StderrJsonFormatter();
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Warning,
            message: 'No span',
            context: [],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('trace_id', $decoded);
        $this->assertArrayNotHasKey('span_id', $decoded);
    }

    public function testFormatOmitsBulkyContextByDefault(): void
    {
        $formatter = new StderrJsonFormatter();
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Info,
            message: 'Access denied.',
            context: [
                'traceId' => 'abc123',
                'spanId' => 'def456',
                'allowedRoles' => ['role-a'],
                'contextUserData' => ['roles' => ['role-b']],
            ],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('context', $decoded);
        $this->assertSame('abc123', $decoded['trace_id']);
    }

    public function testFormatIncludesFilteredContextWhenEnabled(): void
    {
        $formatter = new StderrJsonFormatter([
            'includeContext' => true,
            'includeContextKeys' => ['reason'],
            'excludeContextKeys' => ['traceId', 'spanId'],
        ]);
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Info,
            message: 'Access denied.',
            context: [
                'traceId' => 'abc123',
                'reason' => 'missing role',
                'allowedRoles' => ['role-a'],
            ],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(['reason' => 'missing role'], $decoded['context']);
    }

    public function testFormatTruncatesOversizedContext(): void
    {
        $formatter = new StderrJsonFormatter([
            'includeContext' => true,
            'maxContextJsonLength' => 20,
        ]);
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Info,
            message: 'test',
            context: ['payload' => str_repeat('x', 100)],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($decoded['context']['_truncated']);
        $this->assertLessThanOrEqual(20, strlen($decoded['context']['_preview']));
    }

    public function testDebugLevelIncludesFullContext(): void
    {
        $formatter = new StderrJsonFormatter(['includeContext' => true]);
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Debug,
            message: 'debug detail',
            context: [
                'traceId' => 'abc123',
                'allowedRoles' => ['role-a'],
                'contextUserData' => ['id' => 'user-1'],
                'reason' => 'denied',
            ],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(['role-a'], $decoded['context']['allowedRoles']);
        $this->assertSame(['id' => 'user-1'], $decoded['context']['contextUserData']);
        $this->assertSame('denied', $decoded['context']['reason']);
        $this->assertSame('abc123', $decoded['trace_id']);
        $this->assertSame('abc123', $decoded['context']['traceId']);
    }

    public function testNonDebugLevelFiltersDefaultContextKeys(): void
    {
        $formatter = new StderrJsonFormatter(['includeContext' => true]);
        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'tao',
            level: Level::Info,
            message: 'Access denied.',
            context: [
                'traceId' => 'abc123',
                'spanId' => 'def456',
                'file' => '/var/www/html/test.php',
                'line' => 82,
                'allowedRoles' => ['role-a'],
                'contextUserData' => ['id' => 'user-1'],
                'contextRequestData' => ['requestUri' => '/tao/Main/index'],
                'reason' => 'denied',
            ],
            extra: [],
        );

        $decoded = json_decode(rtrim($formatter->format($record), PHP_EOL), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame([
            'contextRequestData' => ['requestUri' => '/tao/Main/index'],
            'reason' => 'denied',
        ], $decoded['context']);
        $this->assertSame('abc123', $decoded['trace_id']);
        $this->assertSame('def456', $decoded['span_id']);
        $this->assertSame('/var/www/html/test.php', $decoded['file']);
        $this->assertSame(82, $decoded['line']);
    }
}
