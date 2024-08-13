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

namespace Plenta\TooltipBundle\EventListener\Hooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Plenta\TooltipBundle\Models\TooltipModel;
use Symfony\Component\Asset\Packages;

class InsertTagListener
{
    protected Packages $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @Hook("replaceInsertTags")
     */
    public function onReplaceInsertTags(string $insertTag)
    {
        $chunks = explode('::', $insertTag);

        if ('plenta_tooltip' === $chunks[0]) {
            $tooltip = TooltipModel::findByIdOrAlias($chunks[1]);

            if ($tooltip && $tooltip->published && $tooltip->getContentElements()) {
                $this->includeAssets();

                return '<span class="plenta-tooltip'.($tooltip->text ? ' no-asterisk' : '').'" data-id="'.$tooltip->id.'">'.$tooltip->text.'</span>';
            }

            return '';
        }

        return false;
    }

    public function includeAssets(): void
    {
        if (false === isset($GLOBALS['TL_BODY']['plenta_tooltip_js'])) {
            $GLOBALS['TL_BODY']['plenta_tooltip_js'] = '<script src="'.$this->packages->getUrl('plentatooltip/layout.js', 'plentatooltip').'" defer ></script>';
        }

        if (false === isset($GLOBALS['TL_CSS']['plenta_tooltip'])) {
            $GLOBALS['TL_CSS']['plenta_tooltip'] = $this->packages->getUrl('plentatooltip/layout.css', 'plentatooltip');
        }
    }
}
