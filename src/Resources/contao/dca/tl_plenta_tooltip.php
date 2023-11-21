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

$GLOBALS['TL_DCA']['tl_plenta_tooltip'] = [
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'ctable' => [
            'tl_content',
        ],
        'label' => &$GLOBALS['TL_LANG']['MOD']['tooltip'][0],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 5,
            'flag' => 11,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_content',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
            ],
            'copyChilds' => [
                'href' => 'act=paste&amp;mode=copy&amp;childs=1',
                'icon' => 'copychilds.svg',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if (!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\')) return false; Backend.getScrollOffset();"',
            ],
            'copyInsertTag' => [
                'icon' => '/bundles/plentatooltip/img/share.svg',
                'attributes' => 'onclick="navigator.clipboard.writeText(this.getAttribute(\'data-insert-tag\')); alert(this.getAttribute(\'data-insert-tag\') + \' wurde in die Zwischenablage kopiert.\'); return false;"',
            ],
            'toggle' => [
                'href' => null,
                'icon' => 'visible.svg',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['tl_plenta_tooltip', 'toggleIcon'],
                'showInHeader' => true,
            ],
        ],
    ],
    'palettes' => [
        '__selector__' => ['type'],
        'content' => '{title_legend},title,alias,type;{published_legend},published',
        'folder' => '{title_legend},title,type',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'sorting' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'pid' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'published' => [
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default ''",
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
                'doNotCopy' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'type' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['folder', 'content'],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_tooltip']['typeRef'],
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'mode' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['modal', 'tooltip'],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_tooltip']['modeRef'],
            'sql' => "varchar(16) NOT NULL default 'modal'",
        ],
    ],
];
