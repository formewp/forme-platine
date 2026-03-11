<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\LayoutSections;

/** A simple store for managing section content. This needs to be a mutable object
 * so that references can be shared across templates. */
final class Sections
{
    public function __construct(private array $sections = [])
    {
    }

    public function add($name, $content): void
    {
        $this->sections[$name] = $content;
    }

    public function append($name, string $content): void
    {
        $this->sections[$name] = ($this->get($name) ?: '') . $content;
    }

    public function prepend($name, string $content): void
    {
        $this->sections[$name] = $content . ($this->get($name) ?: '');
    }

    public function clear($name): void
    {
        unset($this->sections[$name]);
    }

    public function get($name)
    {
        return $this->sections[$name] ?? null;
    }

    public function merge(Sections $sections): self
    {
        return new self(array_merge($this->sections, $sections->sections));
    }
}
