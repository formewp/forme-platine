<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\RenderContext;

use Forme\Platine\Extension\RenderContext\Plates\RenderTemplate;
use Forme\Platine\Extension\RenderContext\Plates\TemplateReference;

final class FuncArgs
{
    public function __construct(public RenderTemplate $render, public TemplateReference $ref, public $func_name, public $args = [])
    {
    }

    public function template()
    {
        return $this->ref->template;
    }

    public function withName($func_name): self
    {
        return new self($this->render, $this->ref, $func_name, $this->args);
    }

    public function withArgs($args): self
    {
        return new self($this->render, $this->ref, $this->func_name, $args);
    }
}
