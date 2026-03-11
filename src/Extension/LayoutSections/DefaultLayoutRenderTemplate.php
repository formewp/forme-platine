<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\LayoutSections;

use Forme\Platine\Extension\LayoutSections\Plates\RenderTemplate\RenderTemplateDecorator;
use Forme\Platine\Extension\LayoutSections\Plates\RenderTemplate;
use Forme\Platine\Extension\LayoutSections\Plates\Template;

final class DefaultLayoutRenderTemplate extends RenderTemplateDecorator
{
    public $render;
    public function __construct(RenderTemplate $render, private $layout_path)
    {
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        if ($template->parent || $template->get('no_layout')) {
            return $this->render->renderTemplate($template, $rt ?: $this);
        }

        $ref      = $template->reference;
        $contents = $this->render->renderTemplate($template, $rt ?: $this);

        if ($ref()->get('layout')) {
            return $contents;
        }

        $layout = $ref()->fork($this->layout_path);
        $ref()->with('layout', $layout->reference);

        return $contents;
    }

    public static function factory($layout_path)
    {
        return fn (RenderTemplate $rt): DefaultLayoutRenderTemplate => new self($rt, $layout_path);
    }
}
