<?php
namespace TYPO3\CMS\Fluid\ViewHelpers\Be;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * View helper which returns the current page path as known from TYPO3 backend modules
 * Note: This view helper is experimental!
 *
 * = Examples =
 *
 * <code>
 * <f:be.pagePath />
 * </code>
 * <output>
 * Current page path, prefixed with "Path:" and wrapped in a span with the class "typo3-docheader-pagePath"
 * </output>
 */
class PagePathViewHelper extends AbstractBackendViewHelper
{
    /**
     * Renders the current page path
     *
     * @return string the rendered page path
     * @see \TYPO3\CMS\Backend\Template\DocumentTemplate::getPagePath() Note: can't call this method as it's protected!
     */
    public function render()
    {
        return static::renderStatic(
            array(),
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param callable $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $id = GeneralUtility::_GP('id');
        $pageRecord = BackendUtility::readPageAccess($id, $GLOBALS['BE_USER']->getPagePermsClause(1));
        // Is this a real page
        if ($pageRecord['uid']) {
            $title = $pageRecord['_thePathFull'];
        } else {
            $title = $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
        }
        // Setting the path of the page
        $pagePath = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xlf:labels.path', true) . ': <span class="typo3-docheader-pagePath">';
        // crop the title to title limit (or 50, if not defined)
        $cropLength = empty($GLOBALS['BE_USER']->uc['titleLen']) ? 50 : $GLOBALS['BE_USER']->uc['titleLen'];
        $croppedTitle = GeneralUtility::fixed_lgd_cs($title, -$cropLength);
        if ($croppedTitle !== $title) {
            $pagePath .= '<abbr title="' . htmlspecialchars($title) . '">' . htmlspecialchars($croppedTitle) . '</abbr>';
        } else {
            $pagePath .= htmlspecialchars($title);
        }
        $pagePath .= '</span>';
        return $pagePath;
    }
}
