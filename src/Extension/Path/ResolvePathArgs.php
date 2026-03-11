<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\Path;

use Forme\Platine\Template;

class ResolvePathArgs
{
    public function __construct(public $path, public array $context, public Template $template)
    {
    }

    public function withPath($path): self
    {
        return new self($path, $this->context, $this->template);
    }

    public function withContext(array $context): self
    {
        return new self($this->path, $context, $this->template);
    }

    public static function fromTemplate(Template $template): self
    {
        return new self($template->name, [], clone $template);
    }
}
