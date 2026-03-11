<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine;

/** Template value object */
final class Template
{
    public $reference;

    public function __construct(
        public string $name,
        public array $data = [],
        public array $attributes = [],
        ?TemplateReference $ref = null,
        public ?TemplateReference $parent = null,
    ) {
        $this->reference = ($ref ?: new TemplateReference())->update($this);
    }

    public function with($key, $value)
    {
        return $this->withAddedAttributes([$key => $value]);
    }

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->attributes)
            ? $this->attributes[$key]
            : $default;
    }

    /** Returns the deferenced parent template */
    public function parent()
    {
        return $this->parent instanceof TemplateReference ? ($this->parent)() : null;
    }

    public function withName($name): self
    {
        return new self($name, $this->data, $this->attributes, $this->reference, $this->parent);
    }

    public function withData(array $data): self
    {
        return new self($this->name, $data, $this->attributes, $this->reference, $this->parent);
    }

    public function withAddedData(array $data): self
    {
        return new self($this->name, array_merge($this->data, $data), $this->attributes, $this->reference, $this->parent);
    }

    public function withAttributes(array $attributes): self
    {
        return new self($this->name, $this->data, $attributes, $this->reference, $this->parent);
    }

    public function withAddedAttributes(array $attributes): self
    {
        return new self($this->name, $this->data, array_merge($this->attributes, $attributes), $this->reference, $this->parent);
    }

    public function __clone()
    {
        $this->reference = new TemplateReference();
    }

    /** Create a new template based off of this current one */
    public function fork($name, array $data = [], array $attributes = []): self
    {
        return new self(
            $name,
            $data,
            $attributes,
            null,
            $this->reference
        );
    }
}
