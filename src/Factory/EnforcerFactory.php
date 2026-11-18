<?php

declare(strict_types=1);

namespace Inexdigital\UamAuthorization\Factory;

use Casbin\Enforcer;
use Casbin\Model\Model;
use Inexdigital\UamAuthorization\Exception\PolicyAddException;
use Inexdigital\UamAuthorization\Service\EnforcerInterface;
use Inexdigital\UamAuthorization\Service\UAMService;
use Psr\Log\LoggerInterface;
use Throwable;

class EnforcerFactory implements EnforcerFactoryInterface
{
    public function __construct(
        private readonly UAMService $uamService,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Summary of create
     * @return EnforcerInterface
     * @throws PolicyAddException
     */
    public function create(): EnforcerInterface
    {
        $config = $this->uamService->getConfig();
        $enforcer = new Enforcer();
        $model = Model::newModelFromString($config->model);
        $enforcer->setModel($model);

        foreach ($config->policyList as $policy) {
            try {
                if ($policy->isGroupingPolicy()) {
                    $enforcer->addNamedGroupingPolicy(
                        $policy->getType(),
                        $policy->getPermission(),
                        $policy->getName(),
                        $policy->getExpression()
                    );

                    continue;
                }

                $enforcer->addNamedPolicy(
                    $policy->getType(),
                    $policy->getPermission(),
                    $policy->getName(),
                    $policy->getExpression()
                );
            } catch (Throwable $e) {
                $this->logger->error(
                    'Failed to add policy while building enforcer',
                    [
                        'type' => $policy->getType(),
                        'permission' => $policy->getPermission(),
                        'name' => $policy->getName(),
                        'expression' => $policy->getExpression(),
                        'exception' => $e
                    ]
                );

                throw PolicyAddException::create($e);
            }
        }

        return $enforcer;
    }
}
