<?php

declare(strict_types=1);

/**
 * Plenta Tooltip Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @license       LGPL
 * @link          https://github.com/plenta/
 */

/*
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes'][Plenta\TooltipBundle\Controller\OverlayController::TYPE] = '
  {title_legend},name,headline,type;
  {overlay_richtext_legend},overlay_richtext;
  {overlay_html_legend:hide},overlay_html;
  {overlay_settings_legend},overlay_loadingTime,overlay_cookie_expires;
  {template_legend:hide},customTpl;
  {expert_legend:hide},cssID
';

/*
 * Selectors
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'overlay_loadingTime';

/*
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['overlay_loadingTime_afterTime'] = 'overlay_delay';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['overlay_loadingTime_afterScroll'] = 'overlay_percent';

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_richtext'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_richtext'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_html'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_html'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['allowHtml' => true, 'class' => 'monospace', 'rte' => 'ace|html', 'helpwizard' => true],
    'explanation' => 'insertTags',
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_loadingTime'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_loadingTime'],
    'default' => 'onLeave',
    'exclude' => true,
    'inputType' => 'radio',
    'options' => ['onLeave', 'onLoad', 'afterTime', 'afterScroll'],
    'eval' => ['tl_class' => 'w50 autoheight', 'mandatory' => true, 'submitOnChange' => true],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'sql' => "varchar(20) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_delay'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_delay'],
    'default' => 120,
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'mandatory' => true, 'tl_class' => 'w50'],
    'sql' => "smallint(5) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_percent'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_percent'],
    'default' => 40,
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'mandatory' => true, 'tl_class' => 'w50', 'maxval' => '100'],
    'sql' => "smallint(3) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['overlay_cookie_expires'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['overlay_cookie_expires'],
    'default' => 30,
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'mandatory' => true, 'tl_class' => 'w50 clr'],
    'sql' => "smallint(5) unsigned NOT NULL default '0'",
];
