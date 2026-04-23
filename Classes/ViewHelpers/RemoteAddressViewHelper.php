<?php

declare(strict_types=1);

namespace WapplerSystems\FormExtended\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the remote IP address of the current request.
 *
 * Uses TYPO3's GeneralUtility::getIndpEnv() which respects
 * trusted proxy configuration ($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP']).
 */
class RemoteAddressViewHelper extends AbstractViewHelper
{

    public function render(): string
    {
        return GeneralUtility::getIndpEnv('REMOTE_ADDR');
    }

}
