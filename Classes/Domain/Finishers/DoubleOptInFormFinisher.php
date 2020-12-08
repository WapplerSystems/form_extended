<?php
namespace WapplerSystems\FormExtended\Domain\Finishers;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Service\TranslationService;
use WapplerSystems\FormExtended\Domain\Model\OptIn;

class DoubleOptInFormFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    /**
     * optInRepository
     *
     * @var \WapplerSystems\FormExtended\Domain\Repository\OptInRepository
     * @inject
     */
    protected $optInRepository = NULL;

    /**
     * signalSlotDispatcher
     *
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher = NULL;

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal()
    {

        $formRuntime = $this->finisherContext->getFormRuntime();
        $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();

        $email = $formRuntime['email'] ?? null;

        $validationPid = $this->parseOption('validationPid');

        if (empty($email)) {
            throw new FinisherException('The options "email" must be set.', 1527145483);
        }

        if (empty($validationPid)) {
            throw new FinisherException('The option "validationPid" must be set.', 1527148282);
        }


        $standaloneView = $this->initializeStandaloneView($formRuntime);

        $optIn = new OptIn();
        $optIn->setEmail($email);
        //$optIn->setDecodedValues($this->finisherContext->getFormValues());


        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $storagePid = $configuration['plugin.']['tx_formextended_doubleoptin.']['persistence.']['storagePid'];
        $optIn->setPid($storagePid);

        $this->optInRepository->add($optIn);

        $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInCreation', [$optIn]);

        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();


        $standaloneView->assign('optIn', $optIn);
        $standaloneView->assign('validationPid', $validationPid);

        $translationService = TranslationService::getInstance();
        if (isset($this->options['translation']['language']) && !empty($this->options['translation']['language'])) {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }
        $message = $standaloneView->render();
        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        $subject = $this->parseOption('subject');
        $recipientAddress = $this->parseOption('recipientAddress');
        $recipientName = $this->parseOption('recipientName');
        $senderAddress = $this->parseOption('senderAddress');
        $senderName = $this->parseOption('senderName');
        $replyToAddress = $this->parseOption('replyToAddress');
        $carbonCopyAddress = $this->parseOption('carbonCopyAddress');
        $blindCarbonCopyAddress = $this->parseOption('blindCarbonCopyAddress');
        $format = $this->parseOption('format');

        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipientAddress)) {
            throw new FinisherException('The option "recipientAddress" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $mail = $this->objectManager->get(MailMessage::class);

        $mail->setFrom([$senderAddress => $senderName])
            ->setTo([$recipientAddress => $recipientName])
            ->setSubject($subject);

        if (!empty($replyToAddress)) {
            $mail->setReplyTo($replyToAddress);
        }

        if (!empty($carbonCopyAddress)) {
            $mail->setCc($carbonCopyAddress);
        }

        if (!empty($blindCarbonCopyAddress)) {
            $mail->setBcc($blindCarbonCopyAddress);
        }

        if ($format === self::FORMAT_PLAINTEXT) {
            $mail->setBody($message, 'text/plain');
        } else {
            $mail->setBody($message, 'text/html');
        }

        $mail->send();

        /* Cancel */
        //$this->finisherContext->cancel();
    }


}
