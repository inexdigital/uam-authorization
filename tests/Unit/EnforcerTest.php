<?php
declare(strict_types=1);
namespace Tests\Unit;
require_once __DIR__ . '/TestDoubles.php';
use Casbin\Enforcer as CasbinEnforcer;
use Casbin\Exceptions\CasbinException;
use Inexdigital\UamAuthorization\Exception\AccessDeniedException;
use Inexdigital\UamAuthorization\Service\Enforcer;
use PHPUnit\Framework\TestCase;

final class EnforcerTest extends TestCase
{
    public function testEnforceReturnsTrueWhenAnyRoleIsAllowed(): void
    {
        $casbin = new CasbinEnforcer();
        $casbin->enforceMap = [
            'ROLE_USER' => false,
            'ROLE_ADMIN' => true,
        ];
        $logger = new SpyLogger();
        $service = new Enforcer($casbin, $logger);
        $result = $service->enforceByUser('orders.read', new FakeUser(['ROLE_USER', 'ROLE_ADMIN']), (object) ['id' => 1]);
        self::assertTrue($result);
    }

    public function testEnforceReturnsFalseWhenNoRoleIsAllowed(): void
    {
        $casbin = new CasbinEnforcer();
        $casbin->enforceMap = [
            'ROLE_USER' => false,
            'ROLE_MANAGER' => false,
        ];
        $logger = new SpyLogger();
        $service = new Enforcer($casbin, $logger);
        $result = $service->enforceByUser('orders.read', new FakeUser(['ROLE_USER', 'ROLE_MANAGER']), (object) ['id' => 1]);
        self::assertFalse($result);
    }

    public function testEnforceReturnsFalseAndLogsWhenCasbinThrows(): void
    {
        $casbin = new CasbinEnforcer();
        $casbin->throwOnEnforce = new CasbinException('casbin-failed');
        $logger = new SpyLogger();
        $service = new Enforcer($casbin, $logger);
        $result = $service->enforceByUser('orders.read', new FakeUser(['ROLE_USER'], 'john@doe.test'), (object) ['id' => 1]);
        self::assertFalse($result);
        self::assertNotEmpty($logger->records);
        self::assertSame('error', $logger->records[0]['level']);
        self::assertSame('orders.read', $logger->records[0]['context']['action']);
        self::assertSame('john@doe.test', $logger->records[0]['context']['user']);
    }

    public function testEnforceByUserOrFailReturnsSilentlyWhenAnyRoleIsAllowed(): void
    {
        $casbin = new CasbinEnforcer();
        $casbin->enforceMap = [
            'ROLE_USER' => false,
            'ROLE_ADMIN' => true,
        ];
        $service = new Enforcer($casbin, new SpyLogger());

        $service->enforceByUserOrFail('orders.read', new FakeUser(['ROLE_USER', 'ROLE_ADMIN'], 'john@doe.test'), (object) ['id' => 1]);

        self::assertTrue(true);
    }

    public function testEnforceByUserOrFailThrowsDomainExceptionWhenAccessDenied(): void
    {
        $casbin = new CasbinEnforcer();
        $casbin->enforceMap = [
            'ROLE_USER' => false,
        ];
        $service = new Enforcer($casbin, new SpyLogger());

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access denied for user "john@doe.test" to action "orders.read"');

        $service->enforceByUserOrFail('orders.read', new FakeUser(['ROLE_USER'], 'john@doe.test'), (object) ['id' => 1]);
    }
}
