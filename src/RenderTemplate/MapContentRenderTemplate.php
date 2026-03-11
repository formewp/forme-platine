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

final class MapContentRenderTemplate extends RenderTemplateDecorator
{
    private $map_content;

    public function __construct(RenderTemplate $render, callable $map_content)
    {
        parent::__construct($render);
        $this->map_content = $map_content;
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        return ($this->map_content)($this->render->renderTemplate($template, $rt ?: $this));
    }

    public static function base64Encode(RenderTemplate $render): self
    {
        return new self($render, 'base64_encode');
    }
}
