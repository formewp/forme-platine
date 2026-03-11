<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\RenderContext;

use Forme\Platine\Extension;
use Forme\Platine\Engine;
use function Forme\Platine\Util\stackGroup;

/** The render context extension provides a RenderContext object and functions to be used within the render context object. This RenderContext object is injected into the template data to allow usefulness in the templates. */
final class RenderContextExtension implements Extension
{
    public function register(Engine $plates): void
    {
        $c = $plates->getContainer();
        $c->addStack('renderContext.func', fn ($c): array => [
            'notFound' => notFoundFunc(),
            'plates'   => stackGroup([
                splitByNameFunc($c->get('renderContext.func.funcs')),
                aliasNameFunc($c->get('renderContext.func.aliases')),
            ]),
        ]);
        $c->add('renderContext.func.aliases', [
            'e'        => 'escape',
            '__invoke' => 'escape',
            'stop'     => 'end',
        ]);
        $c->add('renderContext.func.funcs', function ($c): array {
            $template_args = assertTemplateArgsFunc();
            $one_arg       = assertArgsFunc(1);
            $config        = $c->get('config');

            return [
                'insert' => [insertFunc(), $template_args],
                'render' => [insertFunc(), $template_args],
                'escape' => [
                    isset($config['escape_flags'], $config['escape_encoding'])
                        ? escapeFunc($config['escape_flags'], $config['escape_encoding'])
                        : escapeFunc(),
                    $one_arg,
                ],
                'data'      => [templateDataFunc(), assertArgsFunc(0, 1)],
                'name'      => [accessTemplatePropFunc('name')],
                'context'   => [accessTemplatePropFunc('context')],
                'component' => [componentFunc(), $template_args],
                'slot'      => [slotFunc(), $one_arg],
                'end'       => [endFunc()],
            ];
        });
        $c->add('include.bind', fn ($c) => renderContextBind());
        $c->add('renderContext.factory', fn ($c) => RenderContext::factory(
            fn () => $c->get('renderTemplate'),
            $c->get('renderContext.func')
        ));

        $plates->defineConfig([
            'render_context_var_name' => 'v',
            'escape_encoding'         => null,
            'escape_flags'            => null,
        ]);
        $plates->pushComposers(fn ($c): array => [
            'renderContext.renderContext' => renderContextCompose(
                $c->get('renderContext.factory'),
                $c->get('config')['render_context_var_name']
            ),
        ]);

        $plates->addMethods([
            'registerFunction' => function (Engine $e, $name, callable $func, ?callable $assert_args = null, $simple = true): void {
                $c    = $e->getContainer();
                $func = $simple ? wrapSimpleFunc($func) : $func;

                $c->wrap('renderContext.func.funcs', function (array $funcs, $c) use ($name, $func, $assert_args): array {
                    $funcs[$name] = $assert_args ? [$assert_args, $func] : [$func];

                    return $funcs;
                });
            },
            'addFuncs' => function (Engine $e, callable $add_funcs, $simple = false): void {
                $e->getContainer()->wrap('renderContext.func.funcs', function ($funcs, $c) use ($add_funcs, $simple): array {
                    $new_funcs = $simple
                        ? array_map(wrapSimpleFunc::class, $add_funcs($c))
                        : $add_funcs($c);

                    return array_merge($funcs, $new_funcs);
                });
            },
            'wrapFuncs' => function (Engine $e, callable $wrap_funcs): void {
                $e->getContainer()->wrap('renderContext.func.funcs', $wrap_funcs);
            },
        ]);
    }
}
