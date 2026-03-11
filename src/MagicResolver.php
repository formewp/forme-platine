<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

declare(strict_types=1);

namespace Forme\Platine;

final class MagicResolver
{
    public static function resolve(string $name): string
    {
        $name     = str_replace('.', '/', $name);

        if (str_ends_with($name, '/')) {
            $name .= 'index';
        }

        return $name;
    }
}
