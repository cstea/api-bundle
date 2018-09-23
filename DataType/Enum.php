<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\DataType;

abstract class Enum
{
    /**
     * @return int[]
     */
    private static function getAll(): array
    {
        try {
            $class = new \ReflectionClass(static::class);

            return $class->getConstants();
        } catch (\Throwable $exception) {
        }
        
        return [];
    }

    /**
     * @return int[]
     */
    public static function getValues(): array
    {
        return \array_values(static::getAll());
    }

    /**
     * @return string[]
     */
    public static function getConstants(): array
    {
        return \array_keys(static::getAll());
    }

    /**
     * @param string $constant Constant.
     * @return int
     */
    public static function getValue(string $constant): int
    {
        $values = static::getAll();
        $constant = \strtoupper($constant);
        
        return \array_key_exists($constant, $values) ? $values[$constant] : 0;
    }

    /**
     * @param string $value Value.
     * @return string
     */
    public static function getConstant(string $value): string
    {
        $constants = \array_flip(static::getAll());
        
        return \array_key_exists($value, $constants) ? $constants[$value] : '';
    }
}
