<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\RenderTemplate;

use Forme\Platine\RenderTemplate;
use Forme\Platine\Template;

abstract class RenderTemplateDecorator implements RenderTemplate
{
    public function __construct(protected RenderTemplate $render)
    {
    }

    abstract public function renderTemplate(Template $template, ?RenderTemplate $rt = null);
}
