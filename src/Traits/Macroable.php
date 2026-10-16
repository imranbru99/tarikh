<?php

declare(strict_types=1);

namespace Tarikh\Traits;

use Closure;
use BadMethodCallException;
use ReflectionClass;
use ReflectionMethod;

trait Macroable
{
    /**
     * @var array<string, callable>
     */
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void
    {
        self::$macros[$name] = $macro;
    }

    public static function mixin(object $mixin, bool $replace = true): void
    {
        $methods = (new ReflectionClass($mixin))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);

        foreach ($methods as $method) {
            if ($replace || !self::hasMacro($method->name)) {
                $method->setAccessible(true);
                $macro = $method->invoke($mixin);
                if (is_callable($macro)) {
                    self::macro($method->name, $macro);
                }
            }
        }
    }

    public static function hasMacro(string $name): bool
    {
        return isset(self::$macros[$name]);
    }

    /**
     * @param array<mixed> $parameters
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        if (!self::hasMacro($method)) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }

        $macro = self::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo(null, static::class);
        }

        return $macro !== null ? $macro(...$parameters) : null;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (!self::hasMacro($method)) {
            throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
        }

        $macro = self::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro !== null ? $macro(...$parameters) : null;
    }
}
