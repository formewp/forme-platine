<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\LayoutSections;

use Forme\Platine\Extension\LayoutSections\Plates\Extension;
use Forme\Platine\Extension\LayoutSections\Plates\Engine;
use function Forme\Platine\Extension\RenderContext\assertTemplateArgsFunc;
use function Forme\Platine\Extension\RenderContext\assertArgsFunc;
final class LayoutSectionsExtension implements Extension
{
    public function register(Engine $plates): void
    {
        $c = $plates->getContainer();

        $c->wrap('renderTemplate.factories', function ($factories, $c) {
            $default_layout_path = $c->get('config')['default_layout_path'];
            if ($default_layout_path) {
                $factories[] = DefaultLayoutRenderTemplate::factory($default_layout_path);
            }

            $factories[] = LayoutRenderTemplate::factory();

            return $factories;
        });

        $plates->defineConfig(['default_layout_path' => null]);
        $plates->pushComposers(fn ($c): array => ['layoutSections.sections' => sectionsCompose()]);
        $plates->addFuncs(function ($c): array {
            $template_args = assertTemplateArgsFunc();
            $one_arg       = assertArgsFunc(1);

            return [
                'layout'  => [layoutFunc(), $template_args],
                'section' => [sectionFunc(), assertArgsFunc(1, 1)],
                'start'   => [startFunc(), $one_arg],
                'push'    => [startFunc(START_APPEND), $one_arg],
                'unshift' => [startFunc(START_PREPEND), $one_arg],
            ];
        });
    }
}
