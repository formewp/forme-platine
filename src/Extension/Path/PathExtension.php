<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\Path;

use Forme\Platine\Extension;
use Forme\Platine\Engine;

final class PathExtension implements Extension
{
    public function register(Engine $platine): void
    {
        $c = $platine->getContainer();
        $c->add('path.resolvePath.prefixes', fn ($c): array => (array) ($c->get('config')['base_dir'] ?? []));
        $c->addComposed('path.normalizeName', fn ($c): array => [
            'path.stripExt'    => stripExtNormalizeName(),
            'path.stripPrefix' => stripPrefixNormalizeName($c->get('path.resolvePath.prefixes')),
        ]);
        $c->addStack('path.resolvePath', function ($c): array {
            $config   = $c->get('config');
            $prefixes = $c->get('path.resolvePath.prefixes');

            return array_filter([
                'path.id'       => idResolvePath(),
                'path.prefix'   => $prefixes ? prefixResolvePath($prefixes, $c->get('fileExists')) : null,
                'path.ext'      => isset($config['ext']) ? extResolvePath($config['ext']) : null,
                'path.relative' => relativeResolvePath(),
            ]);
        });
        $platine->defineConfig([
            'ext'      => 'phtml',
            'base_dir' => null,
        ]);
        $platine->pushComposers(fn ($c): array => [
            'path.normalizeName' => normalizeNameCompose($c->get('path.normalizeName')),
            'path.resolvePath'   => resolvePathCompose($c->get('path.resolvePath')),
        ]);
    }
}
