<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\RenderContext;

use Forme\Platine\RenderTemplate;
use Forme\Platine\TemplateReference;
use function Forme\Platine\Util\stack;
use BadMethodCallException;

final class RenderContext
{
    private $func_stack;

    public function __construct(
        private readonly RenderTemplate $render,
        private readonly TemplateReference $ref,
        $func_stack = null,
    ) {
        $this->func_stack = $func_stack ?: stack([platesFunc()]);
    }

    public function __get(string $name): mixed
    {
        if (!$this->func_stack) {
            throw new BadMethodCallException('Cannot access ' . $name . ' because no func stack has been setup.');
        }

        return $this->invokeFuncStack($name, []);
    }

    public function __set(string $name, mixed $value)
    {
        throw new BadMethodCallException('Cannot set ' . $name . ' on this render context.');
    }

    public function __call(string $name, array $args)
    {
        if (!$this->func_stack) {
            throw new BadMethodCallException('Cannot call ' . $name . ' because no func stack has been setup.');
        }

        return $this->invokeFuncStack($name, $args);
    }

    public function __invoke(array $args = [])
    {
        if (!$this->func_stack) {
            throw new BadMethodCallException('Cannot invoke the render context because no func stack has been setup.');
        }

        return $this->invokeFuncStack('__invoke', $args);
    }

    private function invokeFuncStack(string $name, array $args)
    {
        return ($this->func_stack)(new FuncArgs(
            $this->render,
            $this->ref,
            $name,
            $args
        ));
    }

    public static function factory(callable $create_render, $func_stack = null)
    {
        return fn (TemplateReference $ref): RenderContext => new self(
            $create_render(),
            $ref,
            $func_stack
        );
    }
}
