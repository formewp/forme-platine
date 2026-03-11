<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Util;

use Psr\SimpleCache\CacheInterface;
use Exception;
use Forme\Platine\Exception\PlatineException;
use Forme\Platine\Exception\StackException;
use Throwable;

function id()
{
    return fn ($arg) => $arg;
}

/** wraps a closure in output buffering and returns the buffered
 * content. */
function obWrap(callable $wrap): string|false
{
    $cur_level = ob_get_level();

    try {
        ob_start();
        $wrap();

        return ob_get_clean();
    } catch (Exception|Throwable $e) {
    }

    // clean the ob stack
    while (ob_get_level() > $cur_level) {
        ob_end_clean();
    }

    throw $e;
}

/** simple utility that wraps php echo which allows for stubbing out the
 * echo func for testing */
function phpEcho()
{
    return function ($v): void {
        echo $v;
    };
}

/** stack a set of functions into each other and returns the stacked func */
function stack(array $funcs): \Closure
{
    return array_reduce($funcs, fn ($next, $func): \Closure => function (...$args) use ($next, $func) {
        $args[] = $next;

        return $func(...$args);
    }, function (): void {
        throw new StackException('No handler was able to return a result.');
    });
}

function stackGroup(array $funcs)
{
    $end_next = null;
    array_unshift($funcs, function (...$args) use (&$end_next) {
        return $end_next(...array_slice($args, 0, -1));
    });
    $next = stack($funcs);

    return function (...$args) use ($next, &$end_next) {
        $end_next = end($args);

        return $next(...array_slice($args, 0, -1));
    };
}

/** compose(f, g)(x) = f(g(x)) */
function compose(...$funcs)
{
    return pipe(...array_reverse($funcs));
}

/** pipe(f, g)(x) = g(f(x)) */
function pipe(...$funcs)
{
    return fn ($arg) => array_reduce($funcs, fn ($acc, $func) => $func($acc), $arg);
}

function joinPath(array $parts, $sep = DIRECTORY_SEPARATOR): ?string
{
    return array_reduce(array_filter($parts), function (string $acc, $part) use ($sep): string {
        if ($acc === null) {
            return rtrim($part, $sep);
        }

        return $acc . $sep . ltrim($part, $sep);
    });
}

function isAbsolutePath($path): bool
{
    return str_starts_with((string) $path, '/');
}

function isRelativePath($path): bool
{
    return str_starts_with((string) $path, './') || str_starts_with((string) $path, '../');
}

function isResourcePath($path): bool
{
    return str_contains((string) $path, '://');
}

function isPath($path): bool
{
    return isAbsolutePath($path) || isRelativePath($path) || isResourcePath($path);
}

/** returns the debug type of an object as string for exception printing */
function debugType($v): string
{
    if (is_object($v)) {
        return 'object ' . $v::class;
    }

    return gettype($v);
}

/**
 * @return mixed[]
 */
function spliceArrayAtKey(array $array, string $key, array $values, $after = true): array
{
    $new_array = [];
    $spliced   = false;
    foreach ($array as $array_key => $val) {
        if ($array_key == $key) {
            $spliced = true;
            if ($after) {
                $new_array[$array_key] = $val;
                $new_array             = array_merge($new_array, $values);
            } else {
                $new_array             = array_merge($new_array, $values);
                $new_array[$array_key] = $val;
            }
        } else {
            $new_array[$array_key] = $val;
        }
    }

    if (!$spliced) {
        throw new PlatineException('Could not find key ' . $key . ' in array.');
    }

    return $new_array;
}

function cachedFileExists(CacheInterface $cache, $ttl = 3600, $file_exists = 'file_exists')
{
    return function (string $path) use ($cache, $ttl, $file_exists) {
        $key = 'League.Plates.file_exists.' . $path;
        $res = $cache->get($key);
        if (!$res) {
            $res = $file_exists($path);
            $cache->set($key, $res, $ttl);
        }

        return $res;
    };
}

/** Invokes the callable if the predicate returns true. Returns the value otherwise. */
function when(callable $predicate, callable $fn)
{
    return fn ($arg) => $predicate($arg) ? $fn($arg) : $arg;
}
