<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine;

use Forme\Platine\Util\Container;
use Forme\Platine\Extension\Data\DataExtension;
use Forme\Platine\Extension\Path\PathExtension;
use Forme\Platine\Extension\RenderContext\RenderContextExtension;
use Forme\Platine\Extension\LayoutSections\LayoutSectionsExtension;
use Forme\Platine\Extension\Folders\FoldersExtension;
use BadMethodCallException;

/** API for the Plates system. This wraps the container and allows extensions to add methods for syntactic sugar. */
final class Engine
{
    private $container;

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?: Container::create(['engine_methods' => []]);
    }

    /** Create a configured engine and set the base dir and extension optionally */
    public static function create($base_dir, $ext = null)
    {
        return self::createWithConfig(array_filter([
            'base_dir' => $base_dir,
            'ext'      => $ext,
        ]));
    }

    /** Create a configured engine and pass in an array to configure after extension registration */
    public static function createWithConfig(array $config = []): self
    {
        $platine = new self();

        $platine->register(new PlatineExtension());
        $platine->register(new DataExtension());
        $platine->register(new PathExtension());
        $platine->register(new RenderContextExtension());
        $platine->register(new LayoutSectionsExtension());
        $platine->register(new FoldersExtension());

        $platine->addConfig($config);

        return $platine;
    }

    public function render(string $template, array $data = [], array $attributes = []): string
    {
        $template = MagicResolver::resolve($template);

        return $this->container->get('renderTemplate')->renderTemplate(new Template(
            $template,
            $data,
            $attributes
        ));
    }

    public function addMethods(array $methods): void
    {
        $this->container->merge('engine_methods', $methods);
    }

    public function __call(string $method, array $args)
    {
        $methods = $this->container->get('engine_methods');
        if (isset($methods[$method])) {
            return $methods[$method]($this, ...$args);
        }

        throw new BadMethodCallException(sprintf('No method %s found for engine.', $method));
    }

    public function register(Extension $extension): void
    {
        $extension->register($this);
    }

    public function getContainer()
    {
        return $this->container;
    }
}
