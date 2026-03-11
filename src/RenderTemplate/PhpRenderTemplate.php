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
use function Forme\Platine\Util\obWrap;
use Forme\Platine\Validator;

final class PhpRenderTemplate implements RenderTemplate
{
    private $bind;

    public function __construct(?callable $bind = null)
    {
        $this->bind = $bind;
    }

    public function renderTemplate(Template $template, ?RenderTemplate $render = null)
    {
        if (!defined('FORME_PLATES_VALIDATION') || FORME_PLATES_VALIDATION) {
            Validator::validate($template);
        }
        $inc = $this->createInclude();
        $inc = $this->bind ? ($this->bind)($inc, $template) : $inc;

        return obWrap(function () use ($inc, $template): void {
            $inc($template->get('path'), $template->data);
        });
    }

    private function createInclude()
    {
        return function (): void {
            $funcGetArg = func_get_arg(1);
            extract($funcGetArg);
            include func_get_arg(0);
        };
    }
}
