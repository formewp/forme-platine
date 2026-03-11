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
use Forme\Platine\RenderTemplate\Plates\Exception\RenderTemplateException;

final class ValidatePathRenderTemplate extends RenderTemplateDecorator
{
    public function __construct(RenderTemplate $render, private $file_exists = 'file_exists')
    {
        parent::__construct($render);
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        $path = $template->get('path');
        if (!$path || ($this->file_exists)($path)) {
            return $this->render->renderTemplate($template, $rt ?: null);
        }

        throw new RenderTemplateException('Template path ' . $path . ' is not a valid path for template: ' . $template->name);
    }
}
