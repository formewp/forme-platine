<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\Folders;

use Forme\Platine\Extension;
use Forme\Platine\Engine;

final class FoldersExtension implements Extension
{
    public function register(Engine $plates): void
    {
        $c = $plates->getContainer();
        $c->add('folders.folders', []);
        $c->wrapStack('path.resolvePath', fn ($stack, $c): array => array_merge($stack, [
            'folders' => foldersResolvePath(
                $c->get('folders.folders'),
                $c->get('config')['folder_separator'],
                $c->get('fileExists')
            ),
        ]));
        $c->wrapComposed('path.normalizeName', fn ($composed, $c): array => array_merge($composed, [
            'folders.stripFolders' => stripFoldersNormalizeName($c->get('folders.folders')),
        ]));

        $plates->defineConfig([
            'folder_separator' => '::',
        ]);
        $plates->addMethods([
            'addFolder' => function ($plates, $folder, $prefixes, $fallback = false): void {
                $prefixes = is_string($prefixes) ? [$prefixes] : $prefixes;
                if ($fallback) {
                    $prefixes[] = '';
                }

                $plates->getContainer()->merge('folders.folders', [
                    $folder => [
                        'folder'   => $folder,
                        'prefixes' => $prefixes,
                    ],
                ]);
            },
        ]);
    }
}
