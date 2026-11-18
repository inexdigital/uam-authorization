<?php

declare(strict_types=1);

namespace Inexdigital\UamAuthorization\Service;

use Inexdigital\UamAuthorization\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

interface EnforcerInterface
{
    public function enforce(string $name, $role, object $sub, $obj): bool;

    public function enforceByUser(string $name, UserInterface $user, object $obj): bool;

    /**
     * @throws AccessDeniedException
     */
    public function enforceByUserOrFail(string $name, UserInterface $user, object $obj): void;
}
