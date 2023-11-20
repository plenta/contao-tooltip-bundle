<?php

declare(strict_types=1);

/**
 * Plenta Tooltip Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @license       proprietary
 * @link          https://github.com/plenta/
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/** @var ContainerBuilder $container */
$fileSystem = new Filesystem();
$projectDir = $container->getParameter('kernel.project_dir');

if ($fileSystem->exists($projectDir.'/web')) {
    $webDir = 'web'; // backwards compatibility
} else {
    $webDir = 'public';
}

$container->loadFromExtension('framework', [
    'assets' => [
        'packages' => [
            'plentatooltip' => [
                'json_manifest_path' => '%kernel.project_dir%/'.$webDir.'/bundles/plentatooltip/webpack/manifest.json',
            ],
        ],
    ],
]);
