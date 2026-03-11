<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\Data;

use Forme\Platine\TemplateReference;
use Forme\Platine\Template;

function addGlobalsCompose(array $globals)
{
    return fn (Template $template) => $template->withData(array_merge($globals, $template->data));
}

function mergeParentDataCompose()
{
    return fn (Template $template) => $template->parent instanceof TemplateReference
        ? $template->withData(array_merge($template->parent()->data, $template->data))
        : $template;
}

function perTemplateDataCompose(array $template_data_map)
{
    return function (Template $template) use ($template_data_map) {
        $name = $template->get('normalized_name', $template->name);

        return isset($template_data_map[$name])
            ? $template->withData(array_merge($template_data_map[$name], $template->data))
            : $template;
    };
}
