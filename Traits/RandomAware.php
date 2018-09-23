<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Nubs\RandomNameGenerator\Alliteration;

/**
 * Trait RandomAware
 * Provides a class with functions capable of generating random data.
 *
 * @package Cstea\ApiBundle\Traits
 */
trait RandomAware
{
    /**
     * Generate a random float number.
     *
     * @return float
     */
    protected function randomFloat(): float
    {
        return \mt_rand() / \mt_getrandmax();
    }

    /**
     * Generate a random integer.
     *
     * @param int $min Min value.
     * @param int $max Max value.
     * @return int
     */
    protected function randomNumber(int $min = 0, int $max = \PHP_INT_MAX): int
    {
        return \rand($min, $max);
    }

    /**
     * Generate a random hash string.
     *
     * @param int|null $maxLength Optional max length.
     * @return string
     */
    protected function randomString(?int $maxLength = null): string
    {
        $str = \md5((string) \rand() . (string) \microtime());

        return $maxLength ? \substr($str, 0, $maxLength) : $str;
    }

    /**
     * Generate a random name / text.
     *
     * @return string
     */
    protected function randomName(): string
    {
        return (new Alliteration())->getName();
    }
}
