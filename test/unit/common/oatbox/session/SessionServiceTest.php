<?php

declare(strict_types=1);

namespace oat\generis\test\unit\common\oatbox\session;

use oat\oatbox\session\SessionService;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private array $environment;
    private $processTenantId;

    protected function setUp(): void
    {
        $this->environment = $_ENV;
        $this->processTenantId = getenv('TENANT_ID');
    }

    protected function tearDown(): void
    {
        $_ENV = $this->environment;
        putenv($this->processTenantId === false ? 'TENANT_ID' : 'TENANT_ID=' . $this->processTenantId);
    }

    /** @dataProvider tenantIdProvider */
    public function testGetTenantId(?string $dotenvTenantId, ?string $processTenantId, ?string $expected): void
    {
        unset($_ENV['TENANT_ID']);
        if ($dotenvTenantId !== null) {
            $_ENV['TENANT_ID'] = $dotenvTenantId;
        }
        putenv($processTenantId === null ? 'TENANT_ID' : 'TENANT_ID=' . $processTenantId);

        $this->assertSame($expected ?? rtrim(ROOT_URL, '/'), (new SessionService())->getTenantId());
    }

    public function tenantIdProvider(): array
    {
        return [
            'dotenv only' => ['1', null, '1'],
            'dotenv takes precedence' => ['1', '2', '1'],
            'process environment fallback' => [null, '2', '2'],
            'empty dotenv fallback' => ['', '2', '2'],
            'root URL fallback' => [null, null, null],
            'empty values fallback' => ['', '', null],
        ];
    }
}
