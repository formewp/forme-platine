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

final class MockRenderTemplate implements RenderTemplate
{
    public function __construct(private array $mocks)
    {
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        if (!isset($this->mocks[$template->name])) {
            throw new RenderTemplateException('Mock include does not exist for name: ' . $template->name);
        }

        return $this->mocks[$template->name]($template);
    }
}
