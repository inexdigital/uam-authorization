<?php
declare(strict_types=1);
namespace Tests\Unit;
require_once __DIR__ . '/TestDoubles.php';
use Casbin\Enforcer;
use Inexdigital\UamAuthorization\Dto\Config;
use Inexdigital\UamAuthorization\Dto\Policy;
use Inexdigital\UamAuthorization\Exception\PolicyAddException;
use Inexdigital\UamAuthorization\Factory\EnforcerFactory;
use Inexdigital\UamAuthorization\Service\UAMService;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Google\Protobuf\RepeatedField;

final class EnforcerFactoryTest extends TestCase
{
    public function testCreateBuildsEnforcerWithPolicies(): void
    {
        $config = new Config('model-text', [
            Policy::createFromArray(new RepeatedField(['p', 'read', 'orders', 'expr-1'])),
            Policy::createFromArray(new RepeatedField(['g', 'admin', 'orders', 'expr-2'])),
        ]);
        $uamService = $this->createMock(UAMService::class);
        $uamService->method('getConfig')->willReturn($config);
        $logger = new SpyLogger();
        $factory = new EnforcerFactory($uamService, $logger);
        $enforcer = $factory->create();
        self::assertInstanceOf(Enforcer::class, $enforcer);
        self::assertCount(1, $enforcer->namedPolicies);
        self::assertCount(1, $enforcer->groupingPolicies);
        self::assertSame(['p', 'read', 'orders', 'expr-1'], $enforcer->namedPolicies[0]);
        self::assertSame(['g', 'admin', 'orders', 'expr-2'], $enforcer->groupingPolicies[0]);
    }

    public function testCreateThrowsPolicyAddExceptionWhenPolicyAddFails(): void
    {
        $config = new Config('model-text', [
            Policy::createFromArray(new RepeatedField(['p', 'read', 'orders', 'expr-1'])),
        ]);
        $uamService = $this->createMock(UAMService::class);
        $uamService->method('getConfig')->willReturn($config);
        $logger = new SpyLogger();
        $factory = new EnforcerFactory($uamService, $logger);
        $this->expectException(PolicyAddException::class);
        $this->expectExceptionMessage('Failed to add policy from UAM policy list');
        // Force stubbed Casbin enforcer to fail when adding policy.
        $GLOBALS['__casbin_throw_on_add'] = new RuntimeException('cannot add');
        try {
            $factory->create();
        } finally {
            unset($GLOBALS['__casbin_throw_on_add']);
        }
    }
}
