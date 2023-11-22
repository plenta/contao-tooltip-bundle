<?php

declare(strict_types=1);

/**
 * Plenta Tooltip Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @license       LGPL
 * @link          https://github.com/plenta/
 */

namespace Plenta\TooltipBundle\EventListener\DCA;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Input;
use Plenta\TooltipBundle\Models\TooltipModel;

class TlContent
{
    /**
     * @Callback(table="tl_content", target="config.onload")
     */
    public function onLoad(): void
    {
        if ('plenta_tooltip' !== Input::get('do')) {
            return;
        }

        $tooltip = TooltipModel::findByIdOrAlias(Input::get('id'));

        if ($tooltip && 'folder' === $tooltip->type) {
            throw new \Exception('Content elements are only allowed for tooltips of the type "content".');
        }
    }
}
