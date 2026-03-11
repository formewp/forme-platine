<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Util;

use Closure;
use LogicException;

final class Container
{
    private array $boxes  = [];

    private array $cached = [];

    public static function create(array $defs): self
    {
        $c = new self();
        foreach ($defs as $key => $val) {
            $c->add($key, $val);
        }

        return $c;
    }

    public function add($id, $value): void
    {
        if (array_key_exists($id, $this->cached)) {
            throw new LogicException('Cannot add service after it has been frozen.');
        }

        $this->boxes[$id] = [$value, $value instanceof Closure];
    }

    public function addComposed(string $id, callable $define_composers): void
    {
        $this->add($id, fn ($c) => compose(...array_values($c->get($id . '.composers'))));
        $this->add($id . '.composers', $define_composers);
    }

    public function wrapComposed(string $id, callable $wrapped): void
    {
        $this->wrap($id . '.composers', $wrapped);
    }

    public function addStack(string $id, callable $define_stack): void
    {
        $this->add($id, fn ($c) => stack($c->get($id . '.stack')));
        $this->add($id . '.stack', $define_stack);
    }

    public function wrapStack(string $id, callable $wrapped): void
    {
        $this->wrap($id . '.stack', $wrapped);
    }

    public function merge($id, array $values): void
    {
        $old = $this->get($id);
        $this->add($id, array_merge($old, $values));
    }

    public function wrap(string $id, $wrapper): void
    {
        if (!$this->has($id)) {
            throw new LogicException('Cannot wrap service ' . $id . ' that does not exist.');
        }

        $box              = $this->boxes[$id];
        $this->boxes[$id] = [fn (Container $c) => $wrapper($this->unbox($box, $c), $c), true];
    }

    public function get(string $id)
    {
        if (array_key_exists($id, $this->cached)) {
            return $this->cached[$id];
        }

        if (!$this->has($id)) {
            throw new LogicException('Cannot retrieve service ' . $id . ' that does exist.');
        }

        $result = $this->unbox($this->boxes[$id], $this);
        if ($this->boxes[$id][1]) { // only cache services
            $this->cached[$id] = $result;
        }

        return $result;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->boxes);
    }

    private function unbox($box, Container $c)
    {
        [$value, $is_factory] = $box;
        if (!$is_factory) {
            return $value;
        }

        return $value($c);
    }
}
