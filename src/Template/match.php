<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Template;

use Forme\Platine\Template;

function matchPathExtension(callable $match)
{
    return matchAttribute('path', fn ($path) => $match(pathinfo((string) $path, PATHINFO_EXTENSION)));
}

function matchName($name)
{
    return fn (Template $template): bool => $template->get('normalized_name', $template->name) == $name;
}

function matchExtensions(array $extensions)
{
    return matchPathExtension(fn ($ext): bool => in_array($ext, $extensions));
}

function matchAttribute($attribute, callable $match)
{
    return fn (Template $template) => $match($template->get($attribute));
}

function matchStub($res)
{
    return fn (Template $template) => $res;
}

function matchAny(array $matches)
{
    return function (Template $template) use ($matches): bool {
        foreach ($matches as $match) {
            if ($match($template)) {
                return true;
            }
        }

        return false;
    };
}

function matchAll(array $matches)
{
    return function (Template $template) use ($matches): bool {
        foreach ($matches as $match) {
            if (!$match($template)) {
                return false;
            }
        }

        return true;
    };
}
