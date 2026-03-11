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

final class StaticFileRenderTemplate implements RenderTemplate
{
    public function __construct(private $get_contents = 'file_get_contents')
    {
    }

    public function renderTemplate(Template $template, ?RenderTemplate $rt = null)
    {
        $path = $template->get('path');

        return ($this->get_contents)($path);
    }
}
