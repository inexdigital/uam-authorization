<?php

declare(strict_types=1);

namespace Inexdigital\UamAuthorization\Factory;

use Inexdigital\UamAuthorization\Exception\PolicyAddException;
use Inexdigital\UamAuthorization\Service\EnforcerInterface;

interface EnforcerFactoryInterface
{
    /**
     * @throws PolicyAddException
     */
    public function create(): EnforcerInterface;
}
