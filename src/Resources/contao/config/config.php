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

use Plenta\TooltipBundle\Models\TooltipModel;

$GLOBALS['BE_MOD']['content']['tooltip'] = [
    'tables' => [TooltipModel::getTable(), 'tl_content'],
];

$GLOBALS['TL_MODELS'][TooltipModel::getTable()] = TooltipModel::class;
