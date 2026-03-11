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
use Forme\Platine\Exception\RenderTemplateException;

final readonly class FileSystemRenderTemplate implements RenderTemplate
{
    public function __construct(private array $render_sets)
    {
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        foreach ($this->render_sets as [$match, $render]) {
            if ($match($template)) {
                return $render->renderTemplate($template, $rt ?: $this);
            }
        }

        throw new RenderTemplateException('No renderer was available for the template: ' . $template->name);
    }
}
