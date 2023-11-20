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

namespace Plenta\TooltipBundle\Models;

use Contao\ContentModel;
use Contao\Model;

class TooltipModel extends Model
{
    protected static $strTable = 'tl_plenta_tooltip';

    public function getContentElements()
    {
        return ContentModel::findPublishedByPidAndTable($this->id, self::$strTable);
    }
}
