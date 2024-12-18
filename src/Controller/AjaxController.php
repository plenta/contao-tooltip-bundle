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

namespace Plenta\TooltipBundle\Controller;

use Contao\Controller;
use Contao\StringUtil;
use Plenta\TooltipBundle\Models\TooltipModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("_plenta")
 */
class AjaxController extends AbstractController
{
    /**
     * @Route("/tooltip/{id}")
     *
     * @param mixed $id
     */
    public function getTooltip($id, TranslatorInterface $translator, array $plentaTooltipSizes): JsonResponse
    {
        $tooltip = TooltipModel::findByIdOrAlias($id);
        $buffer = '';

        foreach ($tooltip->getContentElements() as $contentElement) {
            $buffer .= Controller::getContentElement($contentElement->id);
        }

        $classes = explode(' ', $tooltip->cssClass);

        if ($tooltip->size && ($size = $plentaTooltipSizes[$tooltip->size] ?? null)) {
            $sizeClasses = explode(' ', ($size['cssClass'] ?? ''));
            $classes = array_merge($classes, $sizeClasses);
        }

        $classes = array_filter($classes);

        return new JsonResponse([
            'buffer' => '<!-- indexer::stop -->'.$buffer.'<!-- indexer::continue -->',
            'buttonText' => $translator->trans(
                'MSC.close',
                [],
                'contao_default'
            ),
            'classes' => $classes,
        ]);
    }
}
