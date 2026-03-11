<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine;

/** mutable template reference to allow relationships */
final class TemplateReference
{
    public $template;

    public function __invoke()
    {
        return $this->template;
    }

    public function update(Template $template): self
    {
        $this->template = $template;

        return $this;
    }
}
