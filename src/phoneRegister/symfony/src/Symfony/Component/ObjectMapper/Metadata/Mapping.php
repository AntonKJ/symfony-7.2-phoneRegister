<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ObjectMapper\Metadata;

/**
 * Configures a class or a property to map to.
 *
 * @internal
 *
 * @author Antoine Bluchet <soyuka@gmail.com>
 */
readonly class Mapping
{
    /**
     * @param string|class-string|null                                                               $source    The property or the class to map from
     * @param string|class-string|null                                                               $target    The property or the class to map to
     * @param string|bool|callable(mixed, object): bool|null                                         $if        A boolean, Symfony service name or a callable that instructs whether to map
     * @param (string|callable(mixed, object): mixed)|(string|callable(mixed, object): mixed)[]|null $transform A service id or a callable that transform the value during mapping
     */
    public function __construct(
        public ?string $target = null,
        public ?string $source = null,
        public mixed $if = null,
        public mixed $transform = null,
    ) {
    }
}
