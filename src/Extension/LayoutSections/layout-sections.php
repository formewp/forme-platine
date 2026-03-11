<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\LayoutSections;

use Forme\Platine\TemplateReference;
use function Forme\Platine\Util\obWrap;
use Forme\Platine\Extension\RenderContext\FuncArgs;
use Forme\Platine\Template;
use function Forme\Platine\Extension\RenderContext\startBufferFunc;

function sectionsCompose()
{
    return fn (Template $template) => $template->with('sections', $template->parent instanceof TemplateReference ? $template->parent()->get('sections') : new Sections());
}

function layoutFunc()
{
    return function (FuncArgs $args) {
        [$name, $data] = $args->args;

        $layout = $args->template()->fork($name, $data ?: []);
        $args->template()->with('layout', $layout->reference);

        return $layout;
    };
}

function sectionFunc()
{
    return function (FuncArgs $args) {
        [$name, $else] = $args->args;

        $res = $args->template()->get('sections')->get($name);
        if ($res || !$else) {
            return $res;
        }

        return is_callable($else)
            ? obWrap($else)
            : (string) $else;
    };
}

const START_APPEND  = 0;
const START_PREPEND = 1;
const START_REPLACE = 2;

/** Starts the output buffering for a section, update of 0 = replace, 1 = append, 2 = prepend */
function startFunc($update = START_REPLACE)
{
    return startBufferFunc(fn (FuncArgs $args): \Closure => function ($contents) use ($update, $args): void {
        $name     = $args->args[0];
        $sections = $args->template()->get('sections');

        if ($update === START_APPEND) {
            $sections->append($name, $contents);
        } elseif ($update === START_PREPEND) {
            $sections->prepend($name, $contents);
        } else {
            $sections->add($name, $contents);
        }
    });
}
