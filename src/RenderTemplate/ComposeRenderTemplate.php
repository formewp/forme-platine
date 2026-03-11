<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\RenderTemplate;

use Forme\Platine\RenderTemplate\Plates\RenderTemplate;
use Forme\Platine\RenderTemplate\Plates\Template;

final class ComposeRenderTemplate extends RenderTemplateDecorator
{
    private $compose;

    public function __construct(RenderTemplate $render, callable $compose)
    {
        parent::__construct($render);
        $this->compose = $compose;
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        return $this->render->renderTemplate(($this->compose)($template), $rt ?: $this);
    }
}
