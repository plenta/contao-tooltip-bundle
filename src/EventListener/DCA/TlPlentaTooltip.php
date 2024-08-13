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

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\Slug\Slug;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;
use Plenta\TooltipBundle\Models\TooltipModel;

class TlPlentaTooltip
{
    protected Slug $slug;
    protected array $plentaTooltipSizes;

    public function __construct(Slug $slug, array $plentaTooltipSizes)
    {
        $this->slug = $slug;
        $this->plentaTooltipSizes = $plentaTooltipSizes;
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="fields.alias.save")
     */
    public function onAliasSave(string $varValue, DataContainer $dc)
    {
        $doesAliasExist = fn ($alias) => (bool) TooltipModel::countBy(['alias = ?', 'id != ?'], [$alias, $dc->id]);

        if (empty($varValue)) {
            $varValue = $this->slug->generate(
                $dc->activeRecord->title,
                [],
                $doesAliasExist
            );
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($doesAliasExist($varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="list.operations.copyInsertTag.button")
     */
    public function onCopyInsertTagButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        ?string $icon,
        string $attributes
    ): string {
        if ('folder' === $row['type']) {
            return Image::getHtml('bundles/plentatooltip/img/share_.svg', $label).' ';
        }

        $attributes .= ' data-insert-tag="{{plenta_tooltip::'.$row['alias'].'}}"';

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="list.operations.edit.button")
     */
    public function onEditButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        ?string $icon,
        string $attributes
    ): string {
        if ('folder' === $row['type']) {
            $icon = 'edit_.svg';

            return Image::getHtml($icon, $label).' ';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="list.operations.copyChilds.button")
     */
    public function onCopyChildsButton(
        array $row,
        ?string $href,
        string $label,
        string $title,
        ?string $icon,
        string $attributes
    ): string {
        if ('folder' !== $row['type']) {
            $icon = 'copychilds_.svg';

            return Image::getHtml($icon, $label).' ';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="list.operations.toggle.button")
     *
     * @param mixed $row
     * @param mixed $href
     * @param mixed $label
     * @param mixed $title
     * @param mixed $icon
     * @param mixed $attributes
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if ('folder' === $row['type']) {
            return Image::getHtml($icon, $label).' ';
        }

        if (Input::get('tid')) {
            $this->toggleVisibility(Input::get('tid'), 1 == Input::get('state'), \func_num_args() <= 12 ? null : func_get_arg(12));
            Backend::redirect(Backend::getReferer());
        }

        $user = BackendUser::getInstance();

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$user->hasAccess('tl_plenta_tooltip::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.Backend::addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    /**
     * Disable/enable a tooltip.
     *
     * @param mixed $intId
     */
    public function toggleVisibility($intId, bool $blnVisible, DataContainer $dc = null): void
    {
        $user = BackendUser::getInstance();

        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onload_callback']) && \is_array($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onload_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onload_callback'] as $callback) {
                if (\is_array($callback)) {
                    $obj = System::importStatic($callback[0]);
                    $obj->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$user->hasAccess('tl_plenta_tooltip::published', 'alexf')) {
            throw new AccessDeniedException('Not enough permissions to publish/unpublish tooltip ID "'.$intId.'".');
        }

        $objRow = Database::getInstance()->prepare('SELECT * FROM tl_plenta_tooltip WHERE id=?')
            ->limit(1)
            ->execute($intId);

        if ($objRow->numRows < 1) {
            throw new AccessDeniedException('Invalid tooltip ID "'.$intId.'".');
        }

        // Set the current record
        if ($dc) {
            $dc->activeRecord = $objRow;
        }

        $objVersions = new Versions('tl_plenta_tooltip', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['save_callback']) && \is_array($GLOBALS['TL_DCA']['tl_plenta_tooltip']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_tooltip']['fields']['published']['save_callback'] as $callback) {
                if (\is_array($callback)) {
                    $obj = System::importStatic($callback[0]);
                    $blnVisible = $obj->{$callback[1]}($blnVisible, $dc);
                } elseif (\is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        Database::getInstance()->prepare("UPDATE tl_plenta_tooltip SET tstamp=$time, published='".($blnVisible ? '1' : '0')."' WHERE id=?")
            ->execute($intId);

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onsubmit_callback']) && \is_array($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onsubmit_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_tooltip']['config']['onsubmit_callback'] as $callback) {
                if (\is_array($callback)) {
                    $obj = System::importStatic($callback[0]);
                    $obj->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();

        if ($dc) {
            $dc->invalidateCacheTags();
        }
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="list.label.label")
     *
     * @param mixed $row
     * @param mixed $label
     */
    public function addIcon($row, $label)
    {
        $image = 'folder' === $row['type'] ? 'folderC' : 'articles';

        if ('content' === $row['type'] && !$row['published']) {
            $image .= '_1';
        }

        $attributes = sprintf(
            'data-icon="%s" data-icon-disabled="%s"',
            Image::getPath(str_replace('_1', '', $image)),
            Image::getPath(str_replace('_1', '', $image).('content' === $row['type'] ? '_1' : ''))
        );

        return Image::getHtml($image.'.svg', '', $attributes).' '.$label;
    }

    /**
     * @Callback(table="tl_plenta_tooltip", target="fields.size.options")
     */
    public function getSizeOptions()
    {
        return array_keys($this->plentaTooltipSizes);
    }
}
