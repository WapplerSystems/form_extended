<?php

namespace WapplerSystems\FormExtended\Domain\Finishers;

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\View\TemplatePaths;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;

/**
 * Class CopyToSenderEmailFinisher
 *
 *
 * @package WapplerSystems\FormExtended\Domain\Finishers
 */
class CopyToSenderEmailFinisher extends EmailFinisher
{

    /**
     * Returns whether this finisher is enabled
     *
     * @return bool
     * @throws FinisherException
     */
    public function isEnabled(): bool
    {
        $conditionFieldName = $this->parseOption('conditionFieldName');
        if ($conditionFieldName === null) {
            throw new FinisherException('The option "conditionFieldName" must be set for the CopyToSenderEmailFinisher.', 1612660449);
        }
        if ((boolean)($conditionFieldName) === false) {
            return false;
        }
        return !isset($this->options['renderingOptions']['enabled']) || (bool)$this->parseOption('renderingOptions.enabled') === true;
    }


}
