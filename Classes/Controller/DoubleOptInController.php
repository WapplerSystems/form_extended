<?php
namespace WapplerSystems\FormExtended\Controller;

/*
 * DoubleOptInController
 */

use WapplerSystems\FormExtended\Domain\Model\OptIn;

class DoubleOptInController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * optInRepository
     *
     * @var \WapplerSystems\FormExtended\Domain\Repository\OptInRepository
     */
    protected $optInRepository = NULL;

    /**
     * signalSlotDispatcher
     *
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher = NULL;


    /**
     * @param \WapplerSystems\FormExtended\Domain\Repository\OptInRepository $optInRepository
     */
    public function injectOptInRepository(\WapplerSystems\FormExtended\Domain\Repository\OptInRepository $optInRepository) {
        $this->optInRepository = $optInRepository;
    }


    /**
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher) {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * action validation
     *
     * @param string $hash
     * @return void
     */
    public function validationAction($hash = '')
    {
        if ($hash !== '') {
            /** @var OptIn $optIn */
            $optIn = $this->optInRepository->findOneByValidationHash($hash);

            if ($optIn) {

                if ($optIn->getIsValidated()) {
                    $this->view->assign('alreadyConfirmed', true);
                    return;
                }

                $optIn->setIsValidated(TRUE);
                $optIn->setValidationDate(new \DateTime);
                $this->optInRepository->update($optIn);

                $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInValidation', [$optIn]);

                if (isset($this->settings['forward']) && (int)$this->settings['forward'] > 0) {
                    $url = $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setTargetPageUid($this->settings['forward'])->build();
                    $this->redirectToUri($url);
                }

                $this->view->assign('success', true);
                return;
            }
        }

        $this->view->assign('notFound', true);
    }
}
