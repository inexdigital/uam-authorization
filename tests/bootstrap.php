<?php

declare(strict_types=1);

namespace Psr\Log {
    if (!interface_exists(LoggerInterface::class)) {
        interface LoggerInterface
        {
            public function emergency($message, array $context = []): void;
            public function alert($message, array $context = []): void;
            public function critical($message, array $context = []): void;
            public function error($message, array $context = []): void;
            public function warning($message, array $context = []): void;
            public function notice($message, array $context = []): void;
            public function info($message, array $context = []): void;
            public function debug($message, array $context = []): void;
            public function log($level, $message, array $context = []): void;
        }
    }
}

namespace Symfony\Component\Security\Core\User {
    if (!interface_exists(UserInterface::class)) {
        interface UserInterface
        {
            public function getRoles(): array;
            public function eraseCredentials(): void;
            public function getUserIdentifier(): string;
        }
    }
}

namespace Casbin\Exceptions {
    if (!class_exists(CasbinException::class)) {
        class CasbinException extends \RuntimeException
        {
        }
    }
}

namespace Casbin\Model {
    if (!class_exists(Model::class)) {
        class Model
        {
            public static function newModelFromString(string $model): array
            {
                return ['model' => $model];
            }
        }
    }
}

namespace Casbin {
    use Casbin\Exceptions\CasbinException;

    if (!class_exists(Enforcer::class)) {
        class Enforcer
        {
            public array $namedPolicies = [];
            public array $groupingPolicies = [];
            public array $enforceMap = [];
            public ?\Throwable $throwOnAdd = null;
            public ?CasbinException $throwOnEnforce = null;
            public mixed $model = null;

            public function setModel(mixed $model): void
            {
                $this->model = $model;
            }

            public function addNamedPolicy(string $ptype, string ...$rule): bool
            {
                if (isset($GLOBALS['__casbin_throw_on_add']) && $GLOBALS['__casbin_throw_on_add'] instanceof \Throwable) {
                    throw $GLOBALS['__casbin_throw_on_add'];
                }

                if ($this->throwOnAdd !== null) {
                    throw $this->throwOnAdd;
                }

                $this->namedPolicies[] = [$ptype, ...$rule];

                return true;
            }

            public function addNamedGroupingPolicy(string $ptype, string ...$rule): bool
            {
                if (isset($GLOBALS['__casbin_throw_on_add']) && $GLOBALS['__casbin_throw_on_add'] instanceof \Throwable) {
                    throw $GLOBALS['__casbin_throw_on_add'];
                }

                if ($this->throwOnAdd !== null) {
                    throw $this->throwOnAdd;
                }

                $this->groupingPolicies[] = [$ptype, ...$rule];

                return true;
            }

            public function enforce(string $role, string $name, object $sub, object $obj): bool
            {
                if ($this->throwOnEnforce !== null) {
                    throw $this->throwOnEnforce;
                }

                return $this->enforceMap[$role] ?? false;
            }
        }
    }
}

namespace Google\Protobuf {
    if (!class_exists(RepeatedField::class)) {
        class RepeatedField implements \ArrayAccess, \IteratorAggregate, \Countable
        {
            public function __construct(private array $values = [])
            {
            }

            public function offsetExists(mixed $offset): bool
            {
                return isset($this->values[$offset]);
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->values[$offset] ?? null;
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                if ($offset === null) {
                    $this->values[] = $value;
                    return;
                }

                $this->values[$offset] = $value;
            }

            public function offsetUnset(mixed $offset): void
            {
                unset($this->values[$offset]);
            }

            public function getIterator(): \Traversable
            {
                return new \ArrayIterator($this->values);
            }

            public function count(): int
            {
                return count($this->values);
            }
        }
    }
}

namespace Google\Protobuf\Internal {
    if (!class_exists(Message::class)) {
        class Message
        {
        }
    }
}

namespace Grpc {
    if (!class_exists(BaseStub::class)) {
        class BaseStub
        {
            public function __construct(public string $hostname, public array $opts, public mixed $channel = null)
            {
            }
        }
    }

    if (!class_exists(ChannelCredentials::class)) {
        class ChannelCredentials
        {
            public static function createSsl(): string
            {
                return 'ssl';
            }

            public static function createInsecure(): string
            {
                return 'insecure';
            }
        }
    }

    if (!defined('Grpc\\STATUS_OK')) {
        define('Grpc\\STATUS_OK', 0);
    }
}

namespace {
    $sourceFiles = [
        __DIR__ . '/../src/Dto/Config.php',
        __DIR__ . '/../src/Dto/Policy.php',
        __DIR__ . '/../src/Exception/AccessDeniedException.php',
        __DIR__ . '/../src/Exception/PolicyAddException.php',
        __DIR__ . '/../src/Grpc/Uam/Api/ApiClient.php',
        __DIR__ . '/../src/Service/UAMService.php',
        __DIR__ . '/../src/Factory/EnforcerFactory.php',
        __DIR__ . '/../src/Factory/UamApiClientFactory.php',
        __DIR__ . '/../src/Service/Enforcer.php',
    ];

    foreach ($sourceFiles as $sourceFile) {
        require_once $sourceFile;
    }
}


