<?php

declare(strict_types=1);

namespace Inexdigital\UamAuthorization\Service;

use Casbin\Enforcer as CasbinEnforcer;
use Casbin\Exceptions\CasbinException;
use Inexdigital\UamAuthorization\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Enforcer
{
    public function __construct(
        private readonly CasbinEnforcer $enforcer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Enforce checks whether a "subject" can access an "object" with the given "action".
     * @param string $name
     * @param string $role
     * @param object $sub
     * @param object $obj
     * @return bool
     */
    public function enforce(string $name, $role, object $sub, $obj): bool
    {
        try {
            return $this->enforcer->enforce($role, $name, $sub, $obj);
        } catch (CasbinException $e) {
            $this->logger->error(
                'ABAC enforce failed: CasbinException while evaluating access policy',
                [
                    'action' => $name,
                    'role' => $role,
                    'exception' => $e,
                ]
            );
        }

        return false;
    }

    /**
     * EnforceByUser checks whether any of the user's roles can access an "object" with the given "action".
     * If any role is allowed, the method returns true. If no roles are allowed or an exception occurs, it returns false.
     * @param string $name
     * @param UserInterface $user
     * @param object $obj
     * @return bool
     */
    public function enforceByUser(string $name, UserInterface $user, object $obj): bool
    {
        try {
            foreach ($user->getRoles() as $role) {
                $isAllowed = $this->enforcer->enforce($role, $name, $user, $obj);

                if (!$isAllowed) {
                    continue;
                }

                return true;
            }
        } catch (CasbinException $e) {
            $this->logger->error(
                'ABAC enforce failed: CasbinException while evaluating access policy',
                [
                    'action' => $name,
                    'user' => $user->getUserIdentifier(),
                    'roles' => $user->getRoles(),
                    'exception' => $e,
                ]
            );
        }

        return false;
    }

    /**
     * @throws AccessDeniedException
     */
    public function enforceByUserOrFail(string $name, UserInterface $user, object $obj): void
    {
        if ($this->enforceByUser($name, $user, $obj)) {
            return;
        }

        throw AccessDeniedException::create($name, $user);
    }
}
