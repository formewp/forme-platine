<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\LayoutSections;

use Forme\Platine\RenderTemplate\RenderTemplateDecorator;
use Forme\Platine\Template;
use Forme\Platine\RenderTemplate;

final class LayoutRenderTemplate extends RenderTemplateDecorator
{
    public $render;
    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        $ref     = $template->reference;
        $content = $this->render->renderTemplate($template, $rt ?: $this);

        $layout_ref = $ref()->get('layout');
        if (!$layout_ref) {
            return $content;
        }

        $layout = $layout_ref()->with('sections', $ref()->get('sections'));
        $layout->get('sections')->add('content', $content);

        return ($rt ?: $this)->renderTemplate($layout);
    }

    public static function factory()
    {
        return fn (RenderTemplate $render): LayoutRenderTemplate => new self($render);
    }
}
