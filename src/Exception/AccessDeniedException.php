<?php

declare(strict_types=1);

namespace Inexdigital\UamAuthorization\Exception;

use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessDeniedException extends RuntimeException
{
    public static function create(string $action, UserInterface $user): self
    {
        return new self(sprintf(
            'Access denied for user "%s" to action "%s"',
            $user->getUserIdentifier(),
            $action,
        ));
    }
}
