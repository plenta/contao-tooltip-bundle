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

use Contao\Input;
use Plenta\TooltipBundle\Models\TooltipModel;

if ('plenta_tooltip' === Input::get('do')) {
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = TooltipModel::getTable();
}
