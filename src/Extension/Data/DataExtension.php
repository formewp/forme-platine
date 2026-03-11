<?php

/**
 * Originally based on Plates v4 alpha by RJ Garcia
 * @see https://github.com/thephpleague/plates
 *
 * Modified and maintained by Moussa Clarke
 * @license MIT
 */

namespace Forme\Platine\Extension\Data;

use Forme\Platine\Extension;
use Forme\Platine\Engine;

/** The DataExtension adds the ability to hydrate data into a template before it gets rendered. */
final class DataExtension implements Extension
{
    public function register(Engine $platine): void
    {
        $c = $platine->getContainer();
        $c->add('data.globals', []);
        $c->add('data.template_data', []);

        $platine->defineConfig(['merge_parent_data' => true]);
        $platine->pushComposers(fn ($c): array => array_filter([
            'data.addGlobals'      => $c->get('data.globals') ? addGlobalsCompose($c->get('data.globals')) : null,
            'data.mergeParentData' => $c->get('config')['merge_parent_data'] ? mergeParentDataCompose() : null,
            'data.perTemplateData' => $c->get('data.template_data') ? perTemplateDataCompose($c->get('data.template_data')) : null,
        ]));

        $platine->addMethods([
            'addGlobals' => function (Engine $e, array $data): void {
                $c = $e->getContainer();
                $c->merge('data.globals', $data);
            },
            'addGlobal' => function (Engine $e, $name, $value): void {
                $e->getContainer()->merge('data.globals', [$name => $value]);
            },
            'addData' => function (Engine $e, $data, $name = null) {
                if (!$name) {
                    return $e->addGlobals($data);
                }

                $template_data = $e->getContainer()->get('data.template_data');
                if (!isset($template_data[$name])) {
                    $template_data[$name] = [];
                }

                $template_data[$name] = array_merge($template_data[$name], $data);
                $e->getContainer()->add('data.template_data', $template_data);
            },
        ]);
    }
}
