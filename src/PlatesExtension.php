<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine;

use Forme\Platine\RenderTemplate\ComposeRenderTemplate;
use Forme\Platine\RenderTemplate\FileSystemRenderTemplate;
use Forme\Platine\RenderTemplate\PhpRenderTemplate;
use Forme\Platine\RenderTemplate\MapContentRenderTemplate;
use Forme\Platine\RenderTemplate\StaticFileRenderTemplate;
use Forme\Platine\RenderTemplate\ValidatePathRenderTemplate;
use function Forme\Platine\Template\matchExtensions;
use function Forme\Platine\Template\matchStub;
use function Forme\Platine\Util\id;

final class PlatesExtension implements Extension
{
    public function register(Engine $plates): void
    {
        $c = $plates->getContainer();

        $c->add('config', [
            'validate_paths'   => true,
            'php_extensions'   => ['php', 'phtml'],
            'image_extensions' => ['png', 'jpg'],
        ]);
        $c->addComposed('compose', fn (): array => []);
        $c->add('fileExists', fn ($c): string => 'file_exists');
        $c->add('renderTemplate', function ($c): ComposeRenderTemplate {
            $rt = new FileSystemRenderTemplate([
                [
                    matchExtensions($c->get('config')['php_extensions']),
                    new PhpRenderTemplate($c->get('renderTemplate.bind')),
                ],
                [
                    matchExtensions($c->get('config')['image_extensions']),
                    MapContentRenderTemplate::base64Encode(new StaticFileRenderTemplate()),
                ],
                [
                    matchStub(true),
                    new StaticFileRenderTemplate(),
                ],
            ]);
            if ($c->get('config')['validate_paths']) {
                $rt = new ValidatePathRenderTemplate($rt, $c->get('fileExists'));
            }

            $rt = array_reduce($c->get('renderTemplate.factories'), fn ($rt, $create) => $create($rt), $rt);

            return new ComposeRenderTemplate($rt, $c->get('compose'));
        });
        $c->add('renderTemplate.bind', fn () => id());
        $c->add('renderTemplate.factories', fn (): array => []);

        $plates->addMethods([
            'pushComposers' => function (Engine $e, $def_composer): void {
                $e->getContainer()->wrapComposed('compose', fn ($composed, $c): array => array_merge($composed, $def_composer($c)));
            },
            'unshiftComposers' => function (Engine $e, $def_composer): void {
                $e->getContainer()->wrapComposed('compose', fn ($composed, $c): array => array_merge($def_composer($c), $composed));
            },
            'addConfig' => function (Engine $e, array $config): void {
                $e->getContainer()->merge('config', $config);
            },
            /* merges in config values, but will defer to values already set in the config */
            'defineConfig' => function (Engine $e, array $config_def): void {
                $config = $e->getContainer()->get('config');
                $e->getContainer()->add('config', array_merge($config_def, $config));
            },
        ]);
    }
}
